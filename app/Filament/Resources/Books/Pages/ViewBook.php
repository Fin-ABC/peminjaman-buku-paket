<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Hapus Buku: ' . $record->title)
                ->modalDescription(
                    fn($record) =>
                    $record->hasRelatedData()
                        ? "⚠️ PERINGATAN: Menghapus buku '{$record->title}' akan menghapus SEMUA data peminjaman terkait buku ini. Tindakan ini TIDAK DAPAT DIBATALKAN!"
                        : "Apakah Anda yakin ingin menghapus buku '{$record->title}'?"
                )
                ->modalSubmitActionLabel('Ya, Hapus')
                ->form(fn($record) => $record->hasRelatedData() ? [
                    Section::make('Verifikasi Penghapusan')
                        ->description('⚠️ Data ini memiliki data terkait. Untuk keamanan, masukkan email dan password Anda.')
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

                    $title = $record->title;
                    $record->delete();

                    Notification::make()
                        ->success()
                        ->title('Buku Dihapus')
                        ->body("Buku '{$title}' berhasil dihapus.")
                        ->send();
                }),
        ];
    }
}
