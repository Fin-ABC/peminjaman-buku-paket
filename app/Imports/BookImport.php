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

class BookImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    protected int $skippedCount = 0;

    protected array $semesterMap = [
        'ganjil' => 'odd',
        'genap'  => 'even',
    ];

    public function model(array $row): ?Book
    {
        $semester = $this->semesterMap[strtolower($row['semester'])] ?? null;

        if (! $semester) {
            $this->skippedCount++;
            return null;
        }

        $subject = Subject::where('subject_code', strtoupper($row['subject_code']))->first();
        $major   = Major::where('major_code', strtoupper($row['major_code']))->first();

        if (! $subject || ! $major) {
            $this->skippedCount++;
            return null;
        }

        // Cek duplikat: title + subject_id + major_id + grade + semester
        $exists = Book::where('title', $row['title'])
            ->where('subject_id', $subject->id)
            ->where('major_id', $major->id)
            ->where('grade', $row['grade'])
            ->where('semester', $semester)
            ->exists();

        if ($exists) {
            $this->skippedCount++;
            return null;
        }

        $totalStock = (int) $row['total_stock'];

        $book = new Book([
            'title'           => $row['title'],
            'subject_id'      => $subject->id,
            'major_id'        => $major->id,
            'grade'           => (string) $row['grade'],
            'semester'        => $semester,
            'total_stock'     => $totalStock,
            'remaining_stock' => $totalStock,
            'damaged_count'   => 0,
            'lost_count'      => 0,
        ]);

        return $book;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string'],
            'subject_code' => ['required', 'string'],
            'major_code'   => ['required', 'string'],
            'grade'        => ['required', 'in:10,11,12'],
            'semester'     => ['required', 'string'],
            'total_stock'  => ['required', 'integer', 'min:0'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'title.required'        => 'Kolom title wajib diisi.',
            'subject_code.required' => 'Kolom subject_code wajib diisi.',
            'major_code.required'   => 'Kolom major_code wajib diisi.',
            'grade.required'        => 'Kolom grade wajib diisi.',
            'grade.in'              => 'Grade harus berupa 10, 11, atau 12.',
            'semester.required'     => 'Kolom semester wajib diisi.',
            'total_stock.required'  => 'Kolom total_stock wajib diisi.',
            'total_stock.integer'   => 'Kolom total_stock harus berupa angka.',
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
