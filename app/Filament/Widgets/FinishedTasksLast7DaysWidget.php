<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;


class FinishedTasksLast7DaysWidget extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $since = now()->subDays(7);

        return $table
            ->query(function () use ($user, $since) {
                return \App\Models\Task::query()
                    ->where('estado', 'Finalizado')
                    ->where('fecha_finalizacion', '>=', $since)
                    ->where(function ($q) use ($user) {
                        $q->where('usuario_solicita_id', $user->id)
                            ->orWhere('asignado_a_id', $user->id);
                    });
            })

            ->columns([
                \Filament\Tables\Columns\TextColumn::make('titulo')->label('Tarea'),
                \Filament\Tables\Columns\TextColumn::make('fecha_finalizacion')->dateTime()->label('Finalización'),
            ])
            ->defaultPaginationPageOption(5)
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}


