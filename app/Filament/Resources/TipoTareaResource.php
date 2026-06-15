<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoTareaResource\Pages;
use App\Filament\Traits\AuthorizedResource;
use App\Models\TipoTarea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TipoTareaResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = TipoTarea::class;

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

