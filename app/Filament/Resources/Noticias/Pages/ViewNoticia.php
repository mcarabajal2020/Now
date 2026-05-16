<?php

namespace App\Filament\Resources\Noticias\Pages;

use App\Filament\Resources\Noticias\NoticiaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNoticia extends ViewRecord
{
    protected static string $resource = NoticiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
