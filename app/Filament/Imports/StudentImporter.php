<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nisn')
                ->label('NISN')
                ->requiredMapping()
                ->example('0072537281'),
            ImportColumn::make('student_name')
                ->label('Nama Siswa')
                ->example('Muhammad Himmel Abdul Rojak')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('class_id')
                ->label('Kelas')
                ->requiredMapping()
                ->rules(['required'])
                ->example('10-RPL2')
                ->castStateUsing(function (string $state): ?int {
                    // Format: 10-RPL2
                    $parts = explode('-', strtoupper(trim($state)));

                    if (count($parts) !== 2) {
                        return null;
                    }

                    [$grade, $className] = $parts;

                    // ✅ Cari kelas berdasarkan grade dan class_name aja
                    $class = Classes::where('grade', $grade)
                        ->where('class_name', $className)
                        ->first();

                    return $class?->id;
                }),
            ImportColumn::make('status')
                ->label('Status')
                ->requiredMapping()
                ->example('active')
                ->castStateUsing(function (?string $state): string {
                    if (blank($state)) {
                        return 'active';
                    }
                    return strtolower(trim($state));
                }),
        ];
    }

    public function resolveRecord(): ?Student
    {
        return Student::firstOrNew([
            'nisn' => $this->data['nisn'],
        ]);
    }

    protected function beforeSave(): void
    {
        if (empty($this->data['status'])) {
            $this->data['status'] = 'active';
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import siswa selesai! ' . Number::format($import->successful_rows) . ' siswa berhasil diimport';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' siswa gagal diimport.';
        }

        return $body;
    }
}
