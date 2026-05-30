<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'Roles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::LockClosed;

    protected static ?int $navigationSort = 6;

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
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull(),

                CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->recurso} - {$record->accion}")
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50),

                TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
