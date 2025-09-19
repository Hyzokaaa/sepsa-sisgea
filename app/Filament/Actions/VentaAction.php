<?php

namespace App\Filament\Actions;

use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Ueb;
use App\Models\Operacion;
use App\Models\OperacionDetalle;
use App\Models\Producto;
use App\Services\InventarioService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class VentaAction
{

   public static function make(): Action
    {
        $inventarioService = new InventarioService;

        return Action::make('venta')
            ->label('Registrar venta')
            ->icon('heroicon-o-currency-dollar')
            ->modalWidth('4xl')
            ->form(self::getFormSchema())
            ->action(function (array $data) use ($inventarioService) {
                self::handleVenta($data, $inventarioService);
            });
    }

      protected static function getFormSchema(): array
    {

        return [
            Select::make('almacen_id')
                ->label('Almacen')
                ->options(Almacen::query()
                    ->orderBy('nombre')
                    ->where('activo', true)
                    ->get()
                    ->mapWithKeys(function ($almacen) {
                        return [
                            $almacen->id => "{$almacen->id} - {$almacen->nombre}"
                        ];
                    }))
                ->required()
                ->searchable(),
            Forms\Components\DateTimePicker::make('fecha')
                ->required()
                ->default(now()),

            Select::make('origen_destino_tipo')
                ->label('Tipo de Destino')
                ->options([
                    'cliente'  => 'Cliente',
                    'ueb' => 'UEB',

                ])
                ->reactive()
                ->live()
                ->nullable(),

            Select::make('origen_destino_id')
                ->label('Destino')
                ->options(function (callable $get) {
                    return match ($get('origen_destino_tipo')) {
                        'cliente' => Cliente::pluck('name', 'id'),
                        'ueb' => Ueb::pluck('name', 'id'),
                        default => [],
                    };
                })
                ->nullable()
                ->searchable()
                ->live(),

            Repeater::make('products')
                ->label('Productos')
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
                                ->mapWithKeys(fn (Producto $producto) => [
                                    $producto->id => "{$producto->codigo} - \${$producto->name}"
                                ]);
                        })

                        ->afterStateUpdated(function ( $set, $get,$state) {

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
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ( $set, $get) {
                            self::actualizarImporte($set, $get);
                        }),

                    Hidden::make('precio_costo'),

                    TextInput::make('precio_venta')
                        ->required()
                        ->prefix('$')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ( $set,  $get) {
                            self::actualizarImporte($set, $get);
                        }),

                    TextInput::make('importe')
                        ->label('Importe')
                        ->readOnly()
                        ->numeric()
                        ->prefix('$'),
                ])
                ->columns(4)
                ->live(debounce: 500)
                ->reactive()
                ->deleteAction(fn ($set, $get) => self::calcularTotal($set, $get))
                ->afterStateUpdated(fn ($state,  $set,  $get) => self::calcularTotal($set, $get)),

            TextInput::make('importe_total')
                ->label('Total General')
                ->numeric()
                ->readOnly()
                ->prefix('$'),

            TextInput::make('observacion')
                ->label('observaciones'),

        ];
    }

    protected static function actualizarImporte( $set,  $get): void
    {
        $cantidad = floatval($get('cantidad'));
        $precio = floatval($get('precio_venta'));


        $importe = $cantidad * $precio;

        $set('importe', number_format($importe, 2, '.', ''));
    }

    protected static  function calcularTotal( $set, $get): void
    {
        $productos = $get('products') ?? [];
        $total = 0;
        $costo = 0;


        foreach ($productos as $producto) {
            $cantidad = floatval($producto['cantidad'] ?? 0);
            $precio = floatval($producto['precio_venta'] ?? 0);
            $precioC = floatval($producto['precio_costo'] ?? 0);
            $total += $cantidad * $precio;
            $costo += $cantidad * $precioC;
        }

      $set('importe_total', number_format($total, 2, '.', ''));

    }

    protected static function handleVenta(array $data , InventarioService $inventarioService): void
    {


        try {
            \DB::transaction(function () use ($data, $inventarioService) {
                // Crear operación de venta
                $operacion = Operacion::create([
                    'tipo_operacion' => 'venta',
                    'almacen_id' => $data['almacen_id'],
                    'origen_destino_tipo' => $data['origen_destino_tipo'] ?? null,
                    'origen_destino_id' => $data['origen_destino_id'] ?? null,
                    'importe' => $data['importe_total'],
                    'observacion' => $data['observacion'] ?? false,
                    'user_id' => auth()->id(),
                    'fecha' => $data['fecha'],
                    'cerrado' => false,
                ]);

                foreach ($data['products'] as $producto) {
                    // Registrar producto en operación
                    OperacionDetalle::create([
                        'operacion_id' => $operacion->id,
                        'producto_id' => $producto['producto_id'],
                        'cantidad' => -$producto['cantidad'], // Se descuenta del inventario
                        'precio_costo' => $producto['precio_costo'],
                        'precio_venta' => $producto['precio_venta'],
                    ]);

                    // Actualizar inventario (puede lanzar excepción)
                    $inventarioService->updateOrCreateInventory(
                        $data['almacen_id'],
                        $producto['producto_id'],
                        -$producto['cantidad'],
                        $producto['precio_costo'],
                        $producto['precio_venta'],
                    );
                }
            });

            // Si todo fue bien
            \Filament\Notifications\Notification::make()
                ->title('Venta registrada correctamente')
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Si algo falló (incluyendo inventario negativo)
            \Filament\Notifications\Notification::make()
                ->title("Error al registrar la venta: {$e->getMessage()}")
                ->danger()
                ->send();

            // No relanzamos la excepción, así no se muestra la pantalla fea
        }
    }
}
