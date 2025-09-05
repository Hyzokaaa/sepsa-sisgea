<?php

namespace App\Filament\Resources\UnidadMedidas\Pages;

use App\Filament\Resources\UnidadMedidas\UnidadMedidaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnidadMedida extends EditRecord
{
    protected static string $resource = UnidadMedidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
