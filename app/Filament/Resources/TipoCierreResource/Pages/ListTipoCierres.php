<?php

namespace App\Filament\Resources\TipoCierreResource\Pages;

use App\Filament\Resources\TipoCierreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoCierres extends ListRecords
{
    protected static string $resource = TipoCierreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Crear'),
        ];
    }
}
