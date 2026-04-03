<?php

namespace App\Filament\Pages;

use App\Exports\ClassReportExport;
use App\Exports\DamagedLostBooksExport;
use App\Exports\GradeLevelReportExport;
use App\Exports\SchoolYearReportExport;
use App\Exports\SemesterReportExport;
use App\Models\Classes;
use App\Models\SchoolYear;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;
use BackedEnum;
use UnitEnum;

class LaporanPage extends Page
{
    protected string $view = 'filament.pages.laporan-page';
    protected static ?string $title = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Laporan & Export';
    protected static ?int $navigationSort = 11;

    // State untuk setiap form filter
    public ?int $gradeLevel_yearId = null;
    public ?int $semester_yearId = null;
    public ?string $semester_semesterId = null;
    public ?int $schoolYear_yearId = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function exportGradeLevelAction(): Action
    {
        return Action::make('exportGradeLevel')
            ->label('Export Laporan Per Angkatan')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                Select::make('year_id')
                    ->label('Tahun Ajaran')
                    ->options(SchoolYear::orderByDesc('year_name')->pluck('year_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data) {
                $schoolYear = SchoolYear::find($data['year_id']);

                if (!$schoolYear) {
                    Notification::make()
                        ->danger()
                        ->title('Tahun ajaran tidak ditemukan.')
                        ->send();
                    return;
                }

                $fileName = 'Laporan_Angkatan_' . str_replace('/', '-', $schoolYear->year_name)
                    . '_' . now()->format('d-m-Y') . '.xlsx';

                return Excel::download(
                    new GradeLevelReportExport($schoolYear),
                    $fileName
                );
            });
    }

    public function exportSemesterAction(): Action
    {
        return Action::make('exportSemester')
            ->label('Export Laporan Per Semester')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                Select::make('year_id')
                    ->label('Tahun Ajaran')
                    ->options(SchoolYear::orderByDesc('year_name')->pluck('year_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('semester')
                    ->label('Semester')
                    ->options([
                        'odd'  => 'Ganjil',
                        'even' => 'Genap',
                    ])
                    ->required(),
            ])
            ->action(function (array $data) {
                $schoolYear = SchoolYear::find($data['year_id']);

                if (!$schoolYear) {
                    Notification::make()->danger()->title('Tahun ajaran tidak ditemukan.')->send();
                    return;
                }

                $semesterLabel = $data['semester'] === 'odd' ? 'Ganjil' : 'Genap';
                $fileName = 'Laporan_Semester_' . $semesterLabel
                    . '_' . str_replace('/', '-', $schoolYear->year_name)
                    . '_' . now()->format('d-m-Y') . '.xlsx';

                return Excel::download(
                    new SemesterReportExport($schoolYear, $data['semester']),
                    $fileName
                );
            });
    }

    public function exportSchoolYearAction(): Action
    {
        return Action::make('exportSchoolYear')
            ->label('Export Laporan Per Tahun Ajaran')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                Select::make('year_id')
                    ->label('Tahun Ajaran')
                    ->options(SchoolYear::orderByDesc('year_name')->pluck('year_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data) {
                $schoolYear = SchoolYear::find($data['year_id']);

                if (!$schoolYear) {
                    Notification::make()->danger()->title('Tahun ajaran tidak ditemukan.')->send();
                    return;
                }

                $fileName = 'Laporan_Tahunan_'
                    . str_replace('/', '-', $schoolYear->year_name)
                    . '_' . now()->format('d-m-Y') . '.xlsx';

                return Excel::download(
                    new SchoolYearReportExport($schoolYear),
                    $fileName
                );
            });
    }

    public function exportPerKelasAction(): Action
    {
        return Action::make('exportPerKelas')
            ->label('Export Laporan Per Kelas')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                Select::make('class_id')
                    ->label('Pilih Kelas')
                    ->options(
                        Classes::with('major', 'schoolYear')
                            ->get()
                            ->mapWithKeys(fn($class) => [
                                $class->id => $class->grade . '-' . $class->class_name
                                    . ' ' . ($class->schoolYear?->year_name ?? ''),
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data) {
                $class = Classes::with('major', 'schoolYear')->find($data['class_id']);

                if (!$class) {
                    Notification::make()->danger()->title('Kelas tidak ditemukan.')->send();
                    return;
                }

                $fileName = 'Laporan_Kelas_'
                    . $class->grade . '-' . str_replace(' ', '_', $class->class_name)
                    . '_' . str_replace('/', '-', $class->schoolYear?->year_name ?? '')
                    . '_' . now()->format('d-m-Y') . '.xlsx';

                return Excel::download(new ClassReportExport($class), $fileName);
            });
    }

    // ── ACTION: Laporan Buku Rusak & Hilang ───────────────────────
    public function exportDamagedLostAction(): Action
    {
        return Action::make('exportDamagedLost')
            ->label('Export Laporan Buku Rusak & Hilang')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Export Laporan Buku Rusak & Hilang')
            ->modalDescription('File Excel akan berisi semua eksemplar buku dengan kondisi rusak atau hilang beserta peminjam terakhirnya.')
            ->modalSubmitActionLabel('Ya, Export')
            ->action(function () {
                $fileName = 'Laporan_Buku_Rusak_Hilang_' . now()->format('d-m-Y') . '.xlsx';

                return Excel::download(new DamagedLostBooksExport(), $fileName);
            });
    }
}
