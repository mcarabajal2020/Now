<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
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
                    ->disabled(fn (?Task $record): bool => $record?->asignado_a_id !== auth()->id() || $record?->estado === 'Finalizado')
                    ->dehydrated(fn (?Task $record): bool => $record?->asignado_a_id === auth()->id() && $record?->estado !== 'Finalizado')
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

                Hidden::make('fecha_finalizacion')
                    ->nullable(),
            ]);
    }
}
