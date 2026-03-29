<?php

namespace App\Imports;

use App\Models\Major;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToUpsert;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ClassesImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithUpserts
{
    public function model(array $row): ?array
    {
        $major = Major::where('major_code', strtoupper($row['major_code']))->first();

        if (! $major) {
            return null;
        }

        // Jika year_name kosong, pakai tahun yang aktif
        if (filled($row['year_name'] ?? null)) {
            $schoolYear = SchoolYear::where('year_name', $row['year_name'])->first();
        } else {
            $schoolYear = SchoolYear::where('is_active', true)->first();
        }

        if (! $schoolYear) {
            return null;
        }

        return [
            'grade'      => (string) $row['grade'],
            'major_id'   => $major->id,
            'year_id'    => $schoolYear->id,
            'class_name' => $row['class_name'],
        ];
    }

    public function uniqueBy(): array
    {
        return ['grade', 'major_id', 'year_id', 'class_name'];
    }

    public function rules(): array
    {
        return [
            'grade'      => ['required', 'in:10,11,12'],
            'major_code' => ['required', 'string'],
            'class_name' => ['required', 'string'],
            'year_name'  => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'grade.required'      => 'Kolom grade wajib diisi.',
            'grade.in'            => 'Grade harus berupa 10, 11, atau 12.',
            'major_code.required' => 'Kolom major_code wajib diisi.',
            'class_name.required' => 'Kolom class_name wajib diisi.',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
