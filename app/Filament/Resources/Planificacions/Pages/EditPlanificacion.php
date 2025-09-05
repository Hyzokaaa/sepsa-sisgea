<?php

namespace App\Filament\Resources\Planificacions\Pages;

use App\Filament\Resources\Planificacions\PlanificacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanificacion extends EditRecord
{
    protected static string $resource = PlanificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
