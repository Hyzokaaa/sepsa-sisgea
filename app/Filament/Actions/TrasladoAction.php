<?php

namespace App\Filament\Actions;

use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Operacion;
use App\Models\OperacionDetalle;
use App\Models\Producto;
use App\Models\Ueb;
use App\Services\InventarioService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class TrasladoAction
{

    public static function make(): Action
    {
        $inventarioService = new InventarioService;

        return Action::make('transferencia')
            ->label('Transferir Productos')
            ->icon('heroicon-o-arrow-right')
            ->modalWidth('4xl')
            ->form(self::getFormSchema())
            ->action(function (array $data) use ($inventarioService) {
                self::handleTransfer($data, $inventarioService);
            });
    }
    protected InventarioService $inventarioService; // Nombre consistente



    private static function getFormSchema(): array
    {


        return [

            Forms\Components\DateTimePicker::make('fecha')
                ->required()
                ->default(now()),

            Select::make('almacen_id')
                ->label('Almacen')
                ->options(Almacen::query()
                    ->orderBy('nombre')
                    ->where('activo', true)
                    ->get()
                    ->mapWithKeys(function ($almacen) {
                        return [
                            $almacen->id => "{$almacen->id} - {$almacen->name}"
                        ];
                    }))
                ->required()
                ->searchable(),
            Select::make('origen_destino_tipo')
                ->label('Tipo de Destino')
                ->options([
                    'cliente'  => 'Cliente',
                    'ueb' => 'UEB',
                    'Almacen' => 'Almacen'
                ])
                ->reactive()
                ->live()
                ->required(),
            Select::make('origen_destino_id')
                ->label('Destino')
                ->options(function (callable $get) {
                    return match ($get('origen_destino_tipo')) {
                        'cliente' => Cliente::pluck('name', 'id'),
                        'ueb' => Ueb::pluck('name', 'id'),
                        'Almacen' => Almacen::pluck('nombre', 'id'),
                        default => [],
                    };
                })
                ->required()
                ->live()
                ->searchable(),

            Repeater::make('products')
                ->schema([
                    Select::make('producto_id')
                        ->label('Producto')
                        ->live()
                        ->options(function (callable $get) {
                            // Obtener el área seleccionada
                            $almacenId = $get('../../almacen_id');

                            $query = Producto::query();

                            if ($almacenId) {
                                // Obtener productos con inventario > 0 en el área
                                $query->join('inventarios', 'productos.id', '=', 'inventarios.producto_id')
                                    ->where('inventarios.almacen_id', $almacenId)
                                    ->where('inventarios.cantidad', '>', 0)
                                    ->select('productos.*')
                                    ->distinct();
                            }

                            return $query->get()
                                ->mapWithKeys(fn(Producto $producto) => [
                                    $producto->id => "{$producto->codigo} - \${$producto->nombre}"
                                ]);
                        })

                        ->afterStateUpdated(function ($set, $get, $state) {

                            $almacenId = $get('../../almacen_id');
                            $productoinv = Inventario::where('producto_id', $state)
                                ->where('almacen_id', $almacenId) // Filtra por almacén
                                ->first();

                            if ($productoinv) {
                                $set('precio_costo', $productoinv->precio_costo);
                                $set('precio_venta', $productoinv->precio_venta);
                            }
                        })
                        ->required(),
                    TextInput::make('cantidad')
                        ->numeric()
                        ->live(debounce: 500)
                        ->required()
                        ->afterStateUpdated(function ($set,  $get) {
                            self::actualizarImporte($set, $get);
                        })
                        ->columnSpan(1),
                    Hidden::make('precio_costo'),

                    TextInput::make('precio_venta')
                        ->required()
                        ->prefix('$')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($set, $get) {
                            self::actualizarImporte($set, $get);
                        })
                        ->columnSpan(1),

                    TextInput::make('importe')
                        ->label('Importe')
                        ->numeric()
                        ->live(debounce: 500)
                        ->readOnly()
                        ->prefix('$')
                        ->columnSpan(1),

                ])
                ->columns(4)
                ->live()
                ->deleteAction(function ($set, $get) {
                    self::calcularTotal($set, $get);
                })
                ->afterStateUpdated(function ($state,  $set,  $get) {
                    self::calcularTotal($set, $get);
                }),

            // Campo para el total general
            TextInput::make('importe_total')
                ->label('Total General')
                ->numeric()
                ->prefix('$'),
            TextInput::make('observacion')
                ->label('observaciones'),

        ];
    }

    protected static function actualizarImporte($set, $get): void
    {
        $cantidad = floatval($get('cantidad'));
        $precio = floatval($get('precio_venta'));

        $importe = $cantidad * $precio;

        $set('importe', number_format($importe, 2, '.', ''));
    }

    protected static function calcularTotal($set, $get): void
    {
        $productos = $get('products') ?? [];
        $total = 0;

        foreach ($productos as $producto) {
            $cantidad = floatval($producto['cantidad'] ?? 0);
            $precio = floatval($producto['precio_venta'] ?? 0);
            $total += $cantidad * $precio;
        }

        $set('importe_total', number_format($total, 2, '.', ''));
    }

    protected static function handleTransfer(array $data, InventarioService $inventarioService): void
    {

        try {
            \DB::transaction(function () use ($data, $inventarioService) {
                // Crear operaciones
                $outgoingOperation = Operacion::create([
                    'tipo_operacion' => 'traslado',
                    'origen_destino_tipo' => $data['origen_destino_tipo'],
                    'origen_destino_id' => $data['origen_destino_id'],
                    'observacion' => $data['observacion'],
                    'almacen_id' => $data['almacen_id'],
                    'importe' => $data['importe_total'],
                    //                    'executed_by' => auth()->id(),
                    'fecha' =>  $data['fecha'],
                    'cerrado' => false,
                ]);

                if (! $outgoingOperation) {
                    throw new \Exception('Error al crear operación de salida');
                }

                $incomingOperation = Operacion::create([
                    'tipo_operacion' => 'traslado',
                    'origen_destino_tipo' => $data['origen_destino_tipo'],
                    'origen_destino_id' => $data['almacen_id'],
                    'observacion' => $data['observacion'],
                    'almacen_id' => $data['origen_destino_id'],
                    'importe' => $data['importe_total'],
                    //                    'executed_by' => auth()->id(),
                    'fecha' => $data['fecha'],
                    'cerrado' => false,
                ]);

                if (! $incomingOperation) {
                    throw new \Exception('Error al crear operación de entrada');
                }



                foreach ($data['products'] as $product) {

                    // traslado salida
                    OperacionDetalle::create([
                        'operacion_id' => $outgoingOperation->id,
                        'producto_id' => $product['producto_id'],
                        'cantidad' => -$product['cantidad'], // Se descuenta del inventario
                        'precio_costo' => $product['precio_costo'],
                        'precio_venta' => $product['precio_venta'],
                    ]);

                    // traslado entrada
                    OperacionDetalle::create([
                        'operacion_id' => $incomingOperation->id,
                        'producto_id' => $product['producto_id'],
                        'cantidad' => $product['cantidad'], // Se descuenta del inventario
                        'precio_costo' => $product['precio_costo'],
                        'precio_venta' => $product['precio_venta'],
                    ]);

                    // Actualizar inventario de salida (puede lanzar excepción)
                    $inventarioService->updateOrCreateInventory(
                        $data['almacen_id'],
                        $product['producto_id'],
                        -$product['cantidad'],
                        $product['precio_costo'],
                        $product['precio_venta'],
                    );

                    // Actualizar inventario de entrada (puede lanzar excepción)
                    $inventarioService->updateOrCreateInventory(
                        $data['origen_destino_id'],
                        $product['producto_id'],
                        $product['cantidad'],
                        $product['precio_costo'],
                        $product['precio_venta'],
                    );
                }

                \Filament\Notifications\Notification::make()
                    ->title('Transferencia exitosa')
                    ->success()
                    ->send();
            });
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error al realizar la transferencia')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
