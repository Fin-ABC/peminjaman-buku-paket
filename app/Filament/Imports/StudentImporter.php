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
                ->rules(['required', 'max:20', 'unique:student,nisn', 'string'])
                ->example('0072537281'),
            ImportColumn::make('student_name')
                ->label('Nama Siswa')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'string']),
            ImportColumn::make('class')
                ->label('Kelas')
                ->rules(['required', 'string'])
                ->example('10-RPL1 11-SK2 12-DPIB3')
                // Disini kita manipulasi data "12-RPL2" menjadi class_id
                ->fillRecordUsing(function ($record, string $state): void {
                    $parts = explode('-', $state);
                    if (count($parts) === 2) {
                        $class = Classes::where('grade', $parts[0])
                            ->where('class_name', $parts[1])
                            ->first();

                        if ($class) {
                            $record->class_id = $class->id;
                        }
                    }
                })
                ->requiredMapping(),
            ImportColumn::make('status')
                ->label('Status')
                ->default('active')
                ->example('active')
                ->rules(['in:active,graduated,move,dropout']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        // Cari siswa berdasarkan NISN (update kalau ada, create kalau belum)
        return Student::firstOrNew([
            'nisn' => $this->data['nisn'],
        ]);
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
