<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActividadResource\Pages;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Actividad;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActividadResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Actividad::class;

    protected static ?string $navigationLabel = 'Actividades';

    protected static ?string $modelLabel = 'Actividad';

    protected static ?string $pluralModelLabel = 'Actividades';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?int $navigationSort = 5;

    protected static function getPermissionKey(): string
    {
        return 'actividades';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'llamada' => 'Llamada',
                    'reunion' => 'Reunion',
                    'email' => 'Email',
                ])
                ->required()
                ->native(false),

            TextInput::make('titulo')
                ->label('Titulo')
                ->required()
                ->maxLength(255),

            Textarea::make('descripcion')
                ->label('Descripcion')
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
                ->label('Duracion (minutos)')
                ->numeric()
                ->nullable(),

            Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nombre_cuenta')
                ->searchable()
                ->nullable()
                ->native(false),

            Select::make('oportunidad_id')
                ->label('Oportunidad')
                ->relationship('oportunidad', 'nombre')
                ->searchable()
                ->nullable()
                ->native(false),

            Textarea::make('resultado')
                ->label('Resultado / Notas')
                ->rows(3)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
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
                    ->label('Titulo')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Hora')
                    ->time('H:i'),

                TextColumn::make('cliente.nombre_cuenta')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Sin cliente'),

                TextColumn::make('oportunidad.nombre')
                    ->label('Oportunidad')
                    ->searchable()
                    ->placeholder('Sin oportunidad'),

                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m H:i')
                    ->sortable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'llamada' => 'Llamada',
                        'reunion' => 'Reunion',
                        'email' => 'Email',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActividades::route('/'),
            'create' => Pages\CreateActividad::route('/create'),
            'edit' => Pages\EditActividad::route('/{record}/edit'),
        ];
    }
}
