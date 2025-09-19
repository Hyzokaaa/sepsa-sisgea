<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Producto;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grupo_productos_id')
                    ->relationship('grupoProducto', 'name')
                    ->required(),
                Select::make('unidad_medidas_id')
                    ->relationship('unidadMedida', 'siglas')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('imagen'),



                Textarea::make('descripcion')
                    ->columnSpanFull(),

                Repeater::make('ingredientes')
                    ->columnSpanFull()
                    ->relationship('ingredientes')
                    ->schema([
                        Select::make('ingrediente_id')
                            ->options(Producto::query()
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($producto) {
                                    return [
                                        $producto->id => "{$producto->codigo} - \${$producto->name}"
                                    ];
                                }))
                            ->label('Nombre del Ingrediente'),

                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2)
                    ->addActionLabel('Añadir Ingrediente')
                    ->defaultItems(0)
                    ->collapsible()
                    ->reorderable(true),
                Toggle::make('activo')
                    ->required(),
            ]);
    }
}
