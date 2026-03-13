<?php

namespace App\Filament\Resources\BookItems\Pages;

use App\Filament\Resources\BookItems\BookItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBookItem extends ViewRecord
{
    protected static string $resource = BookItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
