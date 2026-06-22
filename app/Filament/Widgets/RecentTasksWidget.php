<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentTasksWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Tareas recientes';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user->role?->nombre === 'admin';

        $query = Task::query()->with(['solicitante', 'asignadoA']);

        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('usuario_solicita_id', $user->id)
                    ->orWhere('asignado_a_id', $user->id);
            });
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('titulo')
                    ->label('Tarea')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),

                TextColumn::make('solicitante.name')
                    ->label('Solicitante')
                    ->searchable(),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar'),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
