<?php

namespace App\Policies;

use App\Models\TipoTarea;
use App\Models\User;

class TipoTareaPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdminLike($user) || $user->canViewResource('tipo_tareas');
    }

    public function view(User $user, TipoTarea $record): bool
    {
        return $this->isAdminLike($user) || $user->canViewResource('tipo_tareas');
    }

    public function create(User $user): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_tareas');
    }

    public function update(User $user, TipoTarea $record): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_tareas');
    }

    public function delete(User $user, TipoTarea $record): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_tareas');
    }

    private function isAdminLike(User $user): bool
    {
        return $user->role?->nombre === 'admin'
            || (bool) $user->puede_autorizar
            || (bool) $user->puede_realizar_pago
            || (bool) $user->puede_realizar_transferencia;
    }
}
