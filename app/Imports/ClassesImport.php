<?php

namespace App\Imports;

use App\Models\Classes;
use App\Models\Major;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\ToUpsert; // <- Ini bisa dihapus karena tidak terpakai, yang benar adalah WithUpserts
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ClassesImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithUpserts
{
    const COL_GRADE      = 'tingkat';
    const COL_MAJOR_CODE = 'kode_jurusan';
    const COL_CLASS_NAME = 'nama_kelas';
    const COL_YEAR_NAME  = 'tahun_ajaran';

    public function model(array $row): ?Classes
    {
        $major = Major::where('major_code', strtoupper($row[self::COL_MAJOR_CODE]))->first();

        if (! $major) {
            return null;
        }

        // Jika year_name kosong, pakai tahun ajaran yang aktif
        if (filled($row[self::COL_YEAR_NAME] ?? null)) {
            $schoolYear = SchoolYear::where('year_name', $row[self::COL_YEAR_NAME])->first();
        } else {
            $schoolYear = SchoolYear::where('is_active', true)->first();
        }

        if (! $schoolYear) {
            return null;
        }

        return new Classes([
            'grade'      => (string) $row[self::COL_GRADE],
            'major_id'   => $major->id,
            'year_id'    => $schoolYear->id,
            'class_name' => $row[self::COL_CLASS_NAME],
        ]);
    }

    public function uniqueBy(): array
    {
        // PERHATIAN: Ini adalah nama kolom di DATABASE, jadi jangan gunakan konstanta Excel di sini
        return ['grade', 'major_id', 'year_id', 'class_name'];
    }

    public function rules(): array
    {
        return [
            self::COL_GRADE      => ['required', 'in:10,11,12'],
            self::COL_MAJOR_CODE => ['required', 'string'],
            self::COL_CLASS_NAME => ['required', 'string'],
            self::COL_YEAR_NAME  => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            self::COL_GRADE . '.required'      => 'Kolom ' . self::COL_GRADE . ' wajib diisi.',
            self::COL_GRADE . '.in'            => 'Grade harus berupa 10, 11, atau 12.',
            self::COL_MAJOR_CODE . '.required' => 'Kolom ' . self::COL_MAJOR_CODE . ' wajib diisi.',
            self::COL_CLASS_NAME . '.required' => 'Kolom ' . self::COL_CLASS_NAME . ' wajib diisi.',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
