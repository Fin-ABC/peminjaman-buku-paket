<?php

namespace App\Filament\Pages;

use App\Filament\Imports\BookImporter;
use App\Filament\Imports\StudentImporter;
use App\Filament\Imports\SubjectImporter;
use BackedEnum;
use Filament\Actions\ImportAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ImportCenter extends Page
{
    protected string $view = 'filament.pages.import-center';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDown;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('import_students')
                ->importer(StudentImporter::class)
                ->color('primary')
                ->label('Import Siswa'),

            ImportAction::make('import_books')
                ->importer(BookImporter::class)
                ->color('success')
                ->label('Import Buku'),

            ImportAction::make('import_subjects')
                ->importer(SubjectImporter::class)
                ->color('warning')
                ->label('Import Mata Pelajaran'),
        ];
    }
}
