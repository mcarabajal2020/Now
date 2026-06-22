<?php

namespace App\Filament\Traits;

use App\Models\User;

trait AuthorizedResource
{
    abstract protected static function getPermissionKey(): string;

    public static function canViewAny(): bool
    {
        return static::checkPermission('ver');
    }

    public static function canCreate(): bool
    {
        return static::checkPermission('editar');
    }

    public static function canEdit(mixed $record): bool
    {
        return static::checkPermission('editar');
    }

    public static function canDelete(mixed $record): bool
    {
        return static::checkPermission('editar');
    }

    public static function canView(mixed $record): bool
    {
        return static::checkPermission('ver');
    }

    protected static function checkPermission(string $accion): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->role?->nombre === 'admin') {
            return true;
        }

        $recurso = static::getPermissionKey();

        return match ($accion) {
            'ver' => $user->canViewResource($recurso),
            'editar' => $user->canEditResource($recurso),
            default => false,
        };
    }
}
