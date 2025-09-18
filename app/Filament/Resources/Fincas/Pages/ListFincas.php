<?php

namespace App\Filament\Resources\Fincas\Pages;

use App\Filament\Resources\Fincas\FincaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFincas extends ListRecords
{
    protected static string $resource = FincaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
