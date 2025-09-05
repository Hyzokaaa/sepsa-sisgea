<?php

namespace App\Filament\Resources\Planificacions;

use App\Filament\Resources\Planificacions\Pages\CreatePlanificacion;
use App\Filament\Resources\Planificacions\Pages\EditPlanificacion;
use App\Filament\Resources\Planificacions\Pages\ListPlanificacions;
use App\Filament\Resources\Planificacions\Schemas\PlanificacionForm;
use App\Filament\Resources\Planificacions\Tables\PlanificacionsTable;
use App\Models\Planificacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PlanificacionResource extends Resource
{
    protected static ?string $model = Planificacion::class;
    protected static string | UnitEnum | null $navigationGroup = 'Gestion';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PlanificacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlanificacionsTable::configure($table);
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
            'index' => ListPlanificacions::route('/'),
            'create' => CreatePlanificacion::route('/create'),
            'edit' => EditPlanificacion::route('/{record}/edit'),
        ];
    }
}
