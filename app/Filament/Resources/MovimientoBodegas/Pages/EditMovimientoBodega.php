<?php

namespace App\Filament\Resources\MovimientoBodegas\Pages;

use App\Filament\Resources\MovimientoBodegas\MovimientoBodegaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMovimientoBodega extends EditRecord
{
    protected static string $resource = MovimientoBodegaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
