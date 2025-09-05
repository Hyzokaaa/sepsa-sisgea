<?php

namespace App\Filament\Resources\Demandas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DemandaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ueb_id')
                    ->relationship('ueb', 'name')
                    ->required(),
                Select::make('periodo_id')
                    ->relationship('periodo', 'id')
                    ->required(),
                Select::make('cliente_id')
                    ->relationship('cliente', 'name')
                    ->required(),
                Textarea::make('observacion')
                    ->columnSpanFull(),
            ]);
    }
}
