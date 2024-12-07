<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Inventaris;

class InventarisStat extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil tahun saat ini
        $currentYear = Carbon::now()->year;

        // Total barang berdasarkan quantity pada tahun ini
        $totalQuantity = Inventaris::whereYear('created_at', $currentYear)
            ->sum('quantity');

        // Hitung total quantity berdasarkan kondisi pada tahun ini
        $bisaDigunakan = Inventaris::whereYear('created_at', $currentYear)
            ->where('kerusakan', 'Bisa-Digunakan')
            ->sum('quantity');

        $kerusakanKecil = Inventaris::whereYear('created_at', $currentYear)
            ->where('kerusakan', 'Kerusakan-kecil')
            ->sum('quantity');

        $kerusakanSedang = Inventaris::whereYear('created_at', $currentYear)
            ->where('kerusakan', 'Kerusakan-sedang')
            ->sum('quantity');

        $kerusakanBesar = Inventaris::whereYear('created_at', $currentYear)
            ->where('kerusakan', 'Kerusakan-besar')
            ->sum('quantity');

        // Total semua barang rusak
        $totalRusak = $kerusakanKecil + $kerusakanSedang + $kerusakanBesar;

        // Total jumlah produk unik berdasarkan kd_barang
        $jumlahProdukUnik = Inventaris::whereYear('created_at', $currentYear)
            ->distinct('kd_barang')
            ->count('kd_barang');

        // Hitung persentase berdasarkan quantity
        $persenBisaDigunakan = $this->hitungPersentase($bisaDigunakan, $totalQuantity);
        $persenKerusakanKecil = $this->hitungPersentase($kerusakanKecil, $totalQuantity);
        $persenKerusakanSedang = $this->hitungPersentase($kerusakanSedang, $totalQuantity);
        $persenKerusakanBesar = $this->hitungPersentase($kerusakanBesar, $totalQuantity);

        return [
            Stat::make('Jumlah Barang', $jumlahProdukUnik)
                ->description('Total Barang Di ICT')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Bisa Digunakan', $bisaDigunakan)
                ->description("{$persenBisaDigunakan}% dari total Barang")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Kerusakan Kecil', $kerusakanKecil)
                ->description("{$persenKerusakanKecil}% dari total Barang")
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('primary'),

            Stat::make('Kerusakan Sedang', $kerusakanSedang)
                ->description("{$persenKerusakanSedang}% dari total Barang")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Kerusakan Besar', $kerusakanBesar)
                ->description("{$persenKerusakanBesar}% dari total Barang")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Barang Rusak', $totalRusak)
                ->description('Jumlah total barang rusak')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color('danger'),
        ];
    }

    // Fungsi untuk menghitung persentase
    private function hitungPersentase($jumlah, $total)
    {
        if ($total === 0) {
            return 0; // Hindari pembagian dengan nol
        }

        return round(($jumlah / $total) * 100, 2);
    }
}
