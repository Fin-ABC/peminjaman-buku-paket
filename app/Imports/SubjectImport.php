<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row): Subject
    {
        // return new Subject([
        //     'subject_code' => $row[0],
        //     'subject_name' => $row[1],
        // ]);

        // $code =

        return new Subject([
            'subject_code' => $row['kode']
                ? $row['kode']
                : Subject::generateCode($row['nama']),
            'subject_name' => $row['nama'],
        ]);
    }

    public function uniqueBy(): string|array
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
