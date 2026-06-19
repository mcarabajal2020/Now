<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoTareaResource\Pages;
use App\Models\TipoTarea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TipoTareaResource extends Resource
{
    protected static ?string $model = TipoTarea::class;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->role?->permissions()
            ->where('recurso', 'tipo_tareas')
            ->where('accion', 'ver')
            ->exists() ?? false;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->role?->permissions()
            ->where('recurso', 'tipo_tareas')
            ->where('accion', 'editar')
            ->exists() ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?int $navigationSort = 10;


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('created_at')->label('Creado')->dateTime(),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoTareas::route('/'),
            'create' => Pages\CreateTipoTarea::route('/create'),
            'edit' => Pages\EditTipoTarea::route('/{record}/edit'),
        ];
    }
}

