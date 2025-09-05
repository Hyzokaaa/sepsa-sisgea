<?php

namespace App\Filament\Resources\UnidadMedidas;

use App\Filament\Resources\UnidadMedidas\Pages\CreateUnidadMedida;
use App\Filament\Resources\UnidadMedidas\Pages\EditUnidadMedida;
use App\Filament\Resources\UnidadMedidas\Pages\ListUnidadMedidas;
use App\Filament\Resources\UnidadMedidas\Schemas\UnidadMedidaForm;
use App\Filament\Resources\UnidadMedidas\Tables\UnidadMedidasTable;
use App\Models\UnidadMedida;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnidadMedidaResource extends Resource
{
    protected static ?string $model = UnidadMedida::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UnidadMedidaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnidadMedidasTable::configure($table);
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
            'index' => ListUnidadMedidas::route('/'),
            'create' => CreateUnidadMedida::route('/create'),
            'edit' => EditUnidadMedida::route('/{record}/edit'),
        ];
    }
}
