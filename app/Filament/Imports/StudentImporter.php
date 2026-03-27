<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nisn')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('student_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('class_id')
                ->label('Mis. 10-RPL2 11-SK2 12-DPIB3')
                // Disini kita manipulasi data "12-RPL2" menjadi class_id
                ->fillRecordUsing(function ($record, string $state): void {
                    $parts = explode('-', $state);
                    if (count($parts) === 2) {
                        $class = Classes::where('grade', $parts[0])
                            ->where('class_name', $parts[1])
                            ->first();

                        if ($class) {
                            $record->class_id = $class->id;
                        }
                    }
                })
                ->requiredMapping(),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['in:active,graduated,move,dropout']),
        ];
    }

    public function resolveRecord(): Student
    {
        return new Student();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
