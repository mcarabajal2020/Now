<?php

namespace App\Filament\Resources\ActividadResource\Pages;

use App\Filament\Resources\ActividadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActividad extends CreateRecord
{
    protected static string $resource = ActividadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
