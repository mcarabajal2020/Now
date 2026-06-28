<?php

namespace App\Filament\Resources\TipoTareaResource\Pages;

use App\Filament\Resources\TipoTareaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoTarea extends CreateRecord
{
    protected static string $resource = TipoTareaResource::class;

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
