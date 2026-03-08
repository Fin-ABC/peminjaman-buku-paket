<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('book_code')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('subject.id')
                    ->label('Subject'),
                TextEntry::make('major.id')
                    ->label('Major'),
                TextEntry::make('grade')
                    ->badge(),
                TextEntry::make('semester')
                    ->badge(),
                TextEntry::make('total_stock')
                    ->numeric(),
                TextEntry::make('remaining_stock')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
