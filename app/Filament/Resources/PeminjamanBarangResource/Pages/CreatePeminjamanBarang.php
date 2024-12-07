<?php

namespace App\Filament\Resources\PeminjamanBarangResource\Pages;

use App\Filament\Resources\PeminjamanBarangResource;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjamanBarang extends CreateRecord
{
    protected static string $resource = PeminjamanBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

   
}
