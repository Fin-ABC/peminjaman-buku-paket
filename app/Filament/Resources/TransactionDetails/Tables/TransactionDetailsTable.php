<?php

namespace App\Filament\Resources\TransactionDetails\Tables;

use App\Models\TransactionDetail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('transaction_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('student_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('status')
                //     ->badge(),
                // TextColumn::make('return_date')
                //     ->date()
                //     ->sortable(),
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

                TextColumn::make('transaction.book.title')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold')
                    ->description(
                        fn($record) =>
                        "Kelas {$record->transaction->class->grade} - {$record->transaction->class->major->major_name}"
                    ),

                TextColumn::make('student.student_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "NIS: {$record->student->nis}"),

                TextColumn::make('transaction.class.class_name')
                    ->label('Kelas')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return "Kelas {$record->transaction->class->grade} - {$record->transaction->class->major->major_name} - {$record->transaction->class->class_name}";
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Borrowed' => 'Dipinjam',
                        'Returned' => 'Dikembalikan',
                        'Overdue' => 'Terlambat',
                        default => $state,
                    })
                    ->color(fn($state) => TransactionDetail::getStatusColor($state)),

                TextColumn::make('return_date')
                    ->label('Tanggal Harus Kembali')
                    ->date('d M Y')
                    ->sortable()
                    ->color(
                        fn($record) =>
                        $record->status === 'Borrowed' && $record->return_date->isPast()
                            ? 'danger'
                            : 'gray'
                    ),

                TextColumn::make('note')
                    ->label('Catatan')
                    ->searchable()
                    ->wrap()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Tidak ada catatan'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Borrowed' => 'Dipinjam',
                        'Returned' => 'Dikembalikan',
                        'Overdue' => 'Terlambat',
                    ])
                    ->multiple(),

                SelectFilter::make('transaction.class_id')
                    ->label('Kelas')
                    ->relationship('transaction.class', 'class_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('transaction.class.grade')
                    ->label('Tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                    ])
                    ->attribute('transaction.class.grade')
                    // ->query(function ($query, $data) {
                        // if ($data['value']) {
                        //     $query->whereHas('transaction.class', function ($q) use ($data) {
                        //         $q->where('grade', $data['value']);
                        //     });
                        // }

                        // if (isset($data['values']) && !empty($data['values'])) {
                        //     $query->whereHas('transaction.class', function ($q) use ($data) {
                        //         $q->whereIn('grade', $data['values']);
                        //     });
                        // }
                    // })
                    ->multiple(),

                SelectFilter::make('transaction.class.major_id')
                    ->label('Jurusan')
                    ->relationship('transaction.class.major', 'major_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('return_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('return_from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('return_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['return_from'], fn($q, $date) => $q->whereDate('return_date', '>=', $date))
                            ->when($data['return_until'], fn($q, $date) => $q->whereDate('return_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 25, 50, 100, 'all']);
    }
}
