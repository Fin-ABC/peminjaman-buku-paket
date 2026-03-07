<?php

namespace App\Filament\Resources\SchoolYears\Tables;

use App\Models\SchoolYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->rowIndex(),
                TextColumn::make('year_name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->weight('bold')
                    ->size('lg')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->beforeStateUpdated(function ($record, $state) {
                        // Jika akan diaktifkan dan sudah ada yang aktif
                        if ($state) {
                            $activeYear = SchoolYear::where('is_active', true)
                                ->where('id', '!=', $record->id)
                                ->first();

                            if ($activeYear) {
                                Notification::make()
                                    ->warning()
                                    ->title('Perubahan Tahun Aktif')
                                    ->body("Tahun ajaran {$activeYear->year_name} saat ini aktif. Mengaktifkan {$record->year_name} akan menonaktifkan tahun ajaran sebelumnya.")
                                    ->persistent()
                                    ->send();
                            }
                        }
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            Notification::make()
                                ->success()
                                ->title('Tahun Ajaran Diaktifkan')
                                ->body("Tahun ajaran {$record->year_name} sekarang aktif.")
                                ->send();
                        }
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn(SchoolYear $record) => 'Hapus Tahun Ajaran ' . $record->year_name)
                    ->modalDescription(
                        fn(SchoolYear $record) =>
                        $record->hasRelatedData()
                            ? "⚠️ PERINGATAN: Menghapus tahun ajaran {$record->year_name} akan menghapus SEMUA data terkait termasuk data peminjaman, data siswa, dan data buku yang terkait dengan tahun ini. Tindakan ini TIDAK DAPAT DIBATALKAN!"
                            : "Apakah Anda yakin ingin menghapus tahun ajaran {$record->year_name}?"
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->form(fn(SchoolYear $record) => $record->hasRelatedData() ? [
                        Section::make('Verifikasi Penghapusan')
                            ->description('⚠️ Data ini memiliki data terkait. Untuk keamanan, masukkan email dan password Anda untuk melanjutkan penghapusan.')
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
                    ->action(function (SchoolYear $record, array $data) {
                        // Get authenticated user
                        $user = Auth::user();

                        // Validasi email dan password jika ada data terkait
                        if ($record->hasRelatedData()) {
                            if (!isset($data['email']) || !isset($data['password'])) {
                                Notification::make()
                                    ->danger()
                                    ->title('Validasi Diperlukan')
                                    ->body('Email dan password wajib diisi untuk menghapus data ini.')
                                    ->persistent()
                                    ->send();
                                return;
                            }

                            if ($data['email'] !== $user->email) {
                                Notification::make()
                                    ->danger()
                                    ->title('Email Tidak Sesuai')
                                    ->body('Email yang Anda masukkan tidak sesuai dengan akun Anda.')
                                    ->persistent()
                                    ->send();
                                return;
                            }

                            if (!Hash::check($data['password'], $user->password)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Password Salah')
                                    ->body('Password yang Anda masukkan salah.')
                                    ->persistent()
                                    ->send();
                                return;
                            }
                        }

                        // Hapus record
                        $yearName = $record->year_name;
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Tahun Ajaran Dihapus')
                            ->body("Tahun ajaran {$yearName} berhasil dihapus.")
                            ->send();
                    }),
            ]);
    }
}
