<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CbusRelationManager extends RelationManager
{
    protected static string $relationship = 'cbus';

    protected static ?string $recordTitleAttribute = 'cbu';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('banco')->label('Banco')->maxLength(255),
            TextInput::make('cbu')->label('CBU')->maxLength(255),
            Textarea::make('observaciones')->label('Observaciones')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('banco')->label('Banco'),
                TextColumn::make('cbu')->label('CBU'),
                TextColumn::make('observaciones')->label('Observaciones')->limit(50)->wrap(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
