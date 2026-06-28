<?php

namespace App\Filament\Resources\OpportunityResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OpportunitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Oportunidad')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('cliente.nombre_cuenta')
                    ->label('Cliente')
                    ->searchable(['nombre_cuenta', 'numero_cuenta'])
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->cliente?->nombre_cuenta ?? '—'),

                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('monto_estimado')
                    ->label('Monto estimado')
                    ->money('ARS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('probabilidad')
                    ->label('Prob.')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter(),

                BadgeColumn::make('etapa')
                    ->label('Etapa')
                    ->colors([
                        'gray' => 'prospeccion',
                        'info' => 'calificacion',
                        'warning' => 'propuesta',
                        'primary' => 'negociacion',
                        'success' => 'ganada',
                        'danger' => 'perdida',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'prospeccion' => 'Prospección',
                        'calificacion' => 'Calificación',
                        'propuesta' => 'Propuesta',
                        'negociacion' => 'Negociación',
                        'ganada' => 'Ganada',
                        'perdida' => 'Perdida',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('fecha_esperada_cierre')
                    ->label('Cierre esperado')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('etapa')
                    ->label('Etapa')
                    ->options([
                        'prospeccion' => 'Prospección',
                        'calificacion' => 'Calificación',
                        'propuesta' => 'Propuesta',
                        'negociacion' => 'Negociación',
                        'ganada' => 'Ganada',
                        'perdida' => 'Perdida',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Responsable')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('origen')
                    ->label('Origen')
                    ->options([
                        'web' => 'Sitio web',
                        'referido' => 'Referido',
                        'llamada_fria' => 'Llamada en frío',
                        'email' => 'Correo',
                        'evento' => 'Evento/Feria',
                        'redes_sociales' => 'Redes sociales',
                        'otro' => 'Otro',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                Action::make('avanzar')
                    ->label('Avanzar')
                    ->icon('heroicon-o-arrow-right')
                    ->color('success')
                    ->visible(fn ($record) => ! in_array($record->etapa, ['ganada', 'perdida']))
                    ->action(function ($record) {
                        $record->avanzarEtapa();
                    })
                    ->requiresConfirmation(),

                Action::make('ganar')
                    ->label('Marcar ganada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->etapa !== 'ganada')
                    ->action(function ($record) {
                        $record->marcarComoGanada();
                    })
                    ->requiresConfirmation(),

                Action::make('perder')
                    ->label('Marcar perdida')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->etapa !== 'perdida')
                    ->form([
                        Textarea::make('motivo_perdida')
                            ->label('Motivo de la pérdida')
                            ->rows(3)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->marcarComoPerdida($data['motivo_perdida']);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar'),
                ]),
            ]);
    }
}
