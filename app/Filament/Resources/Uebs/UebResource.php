<?php

namespace App\Filament\Resources\Uebs;

use App\Filament\Resources\Uebs\Pages\CreateUeb;
use App\Filament\Resources\Uebs\Pages\EditUeb;
use App\Filament\Resources\Uebs\Pages\ListUebs;
use App\Filament\Resources\Uebs\Schemas\UebForm;
use App\Filament\Resources\Uebs\Tables\UebsTable;
use App\Models\Ueb;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum; 

class UebResource extends Resource
{
    protected static ?string $model = Ueb::class;

        protected static string | UnitEnum | null $navigationGroup = 'Administracion';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UebForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UebsTable::configure($table);
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
            'index' => ListUebs::route('/'),
            'create' => CreateUeb::route('/create'),
            'edit' => EditUeb::route('/{record}/edit'),
        ];
    }
}
