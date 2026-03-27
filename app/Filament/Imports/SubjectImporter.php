<?php

namespace App\Filament\Imports;

use App\Models\Subject;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SubjectImporter extends Importer
{
    protected static ?string $model = Subject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('subject_code')
                ->label('Kode Mapel (Kosongkan untuk auto-generate')
                ->fillRecordUsing(function ($record, ?string $state): void {
                    // Jika state dari excel kosong, panggil fungsi generateCode
                    $record->subject_code = blank($state) ? Subject::generateCode() : $state;
                })
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('subject_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): Subject
    {
        return new Subject();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your subject import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
