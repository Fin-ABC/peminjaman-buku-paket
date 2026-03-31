<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookConditionChartWidget;
use App\Filament\Widgets\BorrowingBySubjectChartWidget;
use App\Filament\Widgets\OverdueTableWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UnreturnedTransactionWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';
    public function getWidgets(): array
    {
        return [
            BookConditionChartWidget::class,
            StatsOverviewWidget::class,
            OverdueTableWidget::class,
            UnreturnedTransactionWidget::class,
            BorrowingBySubjectChartWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2; // Chart kondisi dan bar chart tampil berdampingan
    }
}
