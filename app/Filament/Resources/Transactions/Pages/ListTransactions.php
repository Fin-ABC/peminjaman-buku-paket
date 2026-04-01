<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export_all')
                ->label('Export Semua')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new TransactionExport(),
                        'Data_Transaksi_' . now()->format('d-m-Y') . '.xlsx'
                    );
                }),
        ];
    }
}
