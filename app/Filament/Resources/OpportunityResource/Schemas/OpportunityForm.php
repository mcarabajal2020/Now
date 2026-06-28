<?php

namespace App\Filament\Resources\OpportunityResource\Schemas;

use App\Models\Cliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OpportunityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre de la oportunidad')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre_cuenta')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Cliente $record) => "{$record->numero_cuenta} - {$record->nombre_cuenta}")
                    ->required(),

                Select::make('user_id')
                    ->label('Responsable')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id())
                    ->required(),

                TextInput::make('monto_estimado')
                    ->label('Monto estimado')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('$')
                    ->placeholder('0.00'),

                Select::make('probabilidad')
                    ->label('Probabilidad (%)')
                    ->options([
                        10 => '10%',
                        25 => '25%',
                        50 => '50%',
                        75 => '75%',
                        100 => '100%',
                    ])
                    ->default(10)
                    ->required(),

                DatePicker::make('fecha_esperada_cierre')
                    ->label('Fecha esperada de cierre')
                    ->native(false),

                Select::make('etapa')
                    ->label('Etapa')
                    ->options([
                        'prospeccion' => 'Prospección',
                        'calificacion' => 'Calificación',
                        'propuesta' => 'Propuesta',
                        'negociacion' => 'Negociación',
                        'ganada' => 'Ganada',
                        'perdida' => 'Perdida',
                    ])
                    ->default('prospeccion')
                    ->required()
                    ->native(false),

                Select::make('origen')
                    ->label('Origen')
                    ->options([
                        'web' => 'Sitio web',
                        'referido' => 'Referido',
                        'llamada_fria' => 'Llamada en frío',
                        'email' => 'Correo',
                        'evento' => 'Evento/Feria',
                        'redes_sociales' => 'Redes sociales',
                        'otro' => 'Otro',
                    ])
                    ->searchable()
                    ->native(false),

                TextInput::make('fuente')
                    ->label('Fuente / Detalle')
                    ->placeholder('Ej: Google Ads, Juan Pérez, Expo 2024...')
                    ->maxLength(255),
            ]);
    }
}
