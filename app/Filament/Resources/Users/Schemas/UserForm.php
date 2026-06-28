<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    private static array $recursos = [
        'tasks' => 'Tareas',
        'noticias' => 'Noticias',
        'users' => 'Usuarios',
        'oportunidades' => 'Oportunidades',
        'clientes' => 'Clientes',
        'paymentrequests' => 'Pedidos de fondos',
        'actividades' => 'Actividades',
        'areas' => 'Áreas',
        'tipo_tareas' => 'Tipos de tarea',
        'tipo_cierres' => 'Tipos de cierre',
    ];

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
                    ->label('Correo')
                    ->required()
                    ->email()
                    ->maxLength(255),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->native(false)
                    ->maxDate(now()),

                CheckboxList::make('areas')
                    ->label('Áreas')
                    ->relationship('areas', 'nombre')
                    ->searchable(),

                TextInput::make('password')
                    ->label('Contraseña')
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

                Section::make('Permisos por recurso')
                    ->description('Define que puede ver, editar u ocultar este usuario. Si no se asigna ninguno, se usa el rol.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                CheckboxList::make('permisos_ver')
                                    ->label('Puede ver')
                                    ->options(self::$recursos)
                                    ->afterStateHydrated(function ($component, $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        $ver = $record->userPermissions()
                                            ->where('accion', 'ver')
                                            ->pluck('recurso')
                                            ->toArray();

                                        $component->state($ver);
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        return $state;
                                    }),

                                CheckboxList::make('permisos_editar')
                                    ->label('Puede editar')
                                    ->options(self::$recursos)
                                    ->afterStateHydrated(function ($component, $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        $editar = $record->userPermissions()
                                            ->where('accion', 'editar')
                                            ->pluck('recurso')
                                            ->toArray();

                                        $component->state($editar);
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        return $state;
                                    }),

                                CheckboxList::make('permisos_oculto')
                                    ->label('Oculto (no visible)')
                                    ->options(self::$recursos)
                                    ->afterStateHydrated(function ($component, $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        $oculto = $record->userPermissions()
                                            ->where('accion', 'oculto')
                                            ->pluck('recurso')
                                            ->toArray();

                                        $component->state($oculto);
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        return $state;
                                    }),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }
}
