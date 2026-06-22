<?php

namespace App\Filament\Widgets;

use App\Models\PaymentRequest;
use Filament\Widgets\Widget;

class PaymentCashTotalWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.payment-cash-total';

    public ?string $fecha = null;

    public ?float $totalPendiente = 0;

    public int $countPendientes = 0;

    protected static string $pollingInterval = '0s';

    public function mount(): void
    {
        $this->fecha = now()->addDay()->toDateString();
        $this->calcularTotal();
    }

    public function calcularTotal(): void
    {
        if (! $this->fecha) {
            $this->totalPendiente = 0;
            $this->countPendientes = 0;

            return;
        }

        $query = PaymentRequest::query()
            ->where('fecha_pago', '<=', $this->fecha)
            ->whereNotIn('estado', ['terminado', 'cancelado']);

        $this->totalPendiente = (float) $query->sum('monto');
        $this->countPendientes = (clone $query)->count();
    }

    public function updatedFecha(): void
    {
        $this->calcularTotal();
    }
}
