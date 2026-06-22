<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->canEditResource('tasks');
    }
}
