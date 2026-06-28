<?php

namespace App\Filament\Resources\OpportunityResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActividadesRelationManager extends RelationManager
{
    protected static string $relationship = 'actividades';

    protected static ?string $recordTitleAttribute = 'titulo';

    protected static ?string $label = 'Actividades';

    protected static ?string $pluralLabel = 'Actividades';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'llamada' => 'Llamada',
                    'reunion' => 'Reunión',
                    'email' => 'Correo',
                ])
                ->required()
                ->native(false),

            TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->maxLength(255),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->nullable(),

            DatePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->native(false),

            TimePicker::make('hora_inicio')
                ->label('Hora inicio')
                ->native(false),

            TimePicker::make('hora_fin')
                ->label('Hora fin')
                ->native(false),

            TextInput::make('duracion_minutos')
                ->label('Duración (minutos)')
                ->numeric()
                ->nullable(),

            Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nombre_cuenta')
                ->searchable()
                ->nullable()
                ->native(false),

            Textarea::make('resultado')
                ->label('Resultado / Notas')
                ->rows(3)
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'llamada' => 'info',
                        'reunion' => 'warning',
                        'email' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Hora')
                    ->time('H:i'),

                TextColumn::make('cliente.nombre_cuenta')
                    ->label('Cliente')
                    ->placeholder('Sin cliente'),

                TextColumn::make('user.name')
                    ->label('Registrado por'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
