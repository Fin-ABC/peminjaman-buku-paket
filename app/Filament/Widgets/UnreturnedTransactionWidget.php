<?php

namespace App\Filament\Widgets;

use App\Models\Transaction as ModelsTransaction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Transaction;

class UnreturnedTransactionWidget extends TableWidget
{
    protected static ?string $heading = 'Peminjaman Belum Tuntas';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => ModelsTransaction::query()
                ->with('class.major', 'schoolYear', 'book')
                ->where('is_all_returned', false)
                ->orderBy('transaction_date', 'asc'))
            ->columns([
                TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->searchable(),
                TextColumn::make('class.grade')
                    ->label('Tingkat'),
                TextColumn::make('class.class_name')
                    ->label('Kelas'),
                TextColumn::make('class.major.major_name')
                    ->label('Jurusan'),
                TextColumn::make('schoolYear.year_name')
                    ->label('Tahun Ajaran'),
                TextColumn::make('semester')
                    ->label('Semester')
                    ->formatStateUsing(fn($state) => $state === 'odd' ? 'Ganjil' : 'Genap'),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y'),
            ])
            ->paginated([5, 10])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
