<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\Students\StudentResource;
use App\Imports\StudentImport;
use App\Models\User;
use App\Notifications\DuplicateStudentNotification;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Siswa'),
            Action::make('import')
                ->label('Import Siswa')
                ->icon(Heroicon::ArrowUpTray)
                ->form([
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
                    $import = new StudentImport;
                    Excel::import($import, $data['file']);

                    $duplicates = $import->getDuplicates();

                    if (count($duplicates) > 0) {
                        // Kirim notifikasi ke semua user
                        $users = User::all();
                        foreach ($users as $user) {
                            $user->notify(new DuplicateStudentNotification($duplicates, count($duplicates)));
                        }

                        Notification::make()
                            ->title('Import selesai dengan peringatan')
                            ->body(count($duplicates) . ' data duplikat/tidak valid ditemukan. Cek notifikasi untuk detailnya.')
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Import berhasil!')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
