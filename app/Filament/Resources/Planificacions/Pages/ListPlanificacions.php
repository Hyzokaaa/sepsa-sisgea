<?php

namespace App\Filament\Resources\Planificacions\Pages;

use App\Filament\Resources\Planificacions\PlanificacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlanificacions extends ListRecords
{
    protected static string $resource = PlanificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
