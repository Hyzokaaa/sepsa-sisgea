<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grupoProducto.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unidadMedida.name')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('codigo')
                    ->searchable(),
                TextColumn::make('imagen')
                    ->searchable(),
                IconColumn::make('activo')
                    ->boolean(),
                TextColumn::make('ingredientess')
                    ->label('Ingredientes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->html()
                    ->default('-')
                    ->formatStateUsing(function ($state, $record) {
                        $ingredientes = $record->ingredientess()->withPivot('cantidad')->get();

                        if ($ingredientes->isEmpty()) {
                            return '<span class="text-gray-400">Sin ingredientes</span>';
                        }

                        return $ingredientes->map(function ($ingrediente) {
                            return "<div><strong>{$ingrediente->name}</strong>: {$ingrediente->pivot->cantidad}</div>";
                        })->implode('');
                    })
                    ->wrap(),
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
