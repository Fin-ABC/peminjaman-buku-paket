<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Classes;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nisn')
                        ->label('NISN')
                        ->placeholder('Contoh: 2024001')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->disabled(fn (string $operation): bool => $operation === 'edit')  // ✅ Disable saat edit
                        ->dehydrated()  // ✅ Tetap kirim data meski disabled
                        ->helperText(fn (string $operation): string =>
                            $operation === 'edit'
                                ? 'NISN tidak dapat diubah setelah dibuat'
                                : 'NISN (unik, tidak bisa diubah setelah dibuat)'
                        ),

                    TextInput::make('student_name')
                        ->label('Nama Lengkap')
                        ->placeholder('Contoh: Ahmad Fauzi')
                        ->required()
                        ->maxLength(255),

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
                        ->helperText('Pilih kelas tempat siswa terdaftar'),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif',
                            'graduated' => 'Lulus',
                            'move' => 'Pindah',
                            'dropout' => 'Dropout (DO)',
                        ])
                        ->default('active')
                        ->required()
                        ->native(false)
                        ->helperText('Status siswa saat ini'),
            ]);
    }
}
