<?php

namespace App\Exports;

use App\Models\Classes;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClassesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Collection $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records
            ?? Classes::with('major', 'schoolYear')->orderBy('grade')->get();
    }

    public function headings(): array
    {
        return [
            'Tingkat',
            'Nama Kelas',
            'Jurusan',
            'Tahun Ajaran',
        ];
    }

    public function map($row): array
    {
        return [
            'Kelas ' . $row->grade,
            $row->class_name,
            $row->major?->major_name,
            $row->schoolYear?->year_name,
        ];
    }
}
