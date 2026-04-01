<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BookExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Collection $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records
            ?? Book::with('subject', 'major')->orderBy('created_at', 'desc')->get();
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
            'Sisa Stok',
            'Rusak',
            'Hilang',
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
}
