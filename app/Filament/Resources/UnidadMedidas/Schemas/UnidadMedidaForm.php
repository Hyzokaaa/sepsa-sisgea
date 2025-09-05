<?php

namespace App\Filament\Resources\UnidadMedidas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnidadMedidaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('siglas')
                    ->required(),
            ]);
    }
}
