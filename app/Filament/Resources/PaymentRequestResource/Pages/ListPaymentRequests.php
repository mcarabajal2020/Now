<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Exports\PaymentRequestsExport;
use App\Filament\Resources\PaymentRequestResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export_xlsx')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $filename = 'pedidos-de-fondos-'.now()->format('Y-m-d_H-i-s').'.xlsx';

                    return Excel::download(new PaymentRequestsExport($this->getTable()->getQuery()), $filename);
                }),
        ];
    }
}

