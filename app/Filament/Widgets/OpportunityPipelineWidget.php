<?php

namespace App\Filament\Widgets;

use App\Models\Opportunity;
use Filament\Widgets\ChartWidget;

class OpportunityPipelineWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Pipeline de oportunidades';

    protected ?string $description = 'Monto estimado por etapa';

    protected string $color = 'primary';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role?->nombre === 'admin';

        $query = Opportunity::query()->abiertas();

        if (! $isAdmin) {
            $query->where('user_id', $user->id);
        }

        $etapas = [
            'prospeccion' => 'Prospeccion',
            'calificacion' => 'Calificacion',
            'propuesta' => 'Propuesta',
            'negociacion' => 'Negociacion',
        ];

        $labels = [];
        $data = [];

        foreach ($etapas as $key => $label) {
            $labels[] = $label;
            $data[] = (clone $query)->etapa($key)->sum('monto_estimado');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monto estimado',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(239, 68, 68)',
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}
