<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use App\Models\Major;
use App\Models\SchoolYear;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ClassesImporter extends Importer
{
    protected static ?string $model = Classes::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('grade')
                ->label('Tingkat')
                ->requiredMapping()
                ->rules(['required', 'in:10,11,12'])
                ->example('10'),

            ImportColumn::make('major_code')
                ->label('Kode Jurusan')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('RPL')
                ->castStateUsing(function (string $state): ?int {
                    $major = Major::where('major_code', strtoupper(trim($state)))->first();
                    return $major?->id;
                }),

            ImportColumn::make('year_name')
                ->label('Tahun Ajaran')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('2025/2026')
                ->castStateUsing(function (string $state): ?int {
                    $year = SchoolYear::where('year_name', trim($state))->first();
                    return $year?->id;
                }),

            ImportColumn::make('class_name')
                ->label('Nama Kelas')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:50'])
                ->example('1'),
        ];
    }

    public function resolveRecord(): ?Classes
    {
        // Cari kelas berdasarkan kombinasi unique
        return Classes::firstOrNew([
            'grade' => $this->data['grade'],
            'major_id' => $this->data['major_code'],
            'year_id' => $this->data['year_name'],
            'class_name' => $this->data['class_name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import kelas selesai! ' . number_format($import->successful_rows) . ' kelas berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' kelas gagal diimport.';
        }

        return $body;
    }
}
