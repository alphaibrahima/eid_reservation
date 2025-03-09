<?php

namespace App\Filament\Resources\AcheteurResource\Pages;

use App\Filament\Resources\AcheteurResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAcheteur extends CreateRecord
{
    protected static string $resource = AcheteurResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone'] = $data['country_code'] . preg_replace('/\D/', '', $data['local_phone']);
        unset($data['country_code'], $data['local_phone']);
        return $data;
    }
}