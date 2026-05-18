<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 999;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        if ($user) {
            $this->form->fill([
                'name' => $user->name,
                'email' => $user->email,
                'fecha_nacimiento' => $user->fecha_nacimiento,
                'foto_perfil' => $user->foto_perfil,
            ]);
        }
    }

    protected function getFormSchema(): array
    {
        return [
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

            TextInput::make('password')
                ->label('Contraseña (opcional)')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->nullable()
                ->maxLength(255)
                ->revealable(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        auth()->user()->update($data);

        Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Tu perfil ha sido actualizado correctamente.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar cambios')
                ->submit('save'),
        ];
    }
}
