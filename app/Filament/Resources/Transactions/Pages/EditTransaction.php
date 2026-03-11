<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(fn($record) => 'Hapus Transaksi')
                ->modalDescription(
                    fn($record) =>
                    $record->hasRelatedData()
                        ? "⚠️ PERINGATAN: Menghapus transaksi ini akan menghapus SEMUA detail peminjaman siswa ({$record->transactionDetails->count()} siswa). Tindakan ini TIDAK DAPAT DIBATALKAN!"
                        : "Apakah Anda yakin ingin menghapus transaksi peminjaman buku '{$record->book->title}' untuk kelas {$record->class->class_name}?"
                )
                ->modalSubmitActionLabel('Ya, Hapus')
                ->form(fn($record) => $record->hasRelatedData() ? [
                    Section::make('Verifikasi Penghapusan')
                        ->description('⚠️ Transaksi ini memiliki detail peminjaman siswa. Untuk keamanan, masukkan email dan password Anda.')
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

                    $bookTitle = $record->book->title;
                    $className = $record->class->class_name;
                    $record->delete();

                    Notification::make()
                        ->success()
                        ->title('Transaksi Dihapus')
                        ->body("Transaksi peminjaman '{$bookTitle}' untuk kelas {$className} berhasil dihapus.")
                        ->send();
                }),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
