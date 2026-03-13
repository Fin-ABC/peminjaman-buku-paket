<?php

namespace App\Filament\Resources\BookItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('book_id')
                    ->required()
                    ->numeric(),
                TextInput::make('item_code'),
                Select::make('condition')
                    ->options(['good' => 'Good', 'damaged' => 'Damaged', 'lost' => 'Lost'])
                    ->default('good')
                    ->required(),
            ]);
    }
}
