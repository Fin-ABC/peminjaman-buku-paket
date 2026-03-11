<?php

namespace App\Filament\Resources\TransactionDetails\Schemas;

use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransactionDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('transaction_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('student_id')
                //     ->required()
                //     ->numeric(),
                // Select::make('status')
                //     ->options(['Borrowed' => 'Borrowed', 'Returned' => 'Returned', 'Overdue' => 'Overdue'])
                //     ->required(),
                // DatePicker::make('return_date')
                //     ->required(),
                // Textarea::make('note')
                //     ->columnSpanFull(),

                Select::make('transaction_id')
                    ->label('Transaksi')
                    ->options(function () {
                        return Transaction::with(['book', 'class.major', 'schoolYear'])
                            ->get()
                            ->mapWithKeys(function ($transaction) {
                                $label = "{$transaction->book->title} - Kelas {$transaction->class->grade} {$transaction->class->major->major_name} - {$transaction->class->class_name} ({$transaction->schoolYear->year_name} - " . ($transaction->semester === 'odd' ? 'Ganjil' : 'Genap') . ")";
                                return [$transaction->id => $label];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        // Reset student_id jika transaksi berubah
                        $set('student_id', null);
                    })
                    ->helperText('Pilih transaksi peminjaman terlebih dahulu'),

                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function ($get, $record) {
                        $transactionId = $get('transaction_id');

                        if (!$transactionId) {
                            return [];
                        }

                        $transaction = Transaction::find($transactionId);

                        if (!$transaction) {
                            return [];
                        }

                        // Get siswa yang sudah ada di transaction_details untuk transaksi ini
                        $excludedStudentIds = TransactionDetail::where('transaction_id', $transactionId)
                            ->when($record, fn($q) => $q->where('id', '!=', $record->id)) // Exclude current record saat edit
                            ->pluck('student_id')
                            ->toArray();

                        // Get semua siswa dari kelas yang melakukan transaksi, kecuali yang sudah pinjam
                        return Student::where('class_id', $transaction->class_id)
                            ->whereNotIn('id', $excludedStudentIds)
                            ->orderBy('student_name')
                            ->pluck('student_name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->disabled(fn($get) => !$get('transaction_id'))
                    ->helperText(fn($get) => !$get('transaction_id')
                        ? 'Pilih transaksi terlebih dahulu'
                        : 'Hanya menampilkan siswa dari kelas yang melakukan transaksi dan belum meminjam buku ini'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Borrowed' => 'Dipinjam',
                        'Returned' => 'Dikembalikan',
                        'Overdue' => 'Terlambat',
                    ])
                    ->default('Borrowed')
                    ->required()
                    ->native(false)
                    ->helperText('Status peminjaman saat ini'),

                DatePicker::make('return_date')
                    ->label('Tanggal Harus Kembali')
                    ->required()
                    ->default(now()->addDays(14)) // Default 2 minggu dari sekarang
                    ->minDate(now())
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->helperText('Batas waktu pengembalian buku'),

                Textarea::make('note')
                    ->label('Catatan')
                    ->placeholder('Catatan tambahan (opsional)')
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('Catatan mengenai kondisi buku atau informasi lainnya')
                    ->columnSpanFull(),
            ]);
    }
}
