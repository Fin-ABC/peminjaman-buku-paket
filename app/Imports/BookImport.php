<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Major;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class BookImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, WithBatchInserts, SkipsOnError
{
    use SkipsErrors;

    protected int $skippedCount = 0;

    // 1. KITA DEFINISIKAN SEMUA NAMA KOLOM EXCEL DI SINI
    const COL_TITLE    = 'judul_buku';
    const COL_SUBJECT  = 'kode_mapel';
    const COL_MAJOR    = 'kode_jurusan';
    const COL_GRADE    = 'tingkat';
    const COL_SEMESTER = 'semester';
    const COL_STOCK    = 'total_stok';

    protected array $semesterMap = [
        'odd' => 'odd',
        'even'  => 'even',
        'ganjil' => 'odd',
        'genap'  => 'even',
        '1' => 'odd',
        '2' => 'even',
    ];

    public function model(array $row): ?Book
    {
        // 2. KITA PANGGIL MENGGUNAKAN self::NAMA_KONSTANTA
        $semester = $this->semesterMap[strtolower($row[self::COL_SEMESTER])] ?? null;

        if (! $semester) {
            $this->skippedCount++;
            return null;
        }

        $subject = Subject::where('subject_code', strtoupper($row[self::COL_SUBJECT]))->first();
        $major   = Major::where('major_code', strtoupper($row[self::COL_MAJOR]))->first();

        if (! $subject || ! $major) {
            $this->skippedCount++;
            return null;
        }

        $exists = Book::where('title', $row[self::COL_TITLE])
            ->where('subject_id', $subject->id)
            ->where('major_id', $major->id)
            ->where('grade', $row[self::COL_GRADE])
            ->where('semester', $semester)
            ->exists();

        if ($exists) {
            $this->skippedCount++;
            return null;
        }

        $totalStock = (int) $row[self::COL_STOCK];

        return new Book([
            'title'           => $row[self::COL_TITLE],
            'subject_id'      => $subject->id,
            'major_id'        => $major->id,
            'grade'           => (string) $row[self::COL_GRADE],
            'semester'        => $semester,
            'total_stock'     => $totalStock,
            'remaining_stock' => $totalStock,
            'damaged_count'   => 0,
            'lost_count'      => 0,
        ]);
    }

    public function rules(): array
    {
        // 3. GUNAKAN KONSTANTA SEBAGAI KEY ARRAY
        return [
            self::COL_TITLE    => ['required', 'string'],
            self::COL_SUBJECT  => ['required', 'string'],
            self::COL_MAJOR    => ['required', 'string'],
            self::COL_GRADE    => ['required', 'in:10,11,12'],
            self::COL_SEMESTER => ['required'],
            self::COL_STOCK    => ['required', 'integer', 'min:0'],
        ];
    }

    public function customValidationMessages(): array
    {
        // 4. GABUNGKAN KONSTANTA DENGAN ATURAN VALIDASI (menggunakan titik / concatenation)
        return [
            self::COL_TITLE . '.required'    => 'Kolom ' . self::COL_TITLE . ' wajib diisi.',
            self::COL_SUBJECT . '.required'  => 'Kolom ' . self::COL_SUBJECT . ' wajib diisi.',
            self::COL_MAJOR . '.required'    => 'Kolom ' . self::COL_MAJOR . ' wajib diisi.',
            self::COL_GRADE . '.required'    => 'Kolom ' . self::COL_GRADE . ' wajib diisi.',
            self::COL_GRADE . '.in'          => 'Tingkat harus berupa 10, 11, atau 12.',
            self::COL_SEMESTER . '.required' => 'Kolom ' . self::COL_SEMESTER . ' wajib diisi.',
            self::COL_STOCK . '.required'    => 'Kolom ' . self::COL_STOCK . ' wajib diisi.',
            self::COL_STOCK . '.integer'     => 'Kolom ' . self::COL_STOCK . ' harus berupa angka.',
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

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
