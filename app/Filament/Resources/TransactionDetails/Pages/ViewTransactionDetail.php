<?php

namespace App\Filament\Resources\TransactionDetails\Pages;

use App\Filament\Resources\TransactionDetails\TransactionDetailResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransactionDetail extends ViewRecord
{
    protected static string $resource = TransactionDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
