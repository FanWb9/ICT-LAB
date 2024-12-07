<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBarang extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_siswa',
        'nama_barang',
        'quantity',
        'status',
        'kelas',
        'image',
        'guru_mapel',
        'ruangan',
        'description',
        
    ];
    public function scopeToday($query)
{
    return $query->whereDate('created_at', Carbon::today());
}

}
