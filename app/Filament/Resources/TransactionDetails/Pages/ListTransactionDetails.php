<?php

namespace App\Filament\Resources\TransactionDetails\Pages;

use App\Filament\Resources\TransactionDetails\TransactionDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactionDetails extends ListRecords
{
    protected static string $resource = TransactionDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
