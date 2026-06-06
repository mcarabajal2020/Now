<?php

namespace App\Exports;

use App\Models\PaymentRequest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PaymentRequestsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    /** @var Builder */
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Número de cuenta',
            'Tipo de cuenta',
            'CBU',
            'Nombre de cuenta',
            'Monto',
            'Solicitante',
            'Estado',
            'Autorizó',
            'Pagó',
            'Transfirió',
            'Fecha de pago',
            'Observaciones',
            'Observaciones (pago)',
            'Total pagado',
            'Creado',
            'Actualizado',
        ];
    }

    public function map($paymentRequest): array
    {
        return [
            $paymentRequest->cliente?->numero_cuenta ?? $paymentRequest->numero_cuenta,
            $paymentRequest->clienteCbu?->tipo_cbu,
            $paymentRequest->clienteCbu?->cbu,
            $paymentRequest->cliente?->nombre_cuenta ?? $paymentRequest->nombre_cuenta,
            $paymentRequest->monto,
            $paymentRequest->solicitante?->name,
            $paymentRequest->estado,
            $paymentRequest->autorizadoPor?->name,
            $paymentRequest->pagadoPor?->name,
            $paymentRequest->transferidoPor?->name,
            optional($paymentRequest->fecha_pago)?->format('Y-m-d H:i:s'),
            $paymentRequest->observaciones,
            $paymentRequest->observaciones_pago,
            $paymentRequest->total_pagado,
            optional($paymentRequest->created_at)?->format('Y-m-d H:i:s'),
            optional($paymentRequest->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DATETIME, // Fecha de pago
            'M' => NumberFormat::FORMAT_DATE_DATETIME, // Creado
            'N' => NumberFormat::FORMAT_DATE_DATETIME, // Actualizado
        ];
    }
}

