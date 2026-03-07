<?php

namespace App\Filament\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(
                [
                    Grid::make(3)->schema([
                        TextInput::make('year_start')
                            ->label('Tahun Awal')
                            ->placeholder('2025')
                            ->numeric()
                            ->length(4)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $yearEnd = $get('year_end');
                                if ($state && $yearEnd) {
                                    $set('year_name', $state . '/' . $yearEnd);
                                }
                            })
                            ->afterContent(
                                TextEntry::make('separator')
                                    ->label('/')
                                    ->extraAttributes(['class' => 'flex items-center justify-center text-2xl font-bold pt-6']),
                            ),

                        TextInput::make('year_end')
                            ->label('Tahun Akhir')
                            ->placeholder('2026')
                            ->numeric()
                            ->length(4)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $yearStart = $get('year_start');
                                if ($state && $yearStart) {
                                    $set('year_name', $yearStart . '/' . $state);
                                }
                            })
                            ->rules([
                                fn(callable $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $yearStart = $get('year_start');
                                    if ($yearStart && $value && $value != ($yearStart + 1)) {
                                        $fail('Tahun akhir harus 1 tahun setelah tahun awal (contoh: 2025/2026)');
                                    }
                                },
                            ]),
                    ]),

                    TextInput::make('year_name')
                        ->label('Nama Tahun Ajaran (otomatis)')
                        ->placeholder('2025/2026')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->unique(ignoreRecord: true),

                    Toggle::make('is_active')
                        ->label('Aktifkan Tahun Ajaran Ini')
                        ->helperText('Hanya satu tahun ajaran yg bisa aktif. Mengaktifkan tahun ini akan menonaktifkan tahun ajaran lainnya')
                        ->default(false)
                        ->inline(false),
                ],
            );
    }
}
