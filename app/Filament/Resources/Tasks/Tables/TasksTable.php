<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable(),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo' => 'warning',      // amarillo
                        'En Proceso' => 'info',    // celeste
                        'Finalizado' => 'success',  // verde
                        default => 'gray',
                    })
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('detalle')
                    ->label('Detalle')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('solicitante.name')
                    ->label('Solicita')
                    ->toggleable(),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado')
                    ->toggleable(),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->toggleable(),

                TextColumn::make('fecha_finalizacion')
                    ->label('Finalización')
                    ->dateTime()
                    ->toggleable(),

                TextColumn::make('ultima_modificacion')
                    ->label('Última modificación')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'Nuevo' => 'Nuevo',
                        'En Proceso' => 'En Proceso',
                        'Finalizado' => 'Finalizado',
                    ]),
            ])
            ->defaultSort('ultima_modificacion', 'desc')
            ->emptyStateActions([
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
