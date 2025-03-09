<?php

namespace App\Filament\Resources\AcheteurResource\Pages;

use App\Filament\Resources\AcheteurResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcheteur extends EditRecord
{
    protected static string $resource = AcheteurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
