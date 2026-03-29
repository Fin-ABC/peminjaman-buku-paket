<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Notifications\DuplicateStudentsNotification;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class StudentImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    protected array $duplicates = [];

    public function model(array $row): ?Student
    {
        // Skip jika NISN sudah ada
        if (Student::where('nisn', $row['nisn'])->exists()) {
            $this->duplicates[] = [
                'nisn'         => $row['nisn'],
                'student_name' => $row['student_name'],
                'class'        => $row['class'],
            ];
            return null;
        }

        // Parse format "10-RPL1" → grade: 10, class_name: RPL1
        [$grade, $className] = explode('-', $row['class'], 2);

        $class = Classes::where('grade', $grade)
            ->where('class_name', $className)
            ->first();

        if (! $class) {
            $this->duplicates[] = [
                'nisn'         => $row['nisn'],
                'student_name' => $row['student_name'],
                'class'        => $row['class'] . ' (kelas tidak ditemukan)',
            ];
            return null;
        }

        return new Student([
            'nisn'         => $row['nisn'],
            'student_name' => $row['student_name'],
            'class_id'     => $class->id,
            'status'       => filled($row['status'] ?? null) ? $row['status'] : 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn'         => ['required', 'string'],
            'student_name' => ['required', 'string'],
            'class'        => ['required', 'string'],
            'status'       => ['nullable', 'in:active,graduated,move,dropout'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nisn.required'         => 'Kolom nisn wajib diisi.',
            'student_name.required' => 'Kolom student_name wajib diisi.',
            'class.required'        => 'Kolom class wajib diisi.',
            'status.in'             => 'Status tidak valid. Gunakan: active, graduated, move, atau dropout.',
        ];
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function getDuplicates(): array
    {
        return $this->duplicates;
    }
}
