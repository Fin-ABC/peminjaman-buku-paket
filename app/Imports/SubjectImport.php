<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SubjectImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithUpserts
{
    const COL_NAME = 'nama_mata_pelajaran';
    const COL_CODE = 'kode_mapel';

    public function model(array $row): Subject
    {
        $code = filled($row[self::COL_CODE])
            ? $row[self::COL_CODE]
            : Subject::generateCode($row[self::COL_NAME]);

        return new Subject([
            'subject_code' => $code,
            'subject_name' => $row[self::COL_NAME],
        ]);
    }

    public function uniqueBy(): string|array
    {
        return 'subject_name';
    }

    public function rules(): array
    {
        return [
            self::COL_NAME => ['required', 'string', 'max:255'],
            self::COL_CODE => ['nullable', 'string', 'max:20'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            self::COL_NAME . '.required' => 'Kolom ' . self::COL_NAME . ' wajib diisi.',
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
