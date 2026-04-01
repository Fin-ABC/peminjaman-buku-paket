<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\Student;
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

    // 1. Definisikan konstanta berdasarkan header file CSV
    const COL_NISN   = 'nisn';
    const COL_NAME   = 'nama_siswa'; // Menyesuaikan dengan "Nama Siswa" di file CSV
    const COL_CLASS  = 'kelas';      // Menyesuaikan dengan "Kelas" di file CSV
    const COL_STATUS = 'status';

    public function model(array $row): ?Student
    {
        // Skip jika NISN sudah ada
        if (Student::where('nisn', $row[self::COL_NISN])->exists()) {
            $this->duplicates[] = [
                'nisn'         => $row[self::COL_NISN],
                'student_name' => $row[self::COL_NAME],
                'class'        => $row[self::COL_CLASS],
            ];
            return null;
        }

        // Pengaman: Cek apakah format kelas mengandung strip (-)
        if (strpos($row[self::COL_CLASS], '-') === false) {
             $this->duplicates[] = [
                'nisn'         => $row[self::COL_NISN],
                'student_name' => $row[self::COL_NAME],
                'class'        => $row[self::COL_CLASS] . ' (format salah, harus TINGKAT-NAMA)',
            ];
            return null;
        }

        // Parse format "10-RPL1" → grade: 10, class_name: RPL1
        [$grade, $className] = explode('-', $row[self::COL_CLASS], 2);

        $class = Classes::where('grade', $grade)
            ->where('class_name', $className)
            ->first();

        if (! $class) {
            $this->duplicates[] = [
                'nisn'         => $row[self::COL_NISN],
                'student_name' => $row[self::COL_NAME],
                'class'        => $row[self::COL_CLASS] . ' (kelas tidak ditemukan)',
            ];
            return null;
        }

        return new Student([
            'nisn'         => $row[self::COL_NISN],
            'student_name' => $row[self::COL_NAME],
            'class_id'     => $class->id,
            // Jika kosong, kembalikan default 'active'
            'status'       => filled($row[self::COL_STATUS] ?? null) ? $row[self::COL_STATUS] : 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            self::COL_NISN   => ['required', 'string'],
            self::COL_NAME   => ['required', 'string'],
            self::COL_CLASS  => ['required', 'string'],
            self::COL_STATUS => ['nullable', 'in:active,graduated,move,dropout'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            self::COL_NISN . '.required'   => 'Kolom ' . self::COL_NISN . ' wajib diisi.',
            self::COL_NAME . '.required'   => 'Kolom ' . self::COL_NAME . ' wajib diisi.',
            self::COL_CLASS . '.required'  => 'Kolom ' . self::COL_CLASS . ' wajib diisi.',
            self::COL_STATUS . '.in'       => 'Status tidak valid. Gunakan: active, graduated, move, atau dropout.',
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
