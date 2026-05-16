<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable(),

               \Filament\Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo' => 'warning',      // amarillo
                        'En Proceso' => 'info',    // celeste
                        'Finalizado' => 'success',  // verde
                        default => 'gray',
                    })
                    ->toggleable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('detalle')
                    ->label('Detalle')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                \Filament\Tables\Columns\TextColumn::make('solicitante.name')
                    ->label('Solicita')
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('asignadoA.name')
                    ->label('Asignado')
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('fecha_finalizacion')
                    ->label('Finalización')
                    ->dateTime()
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('ultima_modificacion')
                    ->label('Última modificación')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('estado')
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
