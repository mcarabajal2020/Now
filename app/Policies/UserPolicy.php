<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->nombre === 'admin';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role?->nombre === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role?->nombre === 'admin';
    }

    public function update(User $user, User $model): bool
    {
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role?->nombre === 'admin';
    }
}
