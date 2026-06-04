<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TasksExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'Título',
            'Estado',
            'Detalle',
            'Solicita',
            'Asignado',
            'Área',
            'Finalización',
            'Uso',
            'Tipo de tarea',
            'Prioridad',
            'Cliente (cuenta)',
            'Última modificación',
        ];
    }

    public function map($task): array
    {
        return [
            $task->titulo,
            $task->estado,
            $task->detalle,
            $task->solicitante?->name,
            $task->asignadoA?->name,
            $task->area?->nombre,
            optional($task->fecha_finalizacion)?->format('Y-m-d H:i:s'),
            $task->tipo_uso,
            $task->tipo_tarea,
            $task->prioridad,
            $task->cliente?->numero_cuenta,
            optional($task->ultima_modificacion)?->format('Y-m-d H:i:s'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Fecha finalización
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
            // Última modificación
            'L' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}

