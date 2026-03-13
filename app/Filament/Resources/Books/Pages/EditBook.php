<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set checkbox berdasarkan grade
        $data['grade_10'] = $data['grade'] === '10';
        $data['grade_11'] = $data['grade'] === '11';
        $data['grade_12'] = $data['grade'] === '12';

        // Set checkbox berdasarkan semester
        $data['semester_odd'] = $data['semester'] === 'odd';
        $data['semester_even'] = $data['semester'] === 'even';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus checkbox helper fields
        unset($data['grade_10'], $data['grade_11'], $data['grade_12']);
        unset($data['semester_odd'], $data['semester_even']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Re-generate book_code jika ada perubahan subject, major, grade, atau semester
        $this->record->load('subject', 'major');
        $this->record->generateBookCode();

        if ($this->record->isDirty('total_stock')) {
            $this->record->syncBookItems($this->record->total_stock);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
