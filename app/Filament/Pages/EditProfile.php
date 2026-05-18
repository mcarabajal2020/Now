<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;

class EditProfile extends Page
{
    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 999;

    protected string $view = 'filament.pages.edit-profile';

    #[Validate('required|string|max:255')]
    public ?string $name = null;

    #[Validate('required|email|max:255')]
    public ?string $email = null;

    public ?string $fecha_nacimiento = null;

    public ?string $foto_perfil = null;

    #[Validate('nullable|string|min:8')]
    public ?string $password = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->fecha_nacimiento = $user->fecha_nacimiento;
        $this->foto_perfil = $user->foto_perfil;
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
        ];

        if (filled($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Tu perfil ha sido actualizado correctamente.')
            ->send();
    }
}





