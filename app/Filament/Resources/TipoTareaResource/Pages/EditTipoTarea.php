<?php

namespace App\Filament\Resources\TipoTareaResource\Pages;

use App\Filament\Resources\TipoTareaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTipoTarea extends EditRecord
{
    protected static string $resource = TipoTareaResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }
}
