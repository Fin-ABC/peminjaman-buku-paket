<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class SchoolYearReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected SchoolYear $schoolYear;

    public function __construct(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
    }

    public function collection()
    {
        return Book::with('subject', 'major')
            ->orderBy('major_id')
            ->orderBy('grade')
            ->orderBy('semester')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Buku',
            'Judul Buku',
            'Mata Pelajaran',
            'Jurusan',
            'Tingkat',
            'Semester',
            'Total Stok',
            'Sisa Stok Layak Pakai',
            'Total Rusak',
            'Total Hilang',
        ];
    }

    public function map($row): array
    {
        return [
            $row->book_code,
            $row->title,
            $row->subject?->subject_name,
            $row->major?->major_name,
            'Kelas ' . $row->grade,
            $row->semester === 'odd' ? 'Ganjil' : 'Genap',
            $row->total_stock,
            $row->remaining_stock,
            $row->damaged_count,
            $row->lost_count,
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . str_replace('/', '-', $this->schoolYear->year_name);
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Tahunan - ' . $this->schoolYear->year_name,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
