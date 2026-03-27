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
                ->label('Kode Mapel')
                ->rules(['nullable', 'string', 'max:10', 'unique:subjects,subject_code'])
                ->example('MAT'),

            ImportColumn::make('subject_name')
                ->label('Nama Mapel')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255', 'unique:subjects,subject_name'])
                ->example('Matematika'),
        ];
    }

    public function resolveRecord(): ?Subject
    {
        return Subject::firstOrNew([
            'subject_name' => $this->data['subject_name'],
        ]);
    }

    protected function beforeSave(): void
    {
        // Auto-generate subject_code kalau kosong
        if (empty($this->data['subject_code'])) {
            $this->data['subject_code'] = Subject::generateCodeFromName($this->data['subject_name']);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import mata pelajaran selesai! ' . number_format($import->successful_rows) . ' mapel berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' mapel gagal diimport.';
        }
        return $body;
    }
}
