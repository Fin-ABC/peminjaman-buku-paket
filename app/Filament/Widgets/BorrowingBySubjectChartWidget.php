<?php

namespace App\Filament\Widgets;

use App\Models\TransactionDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BorrowingBySubjectChartWidget extends ChartWidget
{
    protected ?string $heading = 'Peminjaman per Mata Pelajaran';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = TransactionDetail::query()
            ->where('status', 'Borrowed')
            ->join('book_items', 'transaction_details.book_item_id', '=', 'book_items.id')
            ->join('books', 'book_items.book_id', '=', 'books.id')
            ->join('subjects', 'books.subject_id', '=', 'subjects.id')
            ->select('subjects.subject_name', DB::raw('count(*) as total'))
            ->groupBy('subjects.subject_name')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dipinjam',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $data->pluck('subject_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
