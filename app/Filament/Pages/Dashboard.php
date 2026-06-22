<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgendaWidget;
use App\Filament\Widgets\FinishedTasksLast7DaysWidget;
use App\Filament\Widgets\KpiStatsWidget;
use App\Filament\Widgets\OpenTasksWidget;
use App\Filament\Widgets\OpportunityPipelineWidget;
use App\Filament\Widgets\PaymentCashTotalWidget;
use App\Filament\Widgets\RecentTasksWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Panel de control';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            KpiStatsWidget::class,
            PaymentCashTotalWidget::class,
            AgendaWidget::class,
            OpportunityPipelineWidget::class,
            RecentTasksWidget::class,
            OpenTasksWidget::class,
            FinishedTasksLast7DaysWidget::class,
        ];
    }
}
