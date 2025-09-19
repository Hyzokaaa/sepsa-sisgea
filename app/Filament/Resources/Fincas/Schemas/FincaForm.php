<?php

namespace App\Filament\Resources\Fincas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FincaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ueb_id')
                    ->relationship('ueb', 'name')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('siglas')
                    ->required(),
                Textarea::make('direccion')
                    ->columnSpanFull(),
                Toggle::make('activo')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }
}
