<?php

namespace App\Filament\Resources\UserPermissionResource\Pages;

use App\Filament\Resources\UserPermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserPermissions extends ListRecords
{
    protected static string $resource = UserPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Crear excepción'),
        ];
    }
}
