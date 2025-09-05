<?php

namespace App\Filament\Resources\Demandas\Pages;

use App\Filament\Resources\Demandas\DemandaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDemanda extends EditRecord
{
    protected static string $resource = DemandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
