<?php

namespace App\Filament\Actions;

use App\Models\Almacen;
use App\Models\Operacion;
use App\Models\OperacionDetalle;
use App\Models\Producto;
use App\Services\InventarioService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CompraAction
{
    public static function make(): Action
    {
        $inventarioService = new InventarioService;

        return Action::make('compra')
            ->label('Registrar Compra')
            ->icon('heroicon-o-shopping-cart')
            ->modalWidth('4xl')
            ->form(self::getFormSchema())
            ->action(function (array $data) use ($inventarioService) {
                self::handleCompra($data, $inventarioService);
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

            Repeater::make('products')
                ->label('Productos')
                ->schema([
                    Select::make('producto_id')
                        ->label('Producto')
                        ->options(Producto::query()
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(function ($producto) {
                                return [
                                    $producto->id => "{$producto->codigo} - \${$producto->name}"
                                ];
                            }))
                        ->required()
                        ->reactive(),

                    TextInput::make('cantidad')
                        ->numeric()
                        ->live(debounce: 500)
                        ->required()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $precio = floatval($get('precio_costo') ?? 0);
                            $set('importe', floatval($state) * $precio);
                        }),

                    TextInput::make('precio_costo')
                        ->label('Precio Costo')
                        ->required()
                        ->prefix('$')
                        ->numeric()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($set, $get, $state) {
                            $cantidad = floatval($get('cantidad') ?? 0);
                            $set('importe', $cantidad * floatval($state));
                        }),

                    TextInput::make('precio_venta')
                        ->required()
                        ->prefix('$')
                        ->numeric(),

                    TextInput::make('importe')
                        ->label('Importe')
                        ->readOnly()
                        ->numeric()
                        ->prefix('$'),
                ])
                ->columns(5)
                ->afterStateUpdated(function ($state, $set, $get) {
                    self::calcularTotal($set, $get);
                })
                ->required(),

            TextInput::make('importe_total')
                ->label('Total General')
                ->numeric()
                ->readOnly()
                ->prefix('$')
                ->default(0),

            TextInput::make('observacion')
                ->label('Observaciones'),
        ];
    }

    protected static function handleCompra(array $data, InventarioService $inventarioService): void
    {
        try {
            DB::transaction(function () use ($data, $inventarioService) {
                $total = 0;

                foreach ($data['products'] as $producto) {
                    $cantidad = floatval($producto['cantidad']);
                    $precioCosto = floatval($producto['precio_costo']);
                    $total += $cantidad * $precioCosto;
                }

                $operacion = Operacion::create([
                    'tipo_operacion' => 'compra',
                    'almacen_id' => $data['almacen_id'],
                    'importe' => $total,
                    'fecha' => $data['fecha'],
                    'user_id' => auth()->id(),
                    'cerrado' => false,
                    'observacion' => $data['observacion'],
                ]);

                foreach ($data['products'] as $producto) {
                    OperacionDetalle::create([
                        'operacion_id' => $operacion->id,
                        'producto_id' => $producto['producto_id'],
                        'cantidad' => $producto['cantidad'],
                        'precio_costo' => $producto['precio_costo'],
                        'precio_venta' => $producto['precio_venta'],
                    ]);

                    $inventarioService->updateOrCreateInventory(
                        $data['almacen_id'],
                        $producto['producto_id'],
                        $producto['cantidad'],
                        $producto['precio_costo'],
                        $producto['precio_venta'],
                    );
                }
            });

            Notification::make()
                ->title('Compra registrada correctamente')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title("Error al registrar la compra: {$e->getMessage()}")
                ->danger()
                ->send();

            throw $e;
        }
    }

    protected static function calcularTotal($set, $get): void
    {
        $productos = $get('products') ?? [];
        $total = 0;

        foreach ($productos as $producto) {
            $cantidad = floatval($producto['cantidad'] ?? 0);
            $precio = floatval($producto['precio_costo'] ?? 0);
            $total += $cantidad * $precio;
        }

        $set('importe_total', number_format($total, 2, '.', ''));
    }
}
