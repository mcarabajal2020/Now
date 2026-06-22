<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Opportunity;
use App\Models\PaymentRequest;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KpiStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role?->nombre === 'admin';

        $tareasQuery = Task::query();
        $oportunidadesQuery = Opportunity::query();
        $pagosQuery = PaymentRequest::query();

        if (! $isAdmin) {
            $tareasQuery->where(function ($q) use ($user) {
                $q->where('usuario_solicita_id', $user->id)
                    ->orWhere('asignado_a_id', $user->id);
            });
            $oportunidadesQuery->where('user_id', $user->id);
            $pagosQuery->where('solicitante_id', $user->id);
        }

        $tareasAbiertas = (clone $tareasQuery)
            ->whereIn('estado', ['Nuevo', 'En Proceso'])
            ->count();

        $tareasFinalizadasHoy = (clone $tareasQuery)
            ->where('estado', 'Finalizado')
            ->whereDate('fecha_finalizacion', now()->toDateString())
            ->count();

        $tareasFinalizadasSemana = (clone $tareasQuery)
            ->where('estado', 'Finalizado')
            ->where('fecha_finalizacion', '>=', now()->subWeek())
            ->count();

        $oportunidadesAbiertas = (clone $oportunidadesQuery)
            ->abiertas()
            ->count();

        $montoPipeline = (clone $oportunidadesQuery)
            ->abiertas()
            ->sum('monto_estimado');

        $oportunidadesGanadas = (clone $oportunidadesQuery)
            ->ganadas()
            ->count();

        $montoGanado = (clone $oportunidadesQuery)
            ->ganadas()
            ->sum('monto_estimado');

        $pagosPendientes = (clone $pagosQuery)
            ->whereIn('estado', ['pendiente_autorizacion', 'pendiente_pago', 'pendiente_transferencia'])
            ->count();

        $montoPendiente = (clone $pagosQuery)
            ->whereIn('estado', ['pendiente_autorizacion', 'pendiente_pago', 'pendiente_transferencia'])
            ->sum('monto');

        $totalClientes = Cliente::count();

        return [
            Stat::make('Tareas abiertas', $tareasAbiertas)
                ->description($tareasFinalizadasHoy.' finalizadas hoy')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($tareasAbiertas > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.tasks.index')),

            Stat::make('Finalizadas (7d)', $tareasFinalizadasSemana)
                ->description('Ultimos 7 dias')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Oportunidades abiertas', $oportunidadesAbiertas)
                ->description('$'.number_format($montoPipeline, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->url(route('filament.admin.resources.opportunities.index')),

            Stat::make('Oportunidades ganadas', $oportunidadesGanadas)
                ->description('$'.number_format($montoGanado, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pagos pendientes', $pagosPendientes)
                ->description('$'.number_format($montoPendiente, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pagosPendientes > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.payment-requests.index')),

            Stat::make('Clientes', $totalClientes)
                ->description(' Registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.admin.resources.clientes.index')),
        ];
    }
}
