<?php

namespace App\Filament\Resources\GrupoProductos;

use App\Filament\Resources\GrupoProductos\Pages\CreateGrupoProducto;
use App\Filament\Resources\GrupoProductos\Pages\EditGrupoProducto;
use App\Filament\Resources\GrupoProductos\Pages\ListGrupoProductos;
use App\Filament\Resources\GrupoProductos\Schemas\GrupoProductoForm;
use App\Filament\Resources\GrupoProductos\Tables\GrupoProductosTable;
use App\Models\GrupoProducto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GrupoProductoResource extends Resource
{
    protected static ?string $model = GrupoProducto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GrupoProductoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GrupoProductosTable::configure($table);
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
            'index' => ListGrupoProductos::route('/'),
            'create' => CreateGrupoProducto::route('/create'),
            'edit' => EditGrupoProducto::route('/{record}/edit'),
        ];
    }
}
