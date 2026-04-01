<?php

namespace App\Filament\Pages;

use App\Imports\BookImport;
use App\Imports\ClassesImport;
use App\Imports\StudentImport;
use App\Imports\SubjectImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use BackedEnum;
use UnitEnum;

class ImportPage extends Page
{
    protected string $view = 'filament.pages.import-page';
    protected static ?string $title = 'Import Data';
    protected static ?string $navigationLabel = 'Import Data';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan & Export';
    protected static ?int $navigationSort = 2;

    // ── ACTION: Import Buku ───────────────────────────────────────
    public function downloadTemplateBukuAction(): Action
    {
        return Action::make('downloadTemplateBuku')
            ->label('Download Template')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn() => Response::download(
                storage_path('app/templates/template_buku.xlsx'),
                'Template_Import_Buku.xlsx'
            ));
    }

    public function importBukuAction(): Action
    {
        return Action::make('importBuku')
            ->label('Import Buku')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
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
                $import = new BookImport();
                Excel::import($import, $data['file']);

                $skipped = $import->getSkippedCount();

                if ($skipped > 0) {
                    Notification::make()
                        ->warning()
                        ->title('Import selesai dengan peringatan')
                        ->body("{$skipped} data dilewati karena duplikat atau data referensi tidak ditemukan.")
                        ->persistent()
                        ->send();
                } else {
                    Notification::make()
                        ->success()
                        ->title('Import Buku Berhasil!')
                        ->send();
                }
            });
    }

    // ── ACTION: Import Kelas ──────────────────────────────────────
    public function downloadTemplateKelasAction(): Action
    {
        return Action::make('downloadTemplateKelas')
            ->label('Download Template')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn() => Response::download(
                storage_path('app/templates/template_kelas.xlsx'),
                'Template_Import_Kelas.xlsx'
            ));
    }

    public function importKelasAction(): Action
    {
        return Action::make('importKelas')
            ->label('Import Kelas')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
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
                Excel::import(new ClassesImport(), $data['file']);

                Notification::make()
                    ->success()
                    ->title('Import Kelas Berhasil!')
                    ->body('Data kelas berhasil diimport. Data yang sudah ada akan diperbarui otomatis.')
                    ->send();
            });
    }

    // ── ACTION: Import Siswa ──────────────────────────────────────
    public function downloadTemplateSiswaAction(): Action
    {
        return Action::make('downloadTemplateSiswa')
            ->label('Download Template')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn() => Response::download(
                storage_path('app/templates/template_siswa.xlsx'),
                'Template_Import_Siswa.xlsx'
            ));
    }

    public function importSiswaAction(): Action
    {
        return Action::make('importSiswa')
            ->label('Import Siswa')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
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
                $import = new StudentImport();
                Excel::import($import, $data['file']);

                $duplicates = $import->getDuplicates();

                if (count($duplicates) > 0) {
                    $names = collect($duplicates)
                        ->take(5)
                        ->map(fn($d) => "• {$d['student_name']} ({$d['nisn']}) — {$d['class']}")
                        ->join("\n");

                    $more = count($duplicates) > 5
                        ? "\n...dan " . (count($duplicates) - 5) . " lainnya."
                        : '';

                    Notification::make()
                        ->warning()
                        ->title('Import selesai dengan ' . count($duplicates) . ' data dilewati')
                        ->body($names . $more)
                        ->persistent()
                        ->send();
                } else {
                    Notification::make()
                        ->success()
                        ->title('Import Siswa Berhasil!')
                        ->send();
                }
            });
    }

    // ── ACTION: Import Mata Pelajaran ─────────────────────────────
    public function downloadTemplateMapelAction(): Action
    {
        return Action::make('downloadTemplateMapel')
            ->label('Download Template')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn() => Response::download(
                storage_path('app/templates/template_mapel.xlsx'),
                'Template_Import_Mapel.xlsx'
            ));
    }

    public function importMapelAction(): Action
    {
        return Action::make('importMapel')
            ->label('Import Mata Pelajaran')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
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
                Excel::import(new SubjectImport(), $data['file']);

                Notification::make()
                    ->success()
                    ->title('Import Mata Pelajaran Berhasil!')
                    ->body('Data mata pelajaran berhasil diimport. Data yang sudah ada akan diperbarui otomatis.')
                    ->send();
            });
    }
}
