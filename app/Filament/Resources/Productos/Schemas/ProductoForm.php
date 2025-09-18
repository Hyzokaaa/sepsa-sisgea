<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                Toggle::make('activo')
                    ->required(),
            ]);
    }
}
