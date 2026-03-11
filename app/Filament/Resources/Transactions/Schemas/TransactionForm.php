<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Book;
use App\Models\Classes;
use App\Models\SchoolYear;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeYear = SchoolYear::where('is_active', true)->first();

        return $schema
            ->components([
                // TextInput::make('book_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('class_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('year_id')
                //     ->required()
                //     ->numeric(),
                // Select::make('semester')
                //     ->options(['even' => 'Even', 'odd' => 'Odd'])
                //     ->required(),
                // DatePicker::make('transaction_date')
                //     ->required(),
                // Toggle::make('is_all_returned')
                //     ->required(),

                Select::make('class_id')
                    ->label('Kelas')
                    ->options(function () {
                        return Classes::with(['major', 'schoolYear'])
                            ->get()
                            ->mapWithKeys(function ($class) {
                                $label = "Kelas {$class->grade} - {$class->major->major_name} - {$class->class_name} ({$class->schoolYear->year_name})";
                                return [$class->id => $label];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        // Reset book_id jika kelas berubah
                        $set('book_id', null);
                    })
                    ->helperText('Pilih kelas terlebih dahulu untuk melihat buku yang tersedia'),

                Select::make('book_id')
                    ->label('Buku')
                    ->options(function ($get) {
                        $classId = $get('class_id');

                        if (!$classId) {
                            return [];
                        }

                        $class = Classes::find($classId);

                        if (!$class) {
                            return [];
                        }

                        // Filter buku berdasarkan grade dan major dari kelas
                        return Book::with('subject')
                            ->where('grade', $class->grade)
                            ->where('major_id', $class->major_id)
                            ->get()
                            ->mapWithKeys(function ($book) {
                                $label = "{$book->title} - {$book->subject->subject_name} (Stok: {$book->remaining_stock})";
                                return [$book->id => $label];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->disabled(fn($get) => !$get('class_id'))
                    ->helperText(fn($get) => !$get('class_id')
                        ? 'Pilih kelas terlebih dahulu'
                        : 'Hanya menampilkan buku sesuai tingkat dan jurusan kelas yang dipilih'),

                Select::make('year_id')
                    ->label('Tahun Ajaran')
                    ->options(SchoolYear::pluck('year_name', 'id'))
                    ->searchable()
                    ->required()
                    ->default($activeYear?->id)
                    ->preload()
                    ->helperText('Tahun ajaran saat transaksi terjadi'),

                Select::make('semester')
                    ->label('Semester')
                    ->options([
                        'odd' => 'Ganjil',
                        'even' => 'Genap',
                    ])
                    ->required()
                    ->native(false)
                    ->helperText('Semester saat transaksi terjadi'),

                DatePicker::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->required()
                    ->default(now())
                    ->maxDate(now())
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->helperText('Tanggal peminjaman buku'),

                Toggle::make('is_all_returned')
                    ->label('Semua Buku Sudah Dikembalikan')
                    ->default(true)
                    ->disabled(fn(string $operation): bool => $operation === 'create')
                    ->dehydrated()
                    ->helperText(
                        fn(string $operation): string =>
                        $operation === 'create'
                            ? 'Tidak dapat diubah saat membuat transaksi baru (default: Ya)'
                            : 'Mengubah ini akan mempengaruhi status semua detail transaksi'
                    )->inline(false),
            ]);
    }
}
