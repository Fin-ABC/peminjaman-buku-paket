<?php

namespace App\Filament\Resources\Classes\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('grade')
                //     ->badge(),
                // TextColumn::make('major_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('year_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('class_name')
                //     ->searchable(),
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

                TextColumn::make('class_name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('grade')
                    ->label('Tingkat')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => 'Kelas ' . $state)
                    ->color('primary'),

                TextColumn::make('major.major_name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('schoolYear.year_name')
                    ->label('Tahun Masuk')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                    ]),
                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'major_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('year_id')
                    ->label('Tahun Masuk')
                    ->relationship('schoolYear', 'year_name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            ])->paginated(10, 25, 50)
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete_with_verification')
                        ->label('Hapus yang Dipilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kelas Terpilih')
                        ->modalDescription(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                return '⚠️ PERINGATAN: Beberapa kelas yang dipilih memiliki data siswa. Menghapus kelas akan menghapus SEMUA data siswa terkait!';
                            }

                            return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' kelas yang dipilih?';
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->form(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            return $hasRelated ? [
                                Section::make('Verifikasi Penghapusan Massal')
                                    ->description('⚠️ Beberapa kelas memiliki data siswa. Masukkan email dan password Anda.')
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
                                ->title('Kelas Dihapus')
                                ->body("{$count} kelas berhasil dihapus.")
                                ->send();
                        }),
                ]),
            ]);
    }
}
