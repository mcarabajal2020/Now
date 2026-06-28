<?php

namespace App\Filament\Resources\TipoCierreResource\Pages;

use App\Filament\Resources\TipoCierreResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTipoCierre extends EditRecord
{
    protected static string $resource = TipoCierreResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }
}
