<?php

namespace App\Filament\Resources\ActividadResource\Pages;

use App\Filament\Resources\ActividadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActividades extends ListRecords
{
    protected static string $resource = ActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Registrar actividad'),
        ];
    }
}
