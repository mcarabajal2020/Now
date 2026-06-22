<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Filament\Resources\PaymentRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentRequest extends ViewRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn (): bool => $this->record->estado !== 'terminado'),
        ];
    }
}
