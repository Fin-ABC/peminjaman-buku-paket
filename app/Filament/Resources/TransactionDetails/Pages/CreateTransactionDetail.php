<?php

namespace App\Filament\Resources\TransactionDetails\Pages;

use App\Filament\Resources\TransactionDetails\TransactionDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionDetail extends CreateRecord
{
    protected static string $resource = TransactionDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
