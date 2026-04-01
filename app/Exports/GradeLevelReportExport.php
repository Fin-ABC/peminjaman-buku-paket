<?php

namespace App\Exports;

use App\Models\Classes;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class GradeLevelReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected SchoolYear $schoolYear;

    public function __construct(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
    }

    public function collection()
    {
        // Group by grade + major, ambil semua kelas di tahun ajaran ini
        $classes = Classes::with(['major', 'students', 'transactions'])
            ->where('year_id', $this->schoolYear->id)
            ->get();

        // Group by grade + major_id, lalu aggregate
        return $classes
            ->groupBy(fn($class) => $class->grade . '-' . $class->major_id)
            ->map(function ($group) {
                $first = $group->first();

                $totalStudents = $group->sum(
                    fn($class) => $class->students->where('status', 'active')->count()
                );

                $totalBooks = \App\Models\Book::where('grade', $first->grade)
                    ->where('major_id', $first->major_id)
                    ->sum('total_stock');

                $totalTransactions    = $group->sum(fn($c) => $c->transactions->count());
                $returnedTransactions = $group->sum(
                    fn($c) => $c->transactions->where('is_all_returned', true)->count()
                );

                $returnPercentage = $totalTransactions > 0
                    ? round(($returnedTransactions / $totalTransactions) * 100, 1) . '%'
                    : '-';

                return (object) [
                    'grade'             => $first->grade,
                    'major_name'        => $first->major?->major_name,
                    'total_students'    => $totalStudents,
                    'total_books'       => $totalBooks,
                    'return_percentage' => $returnPercentage,
                ];
            })
            ->sortBy('grade')
            ->values();
    }

    public function headings(): array
    {
        return [
            'Tingkat',
            'Jurusan',
            'Total Siswa Aktif',
            'Total Buku Dialokasikan',
            'Persentase Pengembalian',
        ];
    }

    public function map($row): array
    {
        return [
            'Kelas ' . $row->grade,
            $row->major_name,
            $row->total_students,
            $row->total_books,
            $row->return_percentage,
        ];
    }

    public function title(): string
    {
        return 'Laporan Per Angkatan';
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Per Angkatan - ' . $this->schoolYear->year_name,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
