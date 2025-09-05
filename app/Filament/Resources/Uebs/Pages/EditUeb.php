<?php

namespace App\Filament\Resources\Uebs\Pages;

use App\Filament\Resources\Uebs\UebResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUeb extends EditRecord
{
    protected static string $resource = UebResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
