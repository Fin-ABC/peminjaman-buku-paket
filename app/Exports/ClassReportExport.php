<?php

namespace App\Exports;

use App\Models\Classes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class ClassReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected Classes $class;

    public function __construct(Classes $class)
    {
        $this->class = $class;
    }

    public function collection()
    {
        return $this->class
            ->load([
                'transactions.transactionDetails.student',
                'transactions.transactionDetails.book_item.book',
                'transactions.book',
                'transactions.schoolYear',
            ])
            ->transactions
            ->flatMap(function ($transaction) {
                return $transaction->transactionDetails->map(function ($detail) use ($transaction) {
                    return (object) [
                        'transaction'  => $transaction,
                        'detail'       => $detail,
                    ];
                });
            });
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Judul Buku',
            'Kode Eksemplar',
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
            $row->detail->student?->nisn,
            $row->detail->student?->student_name,
            $row->detail->book_item?->book?->title,
            $row->detail->book_item?->item_code,
            $row->transaction->schoolYear?->year_name,
            $row->transaction->semester === 'odd' ? 'Ganjil' : 'Genap',
            $row->transaction->transaction_date?->format('d/m/Y'),
            $row->detail->return_date?->format('d/m/Y'),
            $statusMap[$row->detail->status] ?? $row->detail->status,
            $row->detail->note,
        ];
    }

    public function title(): string
    {
        return 'Kelas ' . $this->class->grade . ' ' . $this->class->class_name;
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Kelas ' . $this->class->grade . ' ' . $this->class->class_name,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
