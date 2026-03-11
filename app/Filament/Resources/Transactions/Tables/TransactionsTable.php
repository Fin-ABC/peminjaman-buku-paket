<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkAction;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('book_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('class_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('year_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('semester')
                //     ->badge(),
                // TextColumn::make('transaction_date')
                //     ->date()
                //     ->sortable(),
                // IconColumn::make('is_all_returned')
                //     ->boolean(),
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

                TextColumn::make('book.title')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('book.subject.subject_name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('class.class_name')
                    ->label('Kelas')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return "Kelas {$record->class->grade} - {$record->class->major->major_name} - {$record->class->class_name}";
                    })
                    ->wrap(),

                TextColumn::make('schoolYear.year_name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'odd' ? 'Ganjil' : 'Genap')
                    ->color(fn($state) => $state === 'odd' ? 'warning' : 'purple'),

                TextColumn::make('transaction_date')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_all_returned')
                    ->label('Semua Dikembalikan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('transactionDetails_count')
                    ->label('Jumlah Peminjam')
                    ->counts('transactionDetails')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('book.subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('book.subject', 'subject_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'class_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        'odd' => 'Ganjil',
                        'even' => 'Genap',
                    ])
                    ->multiple(),

                SelectFilter::make('year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('schoolYear', 'year_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('is_all_returned')
                    ->label('Status Pengembalian')
                    ->options([
                        '1' => 'Sudah Semua',
                        '0' => 'Belum Semua',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_with_verification')
                        ->label('Hapus yang Dipilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Transaksi Terpilih')
                        ->modalDescription(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                $totalDetails = $records->sum(fn($r) => $r->transactionDetails->count());
                                return "⚠️ PERINGATAN: Transaksi yang dipilih memiliki {$totalDetails} detail peminjaman siswa. Menghapus akan menghapus SEMUA data detail!";
                            }

                            return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' transaksi yang dipilih?';
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->form(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            return $hasRelated ? [
                                Section::make('Verifikasi Penghapusan Massal')
                                    ->description('⚠️ Beberapa transaksi memiliki detail peminjaman. Masukkan email dan password Anda.')
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
                                ->title('Transaksi Dihapus')
                                ->body("{$count} transaksi berhasil dihapus.")
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->paginationPageOptions([10, 25, 50, 100]);
    }
}
