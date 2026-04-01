<?php

namespace App\Filament\Resources\Books\Pages;

use App\Exports\BookExport;
use App\Exports\BooksExport;
use App\Filament\Imports\BookImporter;
use App\Filament\Resources\Books\BookResource;
use App\Imports\BookImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Buku'),
            Action::make('import')
                ->label('Import Buku')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Action::make('download_template')
                        ->label('Download Template')
                        ->color('gray')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function () {
                            return Response::download(
                                storage_path('app/templates/template_buku.xlsx'),
                                'Template_Import_Buku.xlsx'
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
                    $import = new BookImport;
                    Excel::import($import, $data['file']);

                    $skipped = $import->getSkippedCount();

                    if ($skipped > 0) {
                        Notification::make()
                            ->title('Import selesai dengan peringatan')
                            ->body("{$skipped} data dilewati karena duplikat, kelas/jurusan tidak ditemukan, atau semester tidak valid.")
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Import berhasil!')
                            ->success()
                            ->send();
                    }
                }),
            Action::make('export_all')
                ->label('Export Semua')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new BookExport(),
                        'Data_Buku_' . now()->format('d-m-Y') . '.xlsx'
                    );
                }),
        ];
    }
}
