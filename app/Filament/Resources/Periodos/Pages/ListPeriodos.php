<?php

namespace App\Filament\Resources\Periodos\Pages;

use App\Filament\Resources\Periodos\PeriodoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeriodos extends ListRecords
{
    protected static string $resource = PeriodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
