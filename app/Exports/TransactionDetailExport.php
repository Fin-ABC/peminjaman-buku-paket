<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionDetailExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Collection $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records
            ?? TransactionDetail::with([
                'student',
                'book_item.book',
                'transaction.class.major',
                'transaction.schoolYear',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Judul Buku',
            'Kode Eksemplar',
            'Kelas',
            'Jurusan',
            'Tahun Ajaran',
            'Semester',
            'Tanggal Pinjam',
            'Tenggat Kembali',
            'Status',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        $statusMap = [
            'Borrowed' => 'Dipinjam',
            'Returned' => 'Dikembalikan',
            'Overdue'  => 'Terlambat',
            'lost'     => 'Hilang',
        ];

        return [
            $row->student?->nisn,
            $row->student?->student_name,
            $row->book_item?->book?->title,
            $row->book_item?->item_code,
            $row->transaction?->class?->grade . ' ' . $row->transaction?->class?->class_name,
            $row->transaction?->class?->major?->major_name,
            $row->transaction?->schoolYear?->year_name,
            $row->transaction?->semester === 'odd' ? 'Ganjil' : 'Genap',
            $row->transaction?->transaction_date?->format('d/m/Y'),
            $row->return_date?->format('d/m/Y'),
            $statusMap[$row->status] ?? $row->status,
            $row->note,
        ];
    }
}
