<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
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

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('nis')
                //     ->searchable(),
                // TextColumn::make('student_name')
                //     ->searchable(),
                // TextColumn::make('class_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('status')
                //     ->badge(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIS berhasil disalin!')
                    ->weight('bold'),

                TextColumn::make('student_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('class.class_name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        return "Kelas {$record->class->grade} - {$record->class->major->major_name} - {$record->class->class_name}";
                    })
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => Student::getStatusLabel($state))
                    ->color(fn($state) => Student::getStatusColor($state)),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'class_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'graduated' => 'Lulus',
                        'move' => 'Pindah',
                        'dropout' => 'Dropout (DO)',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn($record) => 'Hapus Siswa: ' . $record->student_name)
                    ->modalDescription(
                        fn($record) =>
                        $record->hasRelatedData()
                            ? "⚠️ PERINGATAN: Menghapus siswa '{$record->student_name}' (NIS: {$record->nis}) akan menghapus SEMUA data peminjaman dan riwayat terkait siswa ini. Tindakan ini TIDAK DAPAT DIBATALKAN!"
                            : "Apakah Anda yakin ingin menghapus siswa '{$record->student_name}' (NIS: {$record->nis})?"
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->form(fn($record) => $record->hasRelatedData() ? [
                        Section::make('Verifikasi Penghapusan')
                            ->description('⚠️ Siswa ini memiliki data peminjaman. Untuk keamanan, masukkan email dan password Anda.')
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
                    ] : [])
                    ->action(function ($record, array $data) {
                        $user = Auth::user();

                        if ($record->hasRelatedData()) {
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

                        $studentName = $record->student_name;
                        $nis = $record->nis;
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Siswa Dihapus')
                            ->body("Siswa '{$studentName}' (NIS: {$nis}) berhasil dihapus.")
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_with_verification')
                        ->label('Hapus yang Dipilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Siswa Terpilih')
                        ->modalDescription(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                return '⚠️ PERINGATAN: Beberapa siswa yang dipilih memiliki data peminjaman. Menghapus siswa akan menghapus SEMUA data peminjaman terkait!';
                            }

                            return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' siswa yang dipilih?';
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->form(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            return $hasRelated ? [
                                Section::make('Verifikasi Penghapusan Massal')
                                    ->description('⚠️ Beberapa siswa memiliki data peminjaman. Masukkan email dan password Anda.')
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
                                ->title('Siswa Dihapus')
                                ->body("{$count} siswa berhasil dihapus.")
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('student_name', 'asc')
            ->paginationPageOptions([10, 25, 50]);
    }
}
