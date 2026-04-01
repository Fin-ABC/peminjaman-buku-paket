<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class BookReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    protected Book $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function collection()
    {
        return $this->book
            ->load([
                'bookItems.transactionDetails.transaction.class.major',
            ])
            ->bookItems;
    }

    public function headings(): array
    {
        return [
            'Kode Eksemplar',
            'Kondisi Fisik Saat Ini',
            'Peminjam Terakhir (Kelas)',
            'Jurusan',
            'Tahun Ajaran Terakhir Dipinjam',
            'Semester Terakhir Dipinjam',
        ];
    }

    public function map($row): array
    {
        $conditionMap = [
            'good'     => 'Baik',
            'damaged'  => 'Rusak',
            'lost'     => 'Hilang',
            'borrowed' => 'Sedang Dipinjam',
        ];

        // Ambil transaksi detail terakhir dari item ini
        $lastDetail = $row->transactionDetails
            ->sortByDesc(fn($d) => $d->transaction?->transaction_date)
            ->first();

        $lastTransaction = $lastDetail?->transaction;

        return [
            $row->item_code,
            $conditionMap[$row->condition] ?? $row->condition,
            $lastTransaction?->class
                ? 'Kelas ' . $lastTransaction->class->grade . ' ' . $lastTransaction->class->class_name
                : 'Belum pernah dipinjam',
            $lastTransaction?->class?->major?->major_name ?? '-',
            $lastTransaction?->schoolYear?->year_name ?? '-',
            $lastTransaction?->semester === 'odd' ? 'Ganjil'
                : ($lastTransaction?->semester === 'even' ? 'Genap' : '-'),
        ];
    }

    public function title(): string
    {
        return substr($this->book->title, 0, 31); // Maks 31 karakter untuk sheet title Excel
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Buku - ' . $this->book->title,
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
