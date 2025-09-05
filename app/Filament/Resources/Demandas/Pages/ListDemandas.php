<?php

namespace App\Filament\Resources\Demandas\Pages;

use App\Filament\Resources\Demandas\DemandaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDemandas extends ListRecords
{
    protected static string $resource = DemandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
