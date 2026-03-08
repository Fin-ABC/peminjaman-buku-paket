<?php

namespace App\Filament\Resources\Majors;

use App\Filament\Resources\Majors\Pages\ManageMajors;
use App\Models\Major;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MajorResource extends Resource
{
    protected static ?string $model = Major::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Jurusan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('major_code')
                    ->label('Kode Jurusan')
                    ->placeholder('Contoh: RPL')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->rules(['regex:/^[A-Z0-9]+$/'])
                    ->validationMessages([
                        'regex' => 'Kode jurusan harus huruf besar dan angka saja (tanpa spasi atau karakter khusus).',
                    ])
                    ->helperText('Kode otomatis dibuat dari nama jurusan, tapi Anda bisa mengubahnya.')
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('major_code', strtoupper($state));
                    }),
                TextInput::make('major_name')
                    ->label('Nama Jurusan')
                    ->placeholder('Contoh: Rekayasa Perangkat Lunak')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get, $operation) {
                        if ($state && ($operation === 'create' || !$get('major_code'))) {
                            $code = Major::generateCode($state);
                            $set('major_code', $code);
                        }
                    })
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('major_code'),
                TextEntry::make('major_name'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Jurusan')
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('major_code')
                    ->label('Kode Jurusan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('major_name')
                    ->label('Nama Jurusan')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->size('lg'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn(Major $record) => 'Hapus Jurusan ' . $record->major_name)
                    ->modalDescription(
                        fn(Major $record) =>
                        $record->hasRelatedData()
                            ? "⚠️ PERINGATAN: Menghapus jurusan {$record->major_name} akan menghapus SEMUA data terkait. Tindakan ini TIDAK DAPAT DIBATALKAN!"
                            : "Apakah Anda yakin ingin menghapus jurusan {$record->major_name}?"
                    )
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->form(fn(Major $record) => $record->hasRelatedData() ? [
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
                    ->action(function (Major $record, array $data) {
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

                        $majorName = $record->major_name;
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Jurusan Dihapus')
                            ->body("Jurusan {$majorName} berhasil dihapus.")
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                   DeleteBulkAction::make('delete_with_verification')
                        ->label('Hapus yang Dipilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Terpilih')
                        ->modalDescription(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            if ($hasRelated) {
                                return '⚠️ PERINGATAN: Beberapa data yang dipilih memiliki data terkait. Menghapus data ini akan menghapus SEMUA data terkait. Tindakan ini TIDAK DAPAT DIBATALKAN!';
                            }

                            return 'Apakah Anda yakin ingin menghapus ' . $records->count() . ' data yang dipilih?';
                        })
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->form(function ($records) {
                            $hasRelated = $records->contains(fn($record) => $record->hasRelatedData());

                            return $hasRelated ? [
                                Section::make('Verifikasi Penghapusan Massal')
                                    ->description('⚠️ Beberapa data memiliki data terkait. Untuk keamanan, masukkan email dan password Anda.')
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

                            // Validasi email dan password jika ada data terkait
                            if ($hasRelated) {
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

                            // Hapus semua records yang dipilih
                            $count = $records->count();
                            $records->each->delete();

                            Notification::make()
                                ->success()
                                ->title('Data Dihapus')
                                ->body("{$count} data jurusan berhasil dihapus.")
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMajors::route('/'),
        ];
    }
}
