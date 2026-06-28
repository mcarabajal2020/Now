<?php

namespace App\Filament\Resources\UserPermissionResource\Pages;

use App\Filament\Resources\UserPermissionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUserPermission extends CreateRecord
{
    protected static string $resource = UserPermissionResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Grabar');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label('Grabar y crear otro');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }
}
