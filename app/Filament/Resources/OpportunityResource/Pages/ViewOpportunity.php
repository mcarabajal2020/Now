<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewOpportunity extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editar'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información general')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nombre')
                                    ->label('Nombre')
                                    ->weight('bold'),

                                TextEntry::make('cliente.nombre_cuenta')
                                    ->label('Cliente')
                                    ->getStateUsing(fn ($record) => $record->cliente?->nombre_cuenta ?? '—'),

                                TextEntry::make('user.name')
                                    ->label('Responsable'),

                                TextEntry::make('origen')
                                    ->label('Origen')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'web' => 'Web',
                                        'referido' => 'Referido',
                                        'llamada_fria' => 'Llamada en frío',
                                        'email' => 'Email',
                                        'evento' => 'Evento/Feria',
                                        'redes_sociales' => 'Redes sociales',
                                        'otro' => 'Otro',
                                        default => $state ?? '—',
                                    }),

                                TextEntry::make('fuente')
                                    ->label('Fuente')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label('Creada')
                                    ->dateTime(),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Detalles comerciales')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('monto_estimado')
                                    ->label('Monto estimado')
                                    ->money('ARS')
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('probabilidad')
                                    ->label('Probabilidad')
                                    ->suffix('%')
                                    ->color(fn ($state) => match (true) {
                                        $state >= 75 => 'success',
                                        $state >= 50 => 'warning',
                                        default => 'gray',
                                    }),

                                TextEntry::make('etapa')
                                    ->label('Etapa actual')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'prospeccion' => 'Prospección',
                                        'calificacion' => 'Calificación',
                                        'propuesta' => 'Propuesta',
                                        'negociacion' => 'Negociación',
                                        'ganada' => 'Ganada',
                                        'perdida' => 'Perdida',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'prospeccion' => 'gray',
                                        'calificacion' => 'info',
                                        'propuesta' => 'warning',
                                        'negociacion' => 'primary',
                                        'ganada' => 'success',
                                        'perdida' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('fecha_esperada_cierre')
                                    ->label('Fecha esperada de cierre')
                                    ->date(),

                                TextEntry::make('ganada_at')
                                    ->label('Ganada el')
                                    ->dateTime()
                                    ->placeholder('—'),

                                TextEntry::make('perdida_at')
                                    ->label('Perdida el')
                                    ->dateTime()
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columns(3),

                Section::make('Descripción')
                    ->schema([
                        TextEntry::make('descripcion')
                            ->columnSpanFull()
                            ->placeholder('Sin descripción'),
                    ]),

                Section::make('Motivo de pérdida')
                    ->schema([
                        TextEntry::make('motivo_perdida')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])
                    ->visible(fn ($record) => $record->etapa === 'perdida' && filled($record->motivo_perdida)),
            ]);
    }
}
