<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
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
                return \App\Models\Task::query()
                    ->whereIn('estado', ['Nuevo', 'En Proceso'])
                    ->where(function ($q) use ($user) {
                        $q->where('usuario_solicita_id', $user->id)
                            ->orWhere('asignado_a_id', $user->id);
                    });
            })

            ->columns([
                \Filament\Tables\Columns\TextColumn::make('titulo')->label('Tarea'),
                \Filament\Tables\Columns\TextColumn::make('estado')->badge(),
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



