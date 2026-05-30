<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'fecha_nacimiento', 'foto_perfil', 'password', 'role_id', 'puede_autorizar', 'puede_realizar_pago', 'puede_realizar_transferencia'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_nacimiento' => 'date',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    /**
     * Verificar si el usuario tiene permiso para una acción sobre un recurso.
     * Retorna 'ver', 'editar', o null
     */
    public function getPermission(string $recurso): ?string
    {
        // Verificar excepciones de usuario (tiene mayor prioridad)
        $userPermission = $this->userPermissions()
            ->where('recurso', $recurso)
            ->first();

        if ($userPermission && $userPermission->accion === 'oculto') {
            return null;
        }

        if ($userPermission && in_array($userPermission->accion, ['ver', 'editar'])) {
            return $userPermission->accion;
        }

        // Si no tiene rol, no tiene permisos
        if (! $this->role) {
            return null;
        }

        // Verificar permisos del rol
        // Ordenar por: editar > ver > otros (compatible con SQLite)
        $permission = $this->role->permissions()
            ->where('recurso', $recurso)
            ->orderByRaw("CASE WHEN accion = 'editar' THEN 1 WHEN accion = 'ver' THEN 2 ELSE 3 END")
            ->first();

        return $permission ? $permission->accion : null;
    }

    public function canViewResource(string $recurso): bool
    {
        $permission = $this->getPermission($recurso);

        return $permission !== null;
    }

    public function canEditResource(string $recurso): bool
    {
        $permission = $this->getPermission($recurso);

        return $permission === 'editar';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->foto_perfil)) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_perfil);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
