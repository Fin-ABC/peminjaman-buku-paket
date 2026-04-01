<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Collection $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records
            ?? Transaction::with('book', 'class.major', 'schoolYear')
                ->orderBy('transaction_date', 'desc')
                ->get();
    }

    public function headings(): array
    {
        return [
            'Judul Buku',
            'Tingkat',
            'Kelas',
            'Jurusan',
            'Tahun Ajaran',
            'Semester',
            'Tanggal Pinjam',
            'Status Pengembalian',
        ];
    }

    public function map($row): array
    {
        return [
            $row->book?->title,
            'Kelas ' . $row->class?->grade,
            $row->class?->class_name,
            $row->class?->major?->major_name,
            $row->schoolYear?->year_name,
            $row->semester === 'odd' ? 'Ganjil' : 'Genap',
            $row->transaction_date?->format('d/m/Y'),
            $row->is_all_returned ? 'Sudah Kembali Semua' : 'Belum Tuntas',
        ];
    }
}
