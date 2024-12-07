<?php

namespace App\Filament\Resources\InventarisResource\Pages;

use App\Filament\Resources\InventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListInventaris extends ListRecords
{
    protected static string $resource = InventarisResource::class;

    // Menambahkan aksi di header
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(), // Aksi untuk membuat inventaris baru
        ];
    }

    // Menambahkan widget di header
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\InventarisStat::class, // Widget untuk menampilkan statistik inventaris
        ];
    }

    // Menambahkan tabs untuk filter data berdasarkan kerusakan
    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua Barang')
                ->query(fn ($query) => $query), // Menampilkan semua data tanpa filter
            'available' => Tab::make('Bisa Digunakan')
                ->query(fn ($query) => $query->where('kerusakan', 'Bisa Digunakan')),
            'minor_damage' => Tab::make('Kerusakan Kecil')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan Kecil')),
            'moderate_damage' => Tab::make('Kerusakan Sedang')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan Sedang')),
            'severe_damage' => Tab::make('Kerusakan Besar')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan Besar')),
        ];
    }
}
