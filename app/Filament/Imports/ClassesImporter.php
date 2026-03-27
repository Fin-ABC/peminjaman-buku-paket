<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ClassesImporter extends Importer
{
    protected static ?string $model = Classes::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('grade')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('major_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('year_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('class_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): Classes
    {
        return new Classes();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your classes import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
