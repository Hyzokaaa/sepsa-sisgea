<?php

namespace App\Filament\Resources\Uebs\Pages;

use App\Filament\Resources\Uebs\UebResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUebs extends ListRecords
{
    protected static string $resource = UebResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
