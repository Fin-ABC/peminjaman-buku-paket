<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
