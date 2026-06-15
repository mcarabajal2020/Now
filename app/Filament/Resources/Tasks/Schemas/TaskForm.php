<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        // Importante: se evalúa en runtime y requiere auth().
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título de Tarea')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->label('Descripción de Tarea')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('detalle')
                    ->label('Historial de avance')
                    ->rows(8)
                    ->visibleOn('edit')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Textarea::make('nuevo_detalle')
                    ->label('Detalle de avance')
                    ->rows(4)
                    ->maxLength(5000)
                    ->visibleOn('edit')
                    ->disabled(fn (?Task $record): bool =>
                        // Deshabilitado si la tarea está finalizada o el usuario no pertenece al área ni es asignado
                        ($record?->estado === 'Finalizado') ||
                        ! Auth::check() ||
                        ! (
                            Auth::id() === $record?->asignado_a_id ||
                            (
                                ! is_null(Auth::user()?->area_id) &&
                                ! is_null($record?->area_id) &&
                                Auth::user()?->area_id === $record?->area_id
                            )
                        )
                    )
                    ->dehydrated(fn (?Task $record): bool => filled($record) && (
                            Auth::id() === $record->asignado_a_id ||
                        (
                            ! is_null(auth()->user()?->area_id) &&
                            ! is_null($record->area_id) &&
                            auth()->user()?->area_id === $record->area_id
                        )
                    ) && $record->estado !== 'Finalizado')
                    ->columnSpanFull(),

                Hidden::make('estado')
                    ->default('Nuevo'),

                DateTimePicker::make('fecha_creacion')
                    ->label('Fecha de creación')
                    ->required()
                    ->default(now())
                    ->disabled()
                    ->dehydrated(),

                Hidden::make('usuario_solicita_id')
                    ->default(fn (): ?int => auth()->id())
                    ->required(),

                Select::make('asignado_a_id')
                    ->label('Asignado a')
                    ->relationship('asignadoA', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Asignar este ticket a un área en lugar de a una persona'),

                // -----------------
                // Uso / tipo / prioridad
                // -----------------
                Select::make('tipo_uso')
                    ->label('Tipo de uso')
                    ->options([
                        'uso interno' => 'Uso interno',
                        'uso externo' => 'Uso externo',
                    ])
                    ->default('uso interno')
                    ->required()
                    ->reactive(),

                Select::make('tipo_tarea_id')
                    ->label('Tipo de tarea')
                    ->relationship('tipoTarea', 'nombre')
                    ->searchable()
                    ->preload()
                    ->nullable(),


                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'prioridad alta' => 'Prioridad alta',
                        'prioridad baja' => 'Prioridad baja',
                    ])
                    ->default('prioridad alta')
                    ->required(),

                // -----------------
                // Cliente (solo uso externo)
                // -----------------
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => ($get('tipo_uso') ?? 'uso interno') === 'uso externo')
                    ->required(fn ($get) => ($get('tipo_uso') ?? 'uso interno') === 'uso externo')
                    ->options(function () {
                        // para que el select funcione como “buscar por nombre/cuenta/tag” usamos una lista paginada
                        // (Filament internamente hace search; con preload puede ser pesado con muchos clientes)
                        return \App\Models\Cliente::query()
                            ->get()
                            ->mapWithKeys(function ($c) {
                                $tagStr = is_array($c->tags) && ! empty($c->tags) ? (' [' . implode(', ', $c->tags) . ']') : '';
                                return [$c->id => $c->numero_cuenta.' - '.$c->nombre_cuenta.$tagStr];
                            })
                            ->toArray();
                    })
                    ->helperText('Se puede filtrar/seleccionar por número de cuenta, nombre o tags (si tiene tags).')
                    ->reactive(),

                Hidden::make('fecha_finalizacion')
                    ->nullable(),
            ]);
    }
}
