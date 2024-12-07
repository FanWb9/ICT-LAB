<?php

namespace App\Filament\Resources\PeminjamanBarangResource\Pages;

use App\Filament\Resources\PeminjamanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeminjamanBarangs extends ListRecords
{
    protected static string $resource = PeminjamanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
