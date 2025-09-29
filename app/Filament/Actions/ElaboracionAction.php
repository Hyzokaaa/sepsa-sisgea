<?php

/*
*/

namespace App\Filament\Actions;

use App\Models\Ueb;
use App\Models\Operacion;
use App\Models\Producto;
use App\Services\InventarioService;
use Filament\Actions\Action;
use App\Models\Inventario;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ElaboracionAction extends Action
{
    protected InventarioService $inventarioService;

    public static function getDefaultName(): ?string
    {
        return 'elaborar_producto';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventarioService = new InventarioService;

        $this->label('Elaborar Producto')
            ->icon('heroicon-o-cog')
            ->modalWidth('4xl')
            ->form($this->getFormSchema())
            ->action(function (array $data): void {
                try {
                    $this->handleElaboracion($data);
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Error al elaborar producto')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected function getFormSchema(): array
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        $userUebId = auth()->user()->ueb_id;

        return [
            Select::make('ueb_id')
                ->label('UEB')
                ->options(
                    fn() => $panelId === 'Ueb'
                        ? Ueb::where('id', $userUebId)->pluck('name', 'id')
                        : Ueb::pluck('name', 'id')
                )
                ->required()
                ->default($userUebId)
                ->searchable(),

            DateTimePicker::make('Fecha')
                ->required()
                ->default(now()),

            Select::make('producto_id')
                ->label('Producto a elaborar')
                ->options(Producto::query()
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(function ($producto) {
                        return [
                            $producto->id => "{$producto->codigo} - {$producto->name}"
                        ];
                    }))
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    $producto = Producto::with('ingredientes')->find($state);

                    if ($producto) {
                        $set('precio_costo', $producto->PrecioCosto ?? 0);
                        $set('precio_venta', $producto->PrecioVenta ?? 0);

                        $ingredientesData = $producto->ingredientes->map(function ($ingrediente) {
                            return [
                                'ingrediente_id' => $ingrediente->id,
                                'nombre' => $ingrediente->name ?? $ingrediente->Producto ?? 'Sin nombre',
                                'cantidad_por_unidad' => $ingrediente->pivot->cantidad ?? 0,
                                'cantidad_total' => 0,
                                'precio_costo' => $ingrediente->PrecioCosto ?? 0,
                                'precio_venta' => $ingrediente->PrecioVenta ?? 0,
                            ];
                        })->toArray();

                        $set('ingredientes', $ingredientesData);
                    } else {
                        $set('precio_costo', 0);
                        $set('precio_venta', 0);
                        $set('ingredientes', []);
                    }
                }),

            TextInput::make('cantidad_producida')
                ->label('Cantidad a elaborar')
                ->numeric()
                ->required()
                ->minValue(0.01)
                ->step(0.01)
                ->live()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $ingredientes = $get('ingredientes') ?? [];
                    $updatedIngredientes = [];

                    foreach ($ingredientes as $ingrediente) {
                        $ingrediente['cantidad_total'] = ($ingrediente['cantidad_por_unidad'] ?? 0) * $state;
                        $updatedIngredientes[] = $ingrediente;
                    }

                    $set('ingredientes', $updatedIngredientes);
                }),

            Repeater::make('ingredientes')
                ->label('Ingredientes requeridos')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Ingrediente')
                        ->disabled()
                        ->columnSpan(2),

                    TextInput::make('cantidad_por_unidad')
                        ->label('Cantidad por unidad')
                        ->numeric()
                        ->disabled()
                        ->step(0.001),

                    TextInput::make('cantidad_total')
                        ->label('Total requerido')
                        ->numeric()
                        ->disabled()
                        ->step(0.001),
                ])
                ->columns(3)
                ->default([])
                ->deletable(false)
                ->addable(false)
                ->reorderable(false),

            TextInput::make('precio_costo')
                ->label('Precio Costo Producto Final')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.01),

            TextInput::make('precio_venta')
                ->label('Precio Venta Producto Final')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.01),
        ];
    }

    protected function handleElaboracion(array $data): void
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        $userUebId = auth()->user()->ueb_id;

        if ($panelId === 'Ueb' && $data['ueb_id'] != $userUebId) {
            Notification::make()
                ->title('No puedes registrar elaboraciones para una UEB distinta a la tuya.')
                ->danger()
                ->send();
            return;
        }

        try {
            DB::transaction(function () use ($data) {
                $uebId = $data['ueb_id'];
                $productoId = $data['producto_id'];
                $cantidadProducida = floatval($data['cantidad_producida']);
                $precioCosto = floatval($data['precio_costo']);
                $precioVenta = floatval($data['precio_venta']);
                $ingredientes = $data['ingredientes'] ?? [];

                // Crear operación principal
                $operacion = Operacion::create([
                    'tipo_operacion' => 'elaboracion',
                    'almacen_id' => $uebId,
                    'importe' => 0,
                    'user_id' => auth()->id(),
                    'fecha' => $data['Fecha'],
                    'Cerrado' => false,
                    'observaciones' => "Elaboración de producto ID $productoId",
                ]);





                /* Error al elaborar producto SQLSTATE[42703]: Undefined column: 7 ERROR: no existe la columna «inventario_id» LINE 1: ...* from "inventarios" where "producto_id" = $1 and "inventari... ^ (Connection: pgsql, SQL: select * from "inventarios" where "producto_id" = 1 and "inventario_id" = App\Models\Inventario and "almacen_id" = 2 limit 1) */

                // Procesar cada ingrediente
                foreach ($ingredientes as $ing) {
                    $ingredienteId = $ing['ingrediente_id'];
                    $cantidadTotal = floatval($ing['cantidad'] ?? 0);
                    $precioCostoIng = floatval($ing['precio_costo'] ?? 0);
                    $precioVentaIng = floatval($ing['precio_venta'] ?? 0);

                    $ingrediente = Producto::find($ingredienteId);
                    $nombreIngrediente = $ingrediente->name ?? $ingrediente->Producto ?? 'Desconocido';

                    // Verificar inventario
                    $inventario = Inventario::where('producto_id', $ingredienteId)
                        ->where('almacen_id', $uebId)  // Solo si tienes esta columna
                        ->first();

                    if (!$inventario || $inventario->cantidad < $cantidadTotal) {
                        throw new \Exception("Inventario insuficiente de {$nombreIngrediente}. Disponible: " . ($inventario->cantidad ?? 0));
                    }

                    // Crear detalle de operación para el ingrediente (salida)
                    $operacion->detalles()->create([
                        'producto_id' => $ingredienteId,
                        'cantidad' => -$cantidadTotal,
                        'precio_costo' => $precioCostoIng,
                        'precio_venta' => $precioVentaIng,
                    ]);

                    // Actualizar inventario del ingrediente
                    $inventario->decrement('cantidad', $cantidadTotal);
                }

                // Actualizar inventario del producto elaborado
                $this->inventarioService->updateOrCreateInventory(
                    $uebId,
                    $productoId,
                    $cantidadProducida,
                    $precioCosto,
                    $precioVenta
                );

                // Crear detalle de operación para el producto elaborado (entrada)
                $operacion->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidadProducida,
                    'precio_costo' => $precioCosto,
                    'precio_venta' => $precioVenta,
                ]);

                // Calcular y actualizar importe total de la operación
                $importeTotal = $operacion->detalles()->sum(DB::raw('cantidad * precio_costo'));
                $operacion->update(['importe' => abs($importeTotal)]);
            });

            Notification::make()
                ->title('Producto elaborado correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al elaborar producto')
                ->body($e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }
}
