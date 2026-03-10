<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditClasses extends EditRecord
{
    protected static string $resource = ClassesResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set checkbox berdasarkan grade
        $data['grade_10'] = isset($data['grade']) && $data['grade'] === '10';
        $data['grade_11'] = isset($data['grade']) && $data['grade'] === '11';
        $data['grade_12'] = isset($data['grade']) && $data['grade'] === '12';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus checkbox helper fields
        unset($data['grade_10'], $data['grade_11'], $data['grade_12']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Hapus Kelas: ' . $record->class_name)
                ->modalDescription(
                    fn($record) =>
                    $record->hasRelatedData()
                        ? "⚠️ PERINGATAN: Menghapus kelas '{$record->class_name}' akan menghapus SEMUA data siswa yang terdaftar di kelas ini. Tindakan ini TIDAK DAPAT DIBATALKAN!"
                        : "Apakah Anda yakin ingin menghapus kelas '{$record->class_name}'?"
                )
                ->modalSubmitActionLabel('Ya, Hapus')
                ->form(fn($record) => $record->hasRelatedData() ? [
                    Section::make('Verifikasi Penghapusan')
                        ->description('⚠️ Kelas ini memiliki data siswa. Untuk keamanan, masukkan email dan password Anda.')
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

                    $className = $record->class_name;
                    $record->delete();

                    Notification::make()
                        ->success()
                        ->title('Kelas Dihapus')
                        ->body("Kelas '{$className}' berhasil dihapus.")
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
