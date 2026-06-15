<?php

namespace App\Filament\Traits;

trait AuthorizedResource
{
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }
}
