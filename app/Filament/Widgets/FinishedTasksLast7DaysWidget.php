<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class FinishedTasksLast7DaysWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Tareas Finalizadas Últimos 7 Días';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('estado', 'Finalizado')
                    ->where('fecha_finalizacion', '>=', now()->subWeek())
            )
            ->columns([
                TextColumn::make('titulo')->label('Tarea'),
                TextColumn::make('fecha_finalizacion')->dateTime()->label('Finalización'),
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
