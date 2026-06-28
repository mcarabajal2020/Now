<?php

namespace App\Filament\Resources\Noticias\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NoticiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),

                FileUpload::make('imagenes')
                    ->label('Imágenes')
                    ->disk('public')
                    ->directory('noticias')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->imageEditor()
                    ->maxFiles(5)
                    ->maxSize(4096)
                    ->columnSpanFull(),

                TextInput::make('link')
                    ->label('Enlace')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
