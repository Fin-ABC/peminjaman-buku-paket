<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Siswa'),
             Action::make('download_template')
                ->label('Download Template')
                ->color('gray')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Response::download(
                        storage_path('app/templates/template_siswa.xlsx'),
                        'Template_Import_Siswa.xlsx'
                    );
                }),
            ImportAction::make()
                ->importer(StudentImporter::class)
                ->label('Import Siswa')
                ->color('success')
                ->icon(Heroicon::ArrowUpTray),
        ];
    }
}
