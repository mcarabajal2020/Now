<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\PaymentRequestResource;
use App\Filament\Resources\Tasks\TaskResource as TasksFilamentResource;
use BackedEnum;
use App\Models\Cliente;
use App\Models\PaymentRequest;
use App\Models\Task;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HistorialClientes extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

    protected static ?string $navigationLabel = 'Historial';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.pages.historial-clientes';

    public Model|string|int|null $record = null;

    public function getRecord(): Model
    {
        if ($this->record instanceof Model) {
            return $this->record;
        }

        return Cliente::findOrFail($this->record);
    }

    public function getTasks(): Collection
    {
        $clienteId = $this->getRecord()?->id;
        if (! $clienteId) {
            return collect();
        }

        return Task::query()
            ->where('cliente_id', $clienteId)
            ->orderByDesc('ultima_modificacion')
            ->orderByDesc('fecha_creacion')
            ->get()
            ->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'titulo' => $task->titulo,
                    'detalle' => $task->detalle,
                    'estado' => $task->estado,
                    'fecha' => $task->ultima_modificacion ?? $task->fecha_creacion,
                    // opcional: link a recurso de tareas
                    'url' => TasksFilamentResource::getUrl('edit', ['record' => $task->id]),
                ];
            });
    }

    public function getPaymentRequests(): Collection
    {
        $clienteId = $this->getRecord()?->id;
        if (! $clienteId) {
            return collect();
        }

        return PaymentRequest::query()
            ->where('cliente_id', $clienteId)
            ->orderByDesc('fecha_pago')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (PaymentRequest $payment) {
                return [
                    'id' => $payment->id,
                    'titulo' => 'Pedido de fondos',
                    'detalle' => $payment->observaciones,
                    'estado' => $payment->estado,
                    'fecha' => $payment->fecha_pago ?? $payment->created_at,
                    'url' => PaymentRequestResource::getUrl('view', ['record' => $payment->id]),
                ];
            });
    }
}

