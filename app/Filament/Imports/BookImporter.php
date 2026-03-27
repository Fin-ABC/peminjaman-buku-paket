<?php

namespace App\Filament\Imports;

use App\Models\Book;
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
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('subject')
                ->requiredMapping()
                ->relationship(resolveUsing: 'subject_code')
                ->rules(['required']),
            ImportColumn::make('major')
                ->requiredMapping()
                ->relationship(resolveUsing: 'major_code')
                ->rules(['required']),
            ImportColumn::make('grade')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('semester')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('total_stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('remaining_stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): Book
    {
        return new Book();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your book import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
