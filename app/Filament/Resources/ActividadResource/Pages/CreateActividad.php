<?php

namespace App\Filament\Resources\ActividadResource\Pages;

use App\Filament\Resources\ActividadResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateActividad extends CreateRecord
{
    protected static string $resource = ActividadResource::class;

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
