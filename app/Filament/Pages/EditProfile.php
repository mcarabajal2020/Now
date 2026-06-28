<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 999;

    protected string $view = 'filament.pages.edit-profile';

    public ?array $data = [];

    public ?string $name = null;

    public ?string $email = null;

    public ?string $fecha_nacimiento = null;

    public ?array $foto_perfil = null;

    public ?string $password = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->fecha_nacimiento = $user->fecha_nacimiento;
        $this->foto_perfil = $user->foto_perfil ? [$user->foto_perfil] : null;

        $this->form->fill([
            'name' => $this->name,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'foto_perfil' => $this->foto_perfil,
        ]);
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
                ->maxSize(2048)
                ->columnSpanFull(),

            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Correo')
                ->email()
                ->required()
                ->maxLength(255),

            DatePicker::make('fecha_nacimiento')
                ->label('Fecha de nacimiento')
                ->native(false)
                ->maxDate(now()),

            TextInput::make('password')
                ->label('Contraseña (opcional)')
                ->password()
                ->minLength(8)
                ->nullable()
                ->maxLength(255)
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state)),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        // Manejar foto de perfil - extraer la ruta si es un array
        if (is_array($data['foto_perfil']) && count($data['foto_perfil']) > 0) {
            $data['foto_perfil'] = $data['foto_perfil'][0];
        } elseif (empty($data['foto_perfil'])) {
            unset($data['foto_perfil']);
        }

        // Actualizar contraseña si se proporciona
        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Actualizar propiedades
        $this->name = $user->name;
        $this->email = $user->email;
        $this->fecha_nacimiento = $user->fecha_nacimiento;
        $this->foto_perfil = $user->foto_perfil ? [$user->foto_perfil] : null;

        Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Tu perfil ha sido actualizado correctamente.')
            ->send();

        // Recargar el formulario
        $this->form->fill([
            'name' => $this->name,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'foto_perfil' => $this->foto_perfil,
        ]);
    }
}
