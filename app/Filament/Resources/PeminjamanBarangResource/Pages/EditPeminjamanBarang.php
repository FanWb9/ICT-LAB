<?php

namespace App\Filament\Resources\PeminjamanBarangResource\Pages;

use App\Filament\Resources\PeminjamanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeminjamanBarang extends EditRecord
{
    protected static string $resource = PeminjamanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
