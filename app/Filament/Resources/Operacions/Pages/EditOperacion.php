<?php

namespace App\Filament\Resources\Operacions\Pages;

use App\Filament\Resources\Operacions\OperacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOperacion extends EditRecord
{
    protected static string $resource = OperacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
