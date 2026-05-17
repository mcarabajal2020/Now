<?php

namespace App\Filament\Traits;

trait AuthorizedResource
{
    /**
     * Verificar si el usuario autenticado puede ver el recurso.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Usar la policy de Laravel
        $model = static::$model;

        return $user->can('viewAny', $model);
    }

    /**
     * Verificar si el usuario autenticado puede crear un recurso.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Usar la policy de Laravel
        $model = static::$model;

        return $user->can('create', $model);
    }
}
