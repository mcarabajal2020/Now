<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationLabel = 'Permisos';

    public static function canViewAny(): bool
    {
        return auth()->user()?->role?->nombre === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role?->nombre === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recurso')
                    ->label('Recurso')
                    ->options([
                        'tasks' => 'Tasks',
                        'noticias' => 'Noticias',
                        'users' => 'Users',
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
                    ->searchable(),

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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
