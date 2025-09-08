<?php

namespace App\Filament\Resources\Operacions\Pages;

use App\Filament\Actions\VentaAction;
use App\Filament\Resources\Operacions\OperacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Actions\CompraAction;

class ListOperacions extends ListRecords
{
    protected static string $resource = OperacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
            CompraAction::make('Compra')
                ->color('info'),
            VentaAction::make('Venta')
                ->color('primary')
        ];
    }
}
