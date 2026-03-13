<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Major;
use App\Models\Subject;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BooksTable
{
    protected static function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                BulkAction::make('delete_with_verification')
                    ->label('Hapus yang Dipilih')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Buku Terpilih')
                    ->modalDescription(function ($records) {
                        $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                        if ($hasRelated) {
                            return '⚠️ PERINGATAN: Beberapa buku yang dipilih memiliki data peminjaman. Menghapus buku ini akan menghapus SEMUA data terkait!';
                        }

                        return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' buku yang dipilih?';
                    })
                    ->modalSubmitActionLabel('Ya, Hapus Semua')
                    ->form(function ($records) {
                        $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                        return $hasRelated ? [
                            Section::make('Verifikasi Penghapusan Massal')
                                ->description('⚠️ Beberapa data memiliki data terkait. Masukkan email dan password Anda.')
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
                            ->title('Buku Dihapus')
                            ->body("{$count} buku berhasil dihapus.")
                            ->send();
                    }),
            ]),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('book_code')
                //     ->searchable(),
                // TextColumn::make('title')
                //     ->searchable(),
                // TextColumn::make('subject.id')
                //     ->searchable(),
                // TextColumn::make('major.id')
                //     ->searchable(),
                // TextColumn::make('grade')
                //     ->badge(),
                // TextColumn::make('semester')
                //     ->badge(),
                // TextColumn::make('total_stock')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('remaining_stock')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('book_code')
                    ->label('Kode Buku')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),
                TextColumn::make('subject.subject_name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('major.major_name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('grade')
                    ->label('Tingkat')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => 'Kelas ' . $state),
                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'odd' ? 'Ganjil' : 'Genap')
                    ->color(fn($state) => $state === 'odd' ? 'warning' : 'purple'),
                TextColumn::make('total_stock')
                    ->label('Total Stok')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('remaining_stock')
                    ->label('Sisa Stok')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($state, $record) => $state === 0 ? 'danger' : ($state < ($record->total_stock / 2) ? 'warning' : 'success')),
                TextColumn::make('damaged_count')
                    ->label('Rusak')
                    ->alignCenter()
                    ->color('warning'),

                TextColumn::make('lost_count')
                    ->label('Hilang')
                    ->alignCenter()
                    ->color('danger'),
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
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::pluck('subject_name', 'id'))
                    ->searchable(),

                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::pluck('major_name', 'id'))
                    ->searchable(),

                SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                    ]),

                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        'odd' => 'Ganjil',
                        'even' => 'Genap',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
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
            ])
            ->bulkActions(self::getTableBulkActions())
            ->defaultSort('created_at', 'desc')
            ->paginated(25, 50, 100);
    }
}
