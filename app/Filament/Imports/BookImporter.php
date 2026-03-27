<?php

namespace App\Filament\Imports;

use App\Models\Book;
use App\Models\Major;
use App\Models\Subject;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class BookImporter extends Importer
{
    protected static ?string $model = Book::class;

    public static function getColumns(): array
    {
        return [
            // ImportColumn::make('book_code')
            //     ->rules(['max:255']),
            ImportColumn::make('title')
                ->label('Judul Buku')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'string'])
                ->example('Matematika Wajib Kelas 10'),
            ImportColumn::make('subject_code')
                ->label('Kode Mapel')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('MAT')
                ->castStateUsing(function (string $state): ?int {
                    $subject = Subject::where('subject_code', strtoupper(trim($state)))->first();
                    return $subject?->id;
                }),
            ImportColumn::make('major_code')
                ->label('Kode Jurusan')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('RPL')
                ->castStateUsing(function (string $state): ?int {
                    $major = Major::where('major_code', strtoupper(trim($state)))->first();
                    return $major?->id;
                }),
            ImportColumn::make('grade')
                ->label('Tingkat')
                ->requiredMapping()
                ->rules(['in:10,11,12', 'required'])
                ->example('10'),
            ImportColumn::make('semester')
                ->label('Semester')
                ->requiredMapping()
                ->rules(['required', 'in:odd,even,ganjil,genap'])
                ->example('ganjil')
                ->castStateUsing(function (string $state): string {
                    $state = strtolower(trim($state));
                    return match ($state) {
                        'ganjil', 'odd', '1' => 'odd',
                        'genap', 'even', '2' => 'even',
                        default => $state,
                    };
                }),
            ImportColumn::make('total_stock')
                ->label('Total Stok')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0'])
                ->example('100'),
            ImportColumn::make('remaining_stock')
                ->label('Sisa Stok')
                ->numeric()
                ->rules(['required', 'integer', 'min:0'])
                ->example('100'),
        ];
    }

    public function resolveRecord(): Book
    {
        return new Book();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import buku selesai! ' . number_format($import->successful_rows) . ' buku berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' buku gagal diimport.';
        }

        return $body;
    }

    protected function afterSave(): void
    {
        // Auto-generate book_code dan book_items
        $book = $this->record;

        $book->load('subject', 'major');
        $book->generateBookCode();

        // Create book_items sesuai total_stock
        if ($book->total_stock > 0) {
            $book->syncBookItems($book->total_stock);
        }
    }
}
