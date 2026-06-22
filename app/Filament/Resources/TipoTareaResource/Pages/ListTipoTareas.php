<?php

namespace App\Filament\Resources\TipoTareaResource\Pages;

use App\Filament\Resources\TipoTareaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoTareas extends ListRecords
{
    protected static string $resource = TipoTareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Crear'),
        ];
    }
}
