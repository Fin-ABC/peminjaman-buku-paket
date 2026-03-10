<?php

namespace App\Filament\Resources\Classes\Schemas;

use App\Models\Major;
use App\Models\SchoolYear;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassesForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeYear = SchoolYear::where('is_active', true)->first();

        return $schema
            ->components([
                // Select::make('grade')
                //     ->options([10 => '10', '11', '12'])
                //     ->required(),
                // TextInput::make('major_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('year_id')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('class_name')
                //     ->required(),

                TextInput::make('class_name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: RPL 1')
                    ->required()
                    ->maxLength(50),

                Select::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::pluck('major_name', 'id'))
                    ->searchable()
                    ->required()
                    ->preload(),

                Select::make('year_id')
                    ->label('Tahun Masuk')
                    ->options(SchoolYear::pluck('year_name', 'id'))
                    ->searchable()
                    ->required()
                    ->default($activeYear?->id)
                    ->helperText('Tahun ajaran saat siswa kelas ini masuk pertama kali')
                    ->preload(),

                // Checkbox Tingkat Kelas
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
                            ->label('Tingkat')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
