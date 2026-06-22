<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserPermissionResource\Pages;
use App\Filament\Traits\AuthorizedResource;
use App\Models\UserPermission;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserPermissionResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = UserPermission::class;

    protected static ?string $navigationLabel = 'Excepciones de permisos';

    protected static ?string $modelLabel = 'Excepción de permiso';

    protected static ?string $pluralModelLabel = 'Excepciones de permisos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::LockOpen;

    protected static ?int $navigationSort = 8;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static function getPermissionKey(): string
    {
        return 'user_permissions';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('recurso')
                    ->label('Recurso')
                    ->options([
                        'tasks' => 'Tareas',
                        'noticias' => 'Noticias',
                        'users' => 'Usuarios',
                        'oportunidades' => 'Oportunidades',
                        'clientes' => 'Clientes',
                        'paymentrequests' => 'Pedidos de fondos',
                        'actividades' => 'Actividades',
                        'areas' => 'Areas',
                        'tipo_cierres' => 'Tipos de cierre',
                        'tipo_tareas' => 'Tipos de tarea',
                    ])
                    ->required(),

                Select::make('accion')
                    ->label('Acción/Estado')
                    ->options([
                        'ver' => 'Ver',
                        'editar' => 'Editar',
                        'oculto' => 'Oculto (No visible)',
                    ])
                    ->required()
                    ->helperText('Selecciona "Oculto" para que el recurso no sea visible para este usuario'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),

                TextColumn::make('recurso')
                    ->label('Recurso')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tasks' => 'Tareas',
                        'noticias' => 'Noticias',
                        'users' => 'Usuarios',
                        'oportunidades' => 'Oportunidades',
                        'clientes' => 'Clientes',
                        'paymentrequests' => 'Pedidos de fondos',
                        'actividades' => 'Actividades',
                        'areas' => 'Areas',
                        'tipo_cierres' => 'Tipos de cierre',
                        'tipo_tareas' => 'Tipos de tarea',
                        default => $state,
                    }),

                TextColumn::make('accion')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ver' => 'info',
                        'editar' => 'warning',
                        'oculto' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('recurso')
                    ->label('Recurso')
                    ->options([
                        'tasks' => 'Tareas',
                        'noticias' => 'Noticias',
                        'users' => 'Usuarios',
                        'oportunidades' => 'Oportunidades',
                        'clientes' => 'Clientes',
                        'paymentrequests' => 'Pedidos de fondos',
                        'actividades' => 'Actividades',
                        'areas' => 'Areas',
                        'tipo_cierres' => 'Tipos de cierre',
                        'tipo_tareas' => 'Tipos de tarea',
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserPermissions::route('/'),
            'create' => Pages\CreateUserPermission::route('/create'),
            'edit' => Pages\EditUserPermission::route('/{record}/edit'),
        ];
    }
}
