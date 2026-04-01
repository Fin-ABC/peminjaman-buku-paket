<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class StudentReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function collection()
    {
        return $this->student
            ->load([
                'transactionDetails.book_item.book.subject',
                'transactionDetails.book_item.book.major',
                'transactionDetails.transaction.schoolYear',
            ])
            ->transactionDetails;
    }

    public function headings(): array
    {
        return [
            'Tahun Ajaran',
            'Semester',
            'Judul Buku',
            'Mata Pelajaran',
            'Kode Eksemplar',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Status',
            'Kondisi Buku Saat Ini',
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

        $conditionMap = [
            'good'     => 'Baik',
            'damaged'  => 'Rusak',
            'lost'     => 'Hilang',
            'borrowed' => 'Sedang Dipinjam',
        ];

        return [
            $row->transaction?->schoolYear?->year_name,
            $row->transaction?->semester === 'odd' ? 'Ganjil' : 'Genap',
            $row->book_item?->book?->title,
            $row->book_item?->book?->subject?->subject_name,
            $row->book_item?->item_code,
            $row->transaction?->transaction_date?->format('d/m/Y'),
            $row->return_date?->format('d/m/Y'),
            $statusMap[$row->status] ?? $row->status,
            $conditionMap[$row->book_item?->condition] ?? $row->book_item?->condition,
            $row->note,
        ];
    }

    public function title(): string
    {
        return $this->student->student_name;
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Peminjaman - ' . $this->student->student_name,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
