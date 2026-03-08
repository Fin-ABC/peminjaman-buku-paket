<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\Book;
use App\Models\Major;
use App\Models\Subject;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Buku')
                    ->placeholder('Contoh: Matematika Kelas XI Semester Ganjil')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::pluck('subject_name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::pluck('major_name', 'id'))
                    ->searchable()
                    ->required(),
                Grid::make(4)
                    ->schema([
                        Checkbox::make('grade_10')
                            ->label('Kelas 10')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('grade_11', false);
                                    $set('grade_12', false);
                                    $set('grade', '10');
                                }
                            }),

                        Checkbox::make('grade_11')
                            ->label('Kelas 11')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('grade_10', false);
                                    $set('grade_12', false);
                                    $set('grade', '11');

                                    Log::info("Grade 11 clicked, setting grade to: 11");
                                }
                            }),

                        Checkbox::make('grade_12')
                            ->label('Kelas 12')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('grade_10', false);
                                    $set('grade_11', false);
                                    $set('grade', '12');
                                }
                            }),

                        TextInput::make('grade')
                            // ->label('Tingkat (hidden)')
                            ->hiddenLabel()
                             ->extraAttributes(['class' => 'hidden'])
                            ->dehydrated()
                            ->disabled()
                            ->required(),
                    ]),
                Grid::make(3)
                    ->schema([
                        Checkbox::make('semester_odd')
                            ->label('Semester Ganjil')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('semester_even', false);
                                    $set('semester', 'odd');
                                }
                            }),

                        Checkbox::make('semester_even')
                            ->label('Semester Genap')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('semester_odd', false);
                                    $set('semester', 'even');
                                }
                            }),

                        TextInput::make('semester')
                            ->label('Semester (Hidden)')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ]),

                TextInput::make('book_code')
                    ->label('Kode Buku (Otomatis)')
                    ->placeholder('Akan dibuat otomatis setelah data disimpan')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Kode buku akan dibuat otomatis setelah data disimpan'),

                TextInput::make('total_stock')
                    ->label('Total Stok')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Auto-set remaining_stock = total_stock jika remaining_stock kosong
                        if (!$get('remaining_stock')) {
                            $set('remaining_stock', $state);
                        }
                    }),

                TextInput::make('remaining_stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Kosongkan untuk otomatis sama dengan total stok'),
            ]);
    }
}
