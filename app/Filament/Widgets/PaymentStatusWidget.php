<?php

namespace App\Filament\Widgets;

use App\Models\PaymentRequest;
use Filament\Widgets\ChartWidget;

class PaymentStatusWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Estado de pedidos de fondos';

    protected ?string $description = 'Distribucion por estado';

    protected string $color = 'primary';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role?->nombre === 'admin';

        $query = PaymentRequest::query();

        if (! $isAdmin) {
            $query->where('solicitante_id', $user->id);
        }

        $estados = [
            'pendiente_autorizacion' => 'Pendiente autorizacion',
            'pendiente_pago' => 'Pendiente pago',
            'pendiente_transferencia' => 'Pendiente transferencia',
            'terminado' => 'Terminado',
            'cancelado' => 'Cancelado',
        ];

        $labels = [];
        $data = [];

        foreach ($estados as $key => $label) {
            $count = (clone $query)->where('estado', $key)->count();
            if ($count > 0) {
                $labels[] = $label;
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(156, 163, 175, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
