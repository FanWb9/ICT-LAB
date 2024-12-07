<?php

namespace App\Imports;
use Illuminate\Support\Facades\Storage;
use App\Models\PeminjamanBarang;
use Maatwebsite\Excel\Concerns\ToModel;

class PeminjamanBarangImport implements ToModel
{
   
    public function model(array $row)
    {
        return new PeminjamanBarang([
            'nama_siswa' => $row[0],
            'nama_barang' => $row[1],
            'quantity' => $row[2],
            'status' => $row[3],
            'kelas' => $row[4],
            'image' => $row[5],
            'guru_mapel' => $row[6],
            'ruangan' => $row[7],
            'description' => $row[8],
        ]);
    }
}
