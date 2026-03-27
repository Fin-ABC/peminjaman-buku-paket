<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Imports\SubjectImporter;
use App\Filament\Resources\Subjects\SubjectResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Mapel'),
            ImportAction::make()
                ->importer(SubjectImporter::class)
                ->label('Import Mapel')
                ->color('success')
                ->icon(Heroicon::ArrowUpTray),
        ];
    }
}
