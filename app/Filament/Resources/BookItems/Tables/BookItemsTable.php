<?php

namespace App\Filament\Resources\BookItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('item_code')
                    ->label('Kode Item')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin!')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('book.title')
                    ->label('Nama Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn ($record) => $record->book->subject->subject_name),

                // ✅ Inline editable select untuk kondisi
                SelectColumn::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good' => 'Baik',
                        'damaged' => 'Rusak',
                        'lost' => 'Hilang',
                    ])
                    ->sortable()
                    ->selectablePlaceholder(false),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('book_id')
                    ->label('Buku')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good' => 'Baik',
                        'damaged' => 'Rusak',
                        'lost' => 'Hilang',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Item Buku')
                    ->modalDescription(fn ($record) =>
                        "Apakah Anda yakin ingin menghapus item buku '{$record->book->title}' ({$record->item_code})? Total stok buku akan berkurang 1."
                    )
                    ->successNotificationTitle('Item Dihapus')
                    ->after(function ($record) {
                        // Total stock akan auto-update via model event
                    }),
            ])
            ->defaultSort('item_code', 'asc')
            ->paginationPageOptions([10, 25, 50, 100])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
