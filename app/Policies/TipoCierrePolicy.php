<?php

namespace App\Policies;

use App\Models\TipoCierre;
use App\Models\User;

class TipoCierrePolicy
{
    private function isAdminLike(User $user): bool
    {
        return $user->role?->nombre === 'admin'
            || (bool) $user->puede_autorizar
            || (bool) $user->puede_realizar_pago
            || (bool) $user->puede_realizar_transferencia;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdminLike($user) || $user->canViewResource('tipo_cierres');
    }

    public function view(User $user, TipoCierre $record): bool
    {
        return $this->isAdminLike($user) || $user->canViewResource('tipo_cierres');
    }

    public function create(User $user): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_cierres');
    }

    public function update(User $user, TipoCierre $record): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_cierres');
    }

    public function delete(User $user, TipoCierre $record): bool
    {
        return $this->isAdminLike($user) || $user->canEditResource('tipo_cierres');
    }
}
