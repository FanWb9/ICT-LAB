<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;

class PeminjamanBarangStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        // Hitung jumlah siswa yang meminjam hari ini
        $peminjamanHariIni = PeminjamanBarang::whereDate('created_at', Carbon::today())
            ->select('nama_siswa')
            ->distinct()
            ->count('nama_siswa'); 

        // Hitung jumlah siswa yang sudah mengembalikan barang
        $sudahDikembalikan = PeminjamanBarang::whereDate('created_at', Carbon::today())
            ->where('status', 'Done') 
            ->select('nama_siswa')
            ->distinct()
            ->count('nama_siswa');

        // Hitung jumlah siswa yang belum mengembalikan barang
        $belumDikembalikan = PeminjamanBarang::whereDate('created_at', Carbon::today())
            ->where('status', 'Proses')
            ->select('nama_siswa')
            ->distinct()
            ->count('nama_siswa');

       
        return [
            Card::make('Jumlah siswa', $peminjamanHariIni > 0 ? $peminjamanHariIni : '-')
                ->description($peminjamanHariIni > 0 ? 'Siswa yang meminjam Barang' : 'Belum ada peminjaman hari ini')
                ->color($peminjamanHariIni > 0 ? 'primary' : 'warning')
                ->icon('heroicon-o-rectangle-stack'),

            Card::make('Sudah Dikembalikan', $sudahDikembalikan)
                ->description('Jumlah siswa yang sudah mengembalikan barang')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Card::make('Belum Dikembalikan', $belumDikembalikan)
                ->description('Jumlah siswa yang belum mengembalikan barang')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
