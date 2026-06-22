<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoCierreResource\Pages\CreateTipoCierre;
use App\Filament\Resources\TipoCierreResource\Pages\EditTipoCierre;
use App\Filament\Resources\TipoCierreResource\Pages\ListTipoCierres;
use App\Filament\Traits\AuthorizedResource;
use App\Models\TipoCierre;
use BackedEnum;
use Filament\Forms\Components\TextInput;
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

    protected static ?string $modelLabel = 'Tipo de cierre';

    protected static ?string $pluralModelLabel = 'Tipos de cierre';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ExclamationTriangle;

    protected static ?int $navigationSort = 11;

    protected static function getPermissionKey(): string
    {
        return 'tipo_cierres';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
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
            'index' => ListTipoCierres::route('/'),
            'create' => CreateTipoCierre::route('/create'),
            'edit' => EditTipoCierre::route('/{record}/edit'),
        ];
    }
}
