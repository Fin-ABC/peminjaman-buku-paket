<?php

namespace App\Exports;

use App\Models\BookItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;

class DamagedLostBooksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithProperties
{
    public function collection()
    {
        return BookItem::with([
            'book.subject',
            'transactionDetails.transaction.class.major',
            'transactionDetails.student',
        ])
        ->whereIn('condition', ['damaged', 'lost'])
        ->orderBy('condition')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Eksemplar',
            'Judul Buku',
            'Mata Pelajaran',
            'Kondisi',
            'Peminjam Terakhir (Siswa)',
            'Kelas Terakhir',
            'Jurusan Terakhir',
        ];
    }

    public function map($row): array
    {
        $conditionMap = [
            'damaged' => 'Rusak',
            'lost'    => 'Hilang',
        ];

        // Ambil transaksi detail terakhir
        $lastDetail = $row->transactionDetails
            ->sortByDesc(fn($d) => $d->transaction?->transaction_date)
            ->first();

        $lastTransaction = $lastDetail?->transaction;

        return [
            $row->item_code,
            $row->book?->title,
            $row->book?->subject?->subject_name,
            $conditionMap[$row->condition] ?? $row->condition,
            $lastDetail?->student?->student_name ?? '-',
            $lastTransaction?->class
                ? 'Kelas ' . $lastTransaction->class->grade . ' ' . $lastTransaction->class->class_name
                : '-',
            $lastTransaction?->class?->major?->major_name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Buku Rusak & Hilang';
    }

    public function properties(): array
    {
        return [
            'title'   => 'Laporan Buku Rusak & Hilang',
            'creator' => 'Perpustakaan SMKN 1 Sumedang',
        ];
    }
}
