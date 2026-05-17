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

        $model = static::$model;
        $resourceName = class_basename($model);
        $resourceKey = strtolower($resourceName);

        // Mapear nombres de modelos a permisos
        $permissionMap = [
            'task' => 'tasks',
            'noticia' => 'noticias',
            'user' => 'users',
        ];

        $recurso = $permissionMap[$resourceKey] ?? $resourceKey;

        return $user->canViewResource($recurso);
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

        $model = static::$model;
        $resourceName = class_basename($model);
        $resourceKey = strtolower($resourceName);

        $permissionMap = [
            'task' => 'tasks',
            'noticia' => 'noticias',
            'user' => 'users',
        ];

        $recurso = $permissionMap[$resourceKey] ?? $resourceKey;

        return $user->canEditResource($recurso);
    }
}
