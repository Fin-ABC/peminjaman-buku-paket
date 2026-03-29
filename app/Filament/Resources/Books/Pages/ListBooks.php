<?php

namespace App\Filament\Resources\Books\Pages;

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
use Maatwebsite\Excel\Facades\Excel;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Buku'),
            ImportAction::make()
                ->importer(BookImporter::class)
                ->label('Import Buku')
                ->color('success')
                ->icon(Heroicon::ArrowUpTray),

            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
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
        ];
    }
}
