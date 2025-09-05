<?php

namespace App\Filament\Resources\GrupoProductos\Pages;

use App\Filament\Resources\GrupoProductos\GrupoProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGrupoProductos extends ManageRecords
{
    protected static string $resource = GrupoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
