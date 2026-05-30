<?php

namespace App\Filament\Resources\PaymentRequestResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $recordTitleAttribute = 'event';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')->label('Evento'),
                TextColumn::make('user.name')->label('Usuario'),
                TextColumn::make('message')->label('Mensaje')->wrap(),
                TextColumn::make('created_at')->label('Fecha')->dateTime(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
