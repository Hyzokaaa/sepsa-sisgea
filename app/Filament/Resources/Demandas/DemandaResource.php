<?php

namespace App\Filament\Resources\Demandas;

use App\Filament\Resources\Demandas\Pages\CreateDemanda;
use App\Filament\Resources\Demandas\Pages\EditDemanda;
use App\Filament\Resources\Demandas\Pages\ListDemandas;
use App\Filament\Resources\Demandas\Schemas\DemandaForm;
use App\Filament\Resources\Demandas\Tables\DemandasTable;
use App\Models\Demanda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use  UnitEnum;

class DemandaResource extends Resource
{
    protected static ?string $model = Demanda::class;

    protected static string | UnitEnum | null $navigationGroup = 'Administracion';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DemandaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DemandasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDemandas::route('/'),
            'create' => CreateDemanda::route('/create'),
            'edit' => EditDemanda::route('/{record}/edit'),
        ];
    }
}
