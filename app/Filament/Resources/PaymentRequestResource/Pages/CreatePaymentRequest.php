<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Filament\Resources\PaymentRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentRequest extends CreateRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['solicitante_id'] = auth()->id();
        $data['estado'] = 'pendiente_autorizacion';

        return $data;
    }
}
