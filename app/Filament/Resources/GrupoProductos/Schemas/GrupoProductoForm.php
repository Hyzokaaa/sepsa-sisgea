<?php

namespace App\Filament\Resources\GrupoProductos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GrupoProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unidad_medidas_id')
                    ->required()
                    ->numeric(),
                Select::make('padre_id')
                    ->relationship('padre', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('codigo')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                Toggle::make('activo')
                    ->required(),
            ]);
    }
}
