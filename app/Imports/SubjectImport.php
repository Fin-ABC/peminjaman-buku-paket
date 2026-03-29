<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToUpsert;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SubjectImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithUpserts
{
    public function model(array $row): Subject
    {
        $code = filled($row['subject_code'])
            ? $row['subject_code']
            : Subject::generateCode($row['subject_name']);

        return new Subject([
            'subject_code' => $code,
            'subject_name' => $row['subject_name'],
        ]);
    }

    public function uniqueBy(): string|array
    {
        return 'subject_name';
    }

    public function rules(): array
    {
        return [
            'subject_name' => ['required', 'string', 'max:255'],
            'subject_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'subject_name.required' => 'Kolom subject_name wajib diisi.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
