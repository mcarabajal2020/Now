<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $recordTitleAttribute = 'comentario';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->colors([
                        'secondary' => 'creado',
                        'primary' => 'asignacion',
                        'warning' => 'estado',
                        'success' => 'comentario',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'creado' => 'Creado',
                        'asignacion' => 'Asignación',
                        'estado' => 'Cambio de estado',
                        'comentario' => 'Comentario',
                        default => $state ?? '',
                    })
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),

                TextColumn::make('comentario')
                    ->label('Detalle')
                    ->wrap()
                    ->limit(200),

                TextColumn::make('old_value')
                    ->label('Valor anterior')
                    ->formatStateUsing(fn ($state): ?string => $this->resolveValue($state)),

                TextColumn::make('new_value')
                    ->label('Valor nuevo')
                    ->formatStateUsing(fn ($state): ?string => $this->resolveValue($state)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function resolveValue($state): ?string
    {
        if (blank($state)) {
            return null;
        }

        // Intentar resolver como ID de usuario
        if (is_numeric($state)) {
            $user = User::find((int) $state);

            return $user?->name ?? (string) $state;
        }

        return (string) $state;
    }
}
