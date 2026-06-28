<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Filament\Resources\PaymentRequestResource;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestLog;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRequest extends EditRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Grabar');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (PaymentRequestResource::canUpdateRequestDetails($this->record)) {
            return $data;
        }

        return array_diff_key($data, array_flip([
            'cliente_id',
            'numero_cuenta',
            'nombre_cuenta',
            'cliente_cbu_id',
            'monto',
        ]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('authorize')
                ->label('Autorizar')
                ->visible(fn () => auth()->user()?->puede_autorizar && $this->record->estado === 'pendiente_autorizacion')
                ->action(function () {
                    /** @var PaymentRequest $record */
                    $record = $this->record;
                    $record->update([
                        'estado' => 'pendiente_pago',
                        'autorizado_por_id' => auth()->id(),
                        'autorizado_at' => now(),
                    ]);
                    PaymentRequestLog::create([
                        'payment_request_id' => $record->id,
                        'event' => 'autorizado',
                        'user_id' => auth()->id(),
                        'message' => null,
                        'created_at' => now(),
                    ]);
                    Notification::make()
                        ->success()
                        ->title('Pedido autorizado')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            Action::make('mark_paid')
                ->label('Pago realizado')
                ->visible(fn () => auth()->user()?->puede_realizar_pago && $this->record->estado === 'pendiente_pago')
                ->action(function () {
                    $record = $this->record;
                    $record->update([
                        'estado' => 'pendiente_transferencia',
                        'pagado_por_id' => auth()->id(),
                        'pagado_at' => now(),
                    ]);
                    PaymentRequestLog::create([
                        'payment_request_id' => $record->id,
                        'event' => 'pagado',
                        'user_id' => auth()->id(),
                        'message' => null,
                        'created_at' => now(),
                    ]);
                    Notification::make()
                        ->success()
                        ->title('Pago registrado')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            Action::make('transfer')
                ->label('Transferencia realizada')
                ->visible(fn () => auth()->user()?->puede_realizar_transferencia && $this->record->estado === 'pendiente_transferencia')
                ->action(function () {
                    $record = $this->record;
                    $record->update([
                        'estado' => 'terminado',
                        'transferido_por_id' => auth()->id(),
                        'transferido_at' => now(),
                    ]);
                    PaymentRequestLog::create([
                        'payment_request_id' => $record->id,
                        'event' => 'transferido',
                        'user_id' => auth()->id(),
                        'message' => null,
                        'created_at' => now(),
                    ]);
                    Notification::make()
                        ->success()
                        ->title('Transferencia registrada')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
                }),
        ];
    }
}
