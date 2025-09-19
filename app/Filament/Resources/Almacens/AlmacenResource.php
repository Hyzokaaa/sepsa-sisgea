<?php

namespace App\Filament\Resources\Almacens;

use App\Filament\Resources\Almacens\Pages\CreateAlmacen;
use App\Filament\Resources\Almacens\Pages\EditAlmacen;
use App\Filament\Resources\Almacens\Pages\ListAlmacens;
use App\Filament\Resources\Almacens\Schemas\AlmacenForm;
use App\Filament\Resources\Almacens\Tables\AlmacensTable;
use App\Models\Almacen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AlmacenResource extends Resource
{
    protected static ?string $model = Almacen::class;

            protected static ?int $navigationSort = 6;

    protected static string | UnitEnum | null $navigationGroup = 'Administracion';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AlmacenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlmacensTable::configure($table);
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
            'index' => ListAlmacens::route('/'),
            'create' => CreateAlmacen::route('/create'),
            'edit' => EditAlmacen::route('/{record}/edit'),
        ];
    }
}
