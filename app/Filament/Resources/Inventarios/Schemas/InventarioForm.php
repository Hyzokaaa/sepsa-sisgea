<?php

namespace App\Filament\Resources\Inventarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('almacen_id')
                    ->relationship('almacen', 'nombre')
                    ->required(),
                Select::make('producto_id')
                    ->relationship('producto', 'name')
                    ->required(),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('precio_costo')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('precio_venta')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
