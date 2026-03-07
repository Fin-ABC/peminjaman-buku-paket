<?php

namespace App\Filament\Resources\SchoolYears\Pages;

use App\Filament\Resources\SchoolYears\SchoolYearResource;
use App\Models\SchoolYear;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ViewSchoolYear extends ViewRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

                    $yearName = $record->year_name;
                    $record->delete();

                    Notification::make()
                        ->success()
                        ->title('Tahun Ajaran Dihapus')
                        ->body("Tahun ajaran {$yearName} berhasil dihapus.")
                        ->send();
                }),
        ];
    }
}
