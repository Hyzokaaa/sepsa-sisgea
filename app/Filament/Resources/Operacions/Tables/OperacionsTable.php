<?php

namespace App\Filament\Resources\Operacions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OperacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('tipo_operacion')
                    ->searchable(),
                TextColumn::make('almacen.nombre')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('origen_destino_tipo')
                    ->searchable(),
                TextColumn::make('origen_destino_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('importe')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('cerrado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
