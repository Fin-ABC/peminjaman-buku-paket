<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nisn')
                ->label('NISN'),
            ExportColumn::make('student_name')
                ->label('Nama Siswa'),
            ExportColumn::make('class_id')
                ->label('Kelas')
                ->formatStateUsing(function ($state, $record) {
                    // Format: 10-RPL2
                    return $record->class
                        ? "{$record->class->grade}-{$record->class->class_name}"
                        : '';
                }),
            ExportColumn::make('status')
                ->label('Status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export siswa selesai! ' . number_format($export->successful_rows) . ' rows berhasil di-export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' rows gagal.';
        }

        return $body;
    }
}
