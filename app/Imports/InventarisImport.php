<?php
namespace App\Imports;

use App\Models\Inventaris;
use Maatwebsite\Excel\Concerns\ToModel;

class InventarisImport implements ToModel
{
    public function model(array $row)
    {
        return new Inventaris([
            'name' => $row[0], 
            'quantity' => $row[1],
            'kd_barang' => $row[2], 
            'kerusakan' => $row[3], 
            'description' => $row[4],
        ]);
    }
}
