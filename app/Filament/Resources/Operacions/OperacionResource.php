<?php

namespace App\Filament\Resources\Operacions;

use App\Filament\Resources\Operacions\Pages\CreateOperacion;
use App\Filament\Resources\Operacions\Pages\EditOperacion;
use App\Filament\Resources\Operacions\Pages\ListOperacions;
use App\Filament\Resources\Operacions\Schemas\OperacionForm;
use App\Filament\Resources\Operacions\Tables\OperacionsTable;
use App\Models\Operacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OperacionResource extends Resource
{
    protected static ?string $model = Operacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OperacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperacionsTable::configure($table);
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
            'index' => ListOperacions::route('/'),
            'create' => CreateOperacion::route('/create'),
            'edit' => EditOperacion::route('/{record}/edit'),
        ];
    }
}
