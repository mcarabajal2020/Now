<?php

namespace App\Filament\Resources\Noticias\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NoticiaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Noticia')
                    ->schema([
                        TextEntry::make('titulo')
                            ->label('Título')
                            ->columnSpanFull(),

                        TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),

                        ImageEntry::make('imagenes')
                            ->label('Imágenes')
                            ->disk('public')
                            ->visibility('public')
                            ->imageHeight(160)
                            ->columnSpanFull(),

                        TextEntry::make('link')
                            ->label('Link')
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab()
                            ->placeholder('Sin link'),
                    ])
                    ->columns(2),

                Section::make('Auditoría')
                    ->schema([
                        TextEntry::make('creador.name')
                            ->label('Creado por'),

                        TextEntry::make('created_at')
                            ->label('Fecha de creación')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
