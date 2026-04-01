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

class SemesterReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected SchoolYear $schoolYear;
    protected string $semester;

    public function __construct(SchoolYear $schoolYear, string $semester)
    {
        $this->schoolYear = $schoolYear;
        $this->semester   = $semester;
    }

    public function collection()
    {
        return Classes::with([
            'major',
            'transactions' => fn($q) => $q->where('year_id', $this->schoolYear->id)
                                          ->where('semester', $this->semester),
        ])
        ->where('year_id', $this->schoolYear->id)
        ->orderBy('grade')
        ->orderBy('class_name')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Tingkat',
            'Nama Kelas',
            'Jurusan',
            'Total Transaksi',
            'Sudah Kembali Semua',
            'Belum Kembali',
            'Persentase Pengembalian',
        ];
    }

    public function map($row): array
    {
        $total    = $row->transactions->count();
        $returned = $row->transactions->where('is_all_returned', true)->count();
        $notYet   = $total - $returned;

        $percentage = $total > 0
            ? round(($returned / $total) * 100, 1) . '%'
            : '-';

        return [
            'Kelas ' . $row->grade,
            $row->class_name,
            $row->major?->major_name,
            $total,
            $returned,
            $notYet,
            $percentage,
        ];
    }

    public function title(): string
    {
        return 'Laporan Semester ' . ($this->semester === 'odd' ? 'Ganjil' : 'Genap');
    }

    public function properties(): array
    {
        $semesterLabel = $this->semester === 'odd' ? 'Ganjil' : 'Genap';

        return [
            'title'   => 'Laporan Semester ' . $semesterLabel . ' - ' . $this->schoolYear->year_name,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
