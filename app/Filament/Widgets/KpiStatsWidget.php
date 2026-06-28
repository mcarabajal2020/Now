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
        $tareasAbiertas = Task::query()
            ->whereIn('estado', ['Nuevo', 'En Proceso'])
            ->count();

        $tareasFinalizadasHoy = Task::query()
            ->where('estado', 'Finalizado')
            ->whereDate('fecha_finalizacion', now()->toDateString())
            ->count();

        $tareasFinalizadasSemana = Task::query()
            ->where('estado', 'Finalizado')
            ->where('fecha_finalizacion', '>=', now()->subWeek())
            ->count();

        $oportunidadesAbiertas = Opportunity::query()
            ->abiertas()
            ->count();

        $montoPipeline = Opportunity::query()
            ->abiertas()
            ->sum('monto_estimado');

        $oportunidadesGanadas = Opportunity::query()
            ->ganadas()
            ->count();

        $montoGanado = Opportunity::query()
            ->ganadas()
            ->sum('monto_estimado');

        $pagosPendientes = PaymentRequest::query()
            ->whereIn('estado', ['pendiente_autorizacion', 'pendiente_pago', 'pendiente_transferencia'])
            ->count();

        $montoPendiente = PaymentRequest::query()
            ->whereIn('estado', ['pendiente_autorizacion', 'pendiente_pago', 'pendiente_transferencia'])
            ->sum('monto');

        $totalClientes = Cliente::count();

        return [
            Stat::make('Tareas abiertas', $tareasAbiertas)
                ->description($tareasFinalizadasHoy.' finalizados hoy')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($tareasAbiertas > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.tasks.index')),

            Stat::make('Finalizados (7d)', $tareasFinalizadasSemana)
                ->description('Últimos 7 días')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Pipeline', '$'.number_format($montoPipeline, 0, ',', '.'))
                ->description($oportunidadesAbiertas.' oportunidades abiertas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->url(route('filament.admin.resources.opportunities.index')),

            Stat::make('Ganadas', $oportunidadesGanadas)
                ->description('$'.number_format($montoGanado, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make('Pagos pendientes', $pagosPendientes)
                ->description('$'.number_format($montoPendiente, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pagosPendientes > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.payment-requests.index')),

            Stat::make('Clientes', $totalClientes)
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.admin.resources.clientes.index')),
        ];
    }
}
