<?php

namespace App\Filament\Resources\Uebs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UebForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->relationship('empresa', 'name')
                    ->required(),
                Select::make('provincia_id')
                    ->relationship('provincia', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('siglas'),
                TextInput::make('direccion'),
                Toggle::make('activo')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }
}
