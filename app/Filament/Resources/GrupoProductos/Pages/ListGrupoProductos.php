<?php

namespace App\Filament\Resources\GrupoProductos\Pages;

use App\Filament\Resources\GrupoProductos\GrupoProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGrupoProductos extends ListRecords
{
    protected static string $resource = GrupoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
