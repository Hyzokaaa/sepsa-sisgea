<?php

namespace App\Filament\Resources\GrupoProductos\Pages;

use App\Filament\Resources\GrupoProductos\GrupoProductoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGrupoProducto extends EditRecord
{
    protected static string $resource = GrupoProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
