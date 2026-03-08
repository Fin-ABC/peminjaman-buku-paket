<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if (empty($data['grade'])) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Silakan pilih tingkat kelas terlebih dahulu.')
                ->persistent()
                ->send();

            $this->halt();
        }

        if (empty($data['semester'])) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Silakan pilih semester terlebih dahulu.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Hapus checkbox helper fields
        unset($data['grade_10'], $data['grade_11'], $data['grade_12']);
        unset($data['semester_odd'], $data['semester_even']);

        // Set remaining_stock = total_stock jika kosong
        if (empty($data['remaining_stock'])) {
            $data['remaining_stock'] = $data['total_stock'];
        }

        $data['book_code'] = null;
        return $data;
    }

    protected function afterCreate(): void
    {
        // Generate book_code setelah record dibuat (sudah punya ID)
        $this->record->load('subject', 'major');
        $this->record->generateBookCode();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
