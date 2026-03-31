<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Imports\ClassesImporter;
use App\Filament\Resources\Classes\ClassesResource;
use App\Imports\ClassesImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ListClasses extends ListRecords
{
    protected static string $resource = ClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Kelas'),
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                     Action::make('download_template')
                        ->label('Download Template')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function () {
                            return Response::download(
                                storage_path('app/templates/template_kelas.xlsx'),
                                'Template_Import_Kelas.xlsx'
                            );
                        }),
                    FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    Excel::import(new ClassesImport, $data['file']);

                    Notification::make()
                        ->title('Import kelas berhasil!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
