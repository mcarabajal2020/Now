<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('foto_perfil')
                    ->label('Foto de perfil')
                    ->disk('public')
                    ->directory('profile-photos')
                    ->avatar()
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->maxLength(255),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->native(false)
                    ->maxDate(now()),

                Select::make('role_id')
                    ->label('Rol')
                    ->relationship('role', 'nombre')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->nullable()
                    ->maxLength(255)
                    ->revealable(),
            ]);
    }
}
