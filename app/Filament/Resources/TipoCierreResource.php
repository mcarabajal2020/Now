<?php

namespace App\Filament\Resources;

use App\Filament\Traits\AuthorizedResource;
use App\Models\TipoCierre;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TipoCierreResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = TipoCierre::class;

    protected static ?string $navigationLabel = 'Tipos de cierre';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ExclamationTriangle;

    protected static ?int $navigationSort = 11;

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
            'index' => \App\Filament\Resources\TipoCierreResource\Pages\ListTipoCierres::route('/'),
            'create' => \App\Filament\Resources\TipoCierreResource\Pages\CreateTipoCierre::route('/create'),
            'edit' => \App\Filament\Resources\TipoCierreResource\Pages\EditTipoCierre::route('/{record}/edit'),
        ];
    }
}
