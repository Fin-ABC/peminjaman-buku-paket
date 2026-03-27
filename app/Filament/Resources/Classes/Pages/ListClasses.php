<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Imports\ClassesImporter;
use App\Filament\Resources\Classes\ClassesResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListClasses extends ListRecords
{
    protected static string $resource = ClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Kelas'),
            ImportAction::make()
                ->importer(ClassesImporter::class)
                ->label('Import Kelas')
                ->color('success')
                ->icon(Heroicon::ArrowUpTray),
        ];
    }
}
