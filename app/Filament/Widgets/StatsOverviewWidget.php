<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\TransactionDetail;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Jalankan scope overdue dulu setiap kali dashboard dibuka
        TransactionDetail::updateOverdueStatus();

        $activeYear = SchoolYear::where('is_active', true)->value('year_name') ?? 'Belum diset';

        $totalStock = Book::sum('total_stock');

        $borrowed = TransactionDetail::where('status', 'Borrowed')->count();

        $problematic = Book::sum('damaged_count') + Book::sum('lost_count');

        $activeStudents = Student::where('status', 'active')->count();

        return [
            Stat::make('Tahun Ajaran Aktif', $activeYear)
                ->description('Tahun ajaran yang sedang berjalan')
                ->icon('heroicon-o-calendar'),

            Stat::make('Total Eksemplar Buku', number_format($totalStock))
                ->description('Jumlah fisik buku keseluruhan')
                ->icon('heroicon-o-book-open')
                ->color('primary'),

            Stat::make('Sedang Dipinjam', number_format($borrowed))
                ->description('Buku yang ada di tangan siswa')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning'),

            Stat::make('Buku Bermasalah', number_format($problematic))
                ->description('Rusak + Hilang')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Siswa Aktif', number_format($activeStudents))
                ->description('Siswa yang terdaftar aktif')
                ->icon('heroicon-o-user-group')
                ->color('success'),
        ];
    }
}
