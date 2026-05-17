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

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(function () use ($user) {
                return Task::query()
                    ->whereIn('estado', ['Nuevo', 'En Proceso'])
                    ->where(function ($q) use ($user) {
                        $q->where('usuario_solicita_id', $user->id)
                            ->orWhere('asignado_a_id', $user->id);
                    });
            })

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
