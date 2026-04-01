<?php

namespace App\Filament\Pages;

use App\Exports\GradeLevelReportExport;
use App\Exports\SchoolYearReportExport;
use App\Exports\SemesterReportExport;
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
    protected static ?int $navigationSort = 1;

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
}
