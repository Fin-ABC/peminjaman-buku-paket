<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Imports\SubjectImporter;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Imports\SubjectImport;
use App\Imports\SubjectsImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Mapel'),
            Action::make('subject_import')
                ->label('Import Mapel')
                ->form([
                    Action::make('download_template')
                        ->label('Download Template')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function () {
                            return Response::download(
                                storage_path('app/templates/template_mapel.xlsx'),
                                'Template_Import_Mapel.xlsx'
                            );
                        }),
                    FileUpload::make('file')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    Excel::import(new SubjectImport, $data['file']);
                    Notification::make()
                        ->title('Import Berhasil')
                        ->success()
                        ->send()
                    ;
                })
        ];
    }
}
