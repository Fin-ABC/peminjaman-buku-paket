<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Siswa'),
            ImportAction::make()
                ->importer(StudentImporter::class)
                ->label('Import Siswa')
                ->color('success')
                ->icon(Heroicon::ArrowUpTray),
        ];
    }
}
