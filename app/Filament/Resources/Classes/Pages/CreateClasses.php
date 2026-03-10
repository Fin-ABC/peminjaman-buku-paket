<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClasses extends CreateRecord
{
    protected static string $resource = ClassesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['grade_10'], $data['grade_11'], $data['grade_12']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
