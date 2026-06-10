<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable(),

                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo' => 'warning',      // amarillo
                        'En Proceso' => 'info',    // celeste
                        'Finalizado' => 'success',  // verde
                        default => 'gray',
                    })
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('detalle')
                    ->label('Detalle')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('solicitante.name')
                    ->label('Solicita')
                    ->toggleable(),

                TextColumn::make('asignadoA.name')
                    ->label('Asignado')
                    ->toggleable(),

                TextColumn::make('area.nombre')
                    ->label('Área')
                    ->toggleable(),

                TextColumn::make('fecha_finalizacion')
                    ->label('Finalización')
                    ->dateTime()
                    ->toggleable(),

                TextColumn::make('tipo_uso')
                    ->label('Uso')
                    ->toggleable(),

                TextColumn::make('tipo_tarea')
                    ->label('Tipo de tarea')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'prioridad alta' => 'danger',
                        'prioridad baja' => 'gray',
                        default => 'secondary',
                    })
                    ->toggleable(),

                TextColumn::make('cliente.numero_cuenta')
                    ->label('Cliente (cuenta)')
                    ->getStateUsing(fn ($record) => $record->cliente ? ($record->cliente->numero_cuenta . ' — ' . $record->cliente->nombre_cuenta) : null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ultima_modificacion')
                    ->label('Última modificación')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                // Filtro de búsqueda por cliente (nombre, número, tags)
                Filter::make('cliente_search')
                    ->label('Buscar cliente')
                    ->form([
                        TextInput::make('cliente_search')
                            ->label('Nombre, número o tag')
                            ->placeholder('Ej: DIETZ, 1169, 45'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['cliente_search'])) {
                            return $query;
                        }
                        $search = trim($data['cliente_search']);
                        return $query->whereHas('cliente', function (Builder $q) use ($search) {
                            $q->where('nombre_cuenta', 'like', "%{$search}%")
                              ->orWhere('numero_cuenta', 'like', "%{$search}%")
                              ->orWhereRaw('JSON_CONTAINS(tags, ?)', [json_encode((string) $search)]);
                        });
                    }),

                SelectFilter::make('tipo_uso')
                    ->label('Tipo de uso')
                    ->options([
                        'uso interno' => 'Uso interno',
                        'uso externo' => 'Uso externo',
                    ]),

                SelectFilter::make('tipo_tarea')
                    ->label('Tipo de tarea')
                    ->options([
                        'consulta' => 'Consulta',
                        'cotizacion' => 'Cotización',
                        'reclamo' => 'Reclamo',
                        'visita_al_campo' => 'Visita al campo',
                        'pedido' => 'Pedido',
                        'comunicado' => 'Comunicado',
                        'transaccion_comercial' => 'Transacción comercial',
                        'gestion_cobranza' => 'Gestión cobranza',
                        'apertura_siniestro' => 'Apertura siniestro',
                        'seguimiento' => 'Seguimiento',
                        'anticipo_financiero' => 'Anticipo financiero',
                        'alta_seguro' => 'Alta seguro',
                        'baja_seguro' => 'Baja seguro',
                        'alta_telefonia' => 'Alta telefonía',
                        'baja_telefonia' => 'Baja telefonía',
                        'alta_fletes' => 'Alta fletes',
                    ]),

                SelectFilter::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'prioridad alta' => 'Prioridad alta',
                        'prioridad baja' => 'Prioridad baja',
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
