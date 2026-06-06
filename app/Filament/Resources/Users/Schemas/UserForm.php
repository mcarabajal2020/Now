<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

                Select::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'nombre')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->nullable()
                    ->maxLength(255)
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null),
                Toggle::make('puede_autorizar')
                    ->label('Puede autorizar pagos')
                    ->visible(fn () => auth()->user()?->role?->nombre === 'admin'),

                Toggle::make('puede_realizar_pago')
                    ->label('Puede marcar pago realizado')
                    ->visible(fn () => auth()->user()?->role?->nombre === 'admin'),

                Toggle::make('puede_realizar_transferencia')
                    ->label('Puede marcar transferencia realizada')
                    ->visible(fn () => auth()->user()?->role?->nombre === 'admin'),
            ]);
    }
}
