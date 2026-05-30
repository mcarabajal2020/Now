<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserPermissionResource\Pages;
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
    protected static ?string $model = UserPermission::class;

    protected static ?string $navigationLabel = 'Excepciones de Permisos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::LockOpen;

    protected static ?int $navigationSort = 8;

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
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('recurso')
                    ->label('Recurso')
                    ->options([
                        'tasks' => 'Tasks',
                        'noticias' => 'Noticias',
                        'users' => 'Users',
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
                    ->badge(),

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
                    ->options([
                        'tasks' => 'Tasks',
                        'noticias' => 'Noticias',
                        'users' => 'Users',
                    ]),
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
            'index' => Pages\ListUserPermissions::route('/'),
            'create' => Pages\CreateUserPermission::route('/create'),
            'edit' => Pages\EditUserPermission::route('/{record}/edit'),
        ];
    }
}
