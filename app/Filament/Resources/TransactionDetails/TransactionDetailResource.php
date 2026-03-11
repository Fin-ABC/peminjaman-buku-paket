<?php

namespace App\Filament\Resources\TransactionDetails;

use App\Filament\Resources\TransactionDetails\Pages\CreateTransactionDetail;
use App\Filament\Resources\TransactionDetails\Pages\EditTransactionDetail;
use App\Filament\Resources\TransactionDetails\Pages\ListTransactionDetails;
use App\Filament\Resources\TransactionDetails\Pages\ViewTransactionDetail;
use App\Filament\Resources\TransactionDetails\Schemas\TransactionDetailForm;
use App\Filament\Resources\TransactionDetails\Schemas\TransactionDetailInfolist;
use App\Filament\Resources\TransactionDetails\Tables\TransactionDetailsTable;
use App\Models\TransactionDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionDetailResource extends Resource
{
    protected static ?string $model = TransactionDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Detail Transaksi';

    public static function form(Schema $schema): Schema
    {
        return TransactionDetailForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionDetailInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionDetailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionDetails::route('/'),
            'create' => CreateTransactionDetail::route('/create'),
            'view' => ViewTransactionDetail::route('/{record}'),
            'edit' => EditTransactionDetail::route('/{record}/edit'),
        ];
    }
}
