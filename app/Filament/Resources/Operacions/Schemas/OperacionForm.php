<?php

namespace App\Filament\Resources\Operacions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OperacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('tipo_operacion')
                    ->required(),
                Select::make('almacen_id')
                    ->relationship('almacen', 'id')
                    ->required(),
                TextInput::make('origen_destino_tipo'),
                TextInput::make('origen_destino_id')
                    ->numeric(),
                Textarea::make('observacion')
                    ->columnSpanFull(),
                Toggle::make('cerrado')
                    ->required(),
            ]);
    }
}
