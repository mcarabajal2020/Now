<?php

namespace App\Filament\Resources\TipoCierreResource\Pages;

use App\Filament\Resources\TipoCierreResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTipoCierre extends CreateRecord
{
    protected static string $resource = TipoCierreResource::class;

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
