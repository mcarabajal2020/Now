<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin puede ver todos los tasks
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->canViewResource('tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Admin puede ver todos los tasks
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        // Si el task está asignado a un área y el usuario pertenece a la misma área
        if ($task->area_id && $user->area_id && $task->area_id === $user->area_id) {
            return true;
        }

        return $user->canViewResource('tasks');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin puede crear tasks
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        return $user->canEditResource('tasks');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Admin puede editar todos los tasks
        if ($user->role?->nombre === 'admin') {
            return true;
        }

        // Si el task está asignado a un área y el usuario pertenece a la misma área, permitir edición solo si el rol/permiso lo permite
        if ($task->area_id && $user->area_id && $task->area_id === $user->area_id) {
            return $user->canEditResource('tasks');
        }

        return $user->canEditResource('tasks');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->canEditResource('tasks');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->canEditResource('tasks');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->canEditResource('tasks');
    }
}
