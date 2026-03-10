<?php

namespace App\Filament\Resources\Classes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClassesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('grade')
                    ->badge(),
                TextEntry::make('major_id')
                    ->numeric(),
                TextEntry::make('year_id')
                    ->numeric(),
                TextEntry::make('class_name'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
