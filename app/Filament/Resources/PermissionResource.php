<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Permission;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Permission::class;

    protected static ?string $navigationLabel = 'Permisos';

    protected static ?string $modelLabel = 'Permiso';

    protected static ?string $pluralModelLabel = 'Permisos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::LockClosed;

    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static function getPermissionKey(): string
    {
        return 'permisos';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        'areas' => 'Áreas',
                        'tipo_cierres' => 'Tipos de cierre',
                        'tipo_tareas' => 'Tipos de tarea',
                    ])
                    ->required(),

                Select::make('accion')
                    ->label('Acción')
                    ->options([
                        'ver' => 'Ver',
                        'editar' => 'Editar',
                        'eliminar' => 'Eliminar',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recurso')
                    ->label('Recurso')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tasks' => 'Tareas',
                        'noticias' => 'Noticias',
                        'users' => 'Usuarios',
                        'oportunidades' => 'Oportunidades',
                        'clientes' => 'Clientes',
                        'paymentrequests' => 'Pedidos de fondos',
                        'actividades' => 'Actividades',
                        'areas' => 'Áreas',
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
                        'eliminar' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
