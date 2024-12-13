<?php

namespace App\Filament\Widgets;

use App\Models\Inventaris;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class TabelInventarisWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Inventaris::query()
                    ->selectRaw("
                        MIN(id) AS id, -- Ambil ID terkecil untuk memastikan kunci unik
                        kd_barang,
                        name,
                        SUM(quantity) AS quantity,
                        SUM(CASE WHEN kerusakan = 'Bisa-Digunakan' THEN quantity ELSE 0 END) AS Bisa_Digunakan,
                        SUM(CASE WHEN kerusakan = 'Kerusakan-Kecil' THEN quantity ELSE 0 END) AS Kerusakan_Kecil,
                        SUM(CASE WHEN kerusakan = 'Kerusakan-Sedang' THEN quantity ELSE 0 END) AS Kerusakan_Sedang,
                        SUM(CASE WHEN kerusakan = 'Kerusakan-Besar' THEN quantity ELSE 0 END) AS Kerusakan_Besar
                    ")
                    ->groupBy('kd_barang', 'name') // Kelompokkan berdasarkan kolom yang diperlukan
                    ->orderBy('kd_barang') // Pastikan urutan berdasarkan kolom yang ada di GROUP BY
            )
            ->columns([
                Tables\Columns\TextColumn::make('kd_barang')
                    ->label('Kode Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah Barang'),
                Tables\Columns\TextColumn::make('Bisa_Digunakan')
                    ->label('Bisa Digunakan'),
                Tables\Columns\TextColumn::make('Kerusakan_Kecil')
                    ->label('Kerusakan Kecil'),
                Tables\Columns\TextColumn::make('Kerusakan_Sedang')
                    ->label('Kerusakan Sedang'),
                Tables\Columns\TextColumn::make('Kerusakan_Besar')
                    ->label('Kerusakan Besar'),
            ]);
    }
}
