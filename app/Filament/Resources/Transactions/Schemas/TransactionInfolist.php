<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('book_id')
                    ->numeric(),
                TextEntry::make('class_id')
                    ->numeric(),
                TextEntry::make('year_id')
                    ->numeric(),
                TextEntry::make('semester')
                    ->badge(),
                TextEntry::make('transaction_date')
                    ->date(),
                IconEntry::make('is_all_returned')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
