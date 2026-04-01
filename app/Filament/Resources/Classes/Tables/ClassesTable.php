<?php

namespace App\Filament\Resources\Classes\Tables;

use App\Exports\ClassReportExport;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('class_name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('grade')
                    ->label('Tingkat')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'lulus' ? $state : 'Kelas ' . $state)
                    ->color('primary'),

                TextColumn::make('major.major_name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('schoolYear.year_name')
                    ->label('Tahun Masuk')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                        'lulus' => 'Lulus',
                    ]),
                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'major_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('year_id')
                    ->label('Tahun Masuk')
                    ->relationship('schoolYear', 'year_name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('luluskan')
                    ->label('Luluskan')
                    ->icon('heroicon-o-academic-cap') // Icon topi toga/kelulusan
                    ->color('success')

                    // Tambahkan konfirmasi agar admin tidak kepencet tanpa sengaja
                    ->requiresConfirmation()
                    ->modalHeading('Luluskan Kelas Ini?')
                    ->modalDescription('Apakah kamu yakin ingin mengubah tingkat kelas ini menjadi Lulus?')
                    ->modalSubmitActionLabel('Ya, Luluskan')

                    // Menyembunyikan tombol ini jika kelasnya sudah berstatus 'lulus'
                    ->hidden(fn($record): bool => $record->grade === 'lulus')

                    // Ini adalah inti logikanya: mengupdate database
                    ->action(function ($record) {
                        $record->update(['grade' => 'lulus']);
                    })

                    // Notifikasi hijau sukses di pojok kanan atas
                    ->successNotificationTitle('Kelas berhasil diluluskan!'),
                Action::make('lihat_siswa')
                    ->label('Lihat Siswa')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    // Arahkan URL ke halaman index StudentResource dengan membawa parameter filter
                    ->url(fn($record): string => StudentResource::getUrl('index', [
                        'filters' => [
                            // 'class_id' ini harus sama persis dengan nama filter di StudentResource
                            'class_id' => ['values' => $record->id],
                        ],
                    ])),
                EditAction::make(),
                Action::make('export_class_report')
                    ->label('Export Laporan')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        $className = 'Kelas_' . $record->grade . '_' . $record->class_name;
                        $fileName  = 'Laporan_' . $className . '_' . now()->format('d-m-Y') . '.xlsx';

                        return Excel::download(
                            new ClassReportExport($record),
                            $fileName
                        );
                    }),
            ])->paginated(10, 25, 50)
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_with_verification')
                        ->label('Hapus yang Dipilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kelas Terpilih')
                        ->modalDescription(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                return '⚠️ PERINGATAN: Beberapa kelas yang dipilih memiliki data siswa. Menghapus kelas akan menghapus SEMUA data siswa terkait!';
                            }

                            return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' kelas yang dipilih?';
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->form(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            return $hasRelated ? [
                                Section::make('Verifikasi Penghapusan Massal')
                                    ->description('⚠️ Beberapa kelas memiliki data siswa. Masukkan email dan password Anda.')
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email Anda')
                                            ->email()
                                            ->required()
                                            ->placeholder(Auth::user()->email ?? ''),

                                        TextInput::make('password')
                                            ->label('Password Anda')
                                            ->password()
                                            ->revealable()
                                            ->required(),
                                    ])
                                    ->columns(1),
                            ] : [];
                        })
                        ->action(function ($records, array $data) {
                            $user = Auth::user();
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                if (!isset($data['email']) || !isset($data['password'])) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Validasi Diperlukan')
                                        ->body('Email dan password wajib diisi.')
                                        ->persistent()
                                        ->send();
                                    return;
                                }

                                if ($data['email'] !== $user->email || !Hash::check($data['password'], $user->password)) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Verifikasi Gagal')
                                        ->body('Email atau password tidak sesuai.')
                                        ->persistent()
                                        ->send();
                                    return;
                                }
                            }

                            $count = $records->count();
                            $records->each->delete();

                            Notification::make()
                                ->success()
                                ->title('Kelas Dihapus')
                                ->body("{$count} kelas berhasil dihapus.")
                                ->send();
                        }),
                ]),
            ]);
    }
}
