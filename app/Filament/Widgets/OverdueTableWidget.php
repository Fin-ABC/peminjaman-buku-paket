<?php

namespace App\Filament\Widgets;

use App\Models\TransactionDetail as ModelsTransactionDetail;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use TransactionDetail;

class OverdueTableWidget extends TableWidget
{
    protected static ?string $heading = 'Daftar Keterlambatan';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => ModelsTransactionDetail::query()
                ->with('student', 'book_item.book', 'transaction')
                ->where('status', 'Overdue')
                ->orderBy('return_date', 'asc'))
            ->columns([
                TextColumn::make('student.student_name')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('book_item.book.title')
                    ->label('Judul Buku')
                    ->searchable(),
                TextColumn::make('book_item.item_code')
                    ->label('Kode Eksemplar'),
                TextColumn::make('return_date')
                    ->label('Batas Kembali')
                    ->date('d M Y')
                    ->color('danger'),
                TextColumn::make('return_date')
                    ->label('Keterlambatan')
                    ->state(fn ($record) => now()->diffInDays($record->return_date) . ' hari')
                    ->color('danger'),
            ])
            ->paginated(5,10)
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
