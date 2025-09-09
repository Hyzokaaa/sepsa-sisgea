<?php

use App\Models\Almacen;
use App\Models\Inventario;
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


class TrasladoAction
{

    public static function make(): Action
    {
        $inventarioService = new InventarioService;

        return Action::make('traslado')
            ->label('Registrar traslado')
            ->icon('heroicon-o-currency-dollar')
            ->modalWidth('4xl')
            ->form(self::getFormSchema())
            ->action(function (array $data) use ($inventarioService) {
                self::handleVenta($data, $inventarioService);
            });;
    }


    protected static function getFormSchema(): array
    {
        return [
            Select::make('almacen_destino_id')
                ->label('Almacén Destino')
                ->options(
                    Almacen::query()
                        ->orderBy('nombre')
                        ->where('activo', true)
                        ->get()
                        ->mapWithKeys(function ($almacen) {
                            return [
                                $almacen->id => "{$almacen->id} - {$almacen->nombre}"
                            ];
                        })
                )
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
                        ->options(
                            Producto::query()
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($producto) {
                                    return [
                                        $producto->id => "{$producto->codigo} - {$producto->nombre}"
                                    ];
                                })
                        )
                        ->required()
                        ->reactive(),

                    TextInput::make('cantidad')
                        ->numeric()
                        ->live(debounce: 500)
                        ->required()
                ])
        ];
    }


        protected static function handleTraslado(array $data, InventarioService $inventarioService)
    {

    }



}
