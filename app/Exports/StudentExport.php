<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Collection $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records
            ?? Student::with('class.major')->orderBy('student_name')->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Tingkat',
            'Kelas',
            'Jurusan',
            'Status',
        ];
    }

    public function map($row): array
    {
        $statusMap = [
            'active'    => 'Aktif',
            'graduated' => 'Lulus',
            'move'      => 'Pindah',
            'dropout'   => 'Keluar',
        ];

        return [
            $row->nisn,
            $row->student_name,
            'Kelas ' . $row->class?->grade,
            $row->class?->class_name,
            $row->class?->major?->major_name,
            $statusMap[$row->status] ?? $row->status,
        ];
    }
}
