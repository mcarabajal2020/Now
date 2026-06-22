<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OpenTasksWidget extends TableWidget
{
    protected static ?int $sort = 1;

    protected static ?string $heading = 'Tareas Abiertas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()->whereIn('estado', ['Nuevo', 'En Proceso'])
            )
            ->columns([
                TextColumn::make('titulo')->label('Tarea'),
                TextColumn::make('estado')->badge(),
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
