<?php

namespace App\Filament\Resources\Operacions\Pages;

use App\Filament\Resources\Operacions\OperacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOperacions extends ListRecords
{
    protected static string $resource = OperacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
