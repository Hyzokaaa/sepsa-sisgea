<?php

namespace App\Filament\Resources\Planificacions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlanificacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('periodo_id')
                    ->relationship('periodo', 'id')
                    ->required(),
                Select::make('ueb_id')
                    ->relationship('ueb', 'name')
                    ->required(),
                Textarea::make('observacion')
                    ->columnSpanFull(),
            ]);
    }
}
