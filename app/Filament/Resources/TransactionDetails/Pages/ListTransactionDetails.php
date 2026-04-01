<?php

namespace App\Filament\Resources\TransactionDetails\Pages;

use App\Exports\TransactionDetailExport;
use App\Filament\Resources\TransactionDetails\TransactionDetailResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactionDetails extends ListRecords
{
    protected static string $resource = TransactionDetailResource::class;

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
                        new TransactionDetailExport(),
                        'Data_Detail_Transaksi_' . now()->format('d-m-Y') . '.xlsx'
                    );
                }),
        ];
    }
}
