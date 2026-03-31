<?php

namespace App\Filament\Widgets;

use App\Models\BookItem;
use Filament\Widgets\ChartWidget;

class BookConditionChartWidget extends ChartWidget
{
    protected ?string $heading = 'Kondisi Fisik Buku';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $good     = BookItem::where('condition', 'good')->count();
        $damaged  = BookItem::where('condition', 'damaged')->count();
        $lost     = BookItem::where('condition', 'lost')->count();
        $borrowed = BookItem::where('condition', 'borrowed')->count();

        return [
            'datasets' => [
                [
                    'data' => [$good, $borrowed, $damaged, $lost],
                    'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444', '#6b7280'],
                ],
            ],
            'labels' => ['Baik', 'Dipinjam', 'Rusak', 'Hilang'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
