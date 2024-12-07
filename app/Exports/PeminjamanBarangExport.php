<?php
namespace App\Exports;

use App\Models\PeminjamanBarang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanBarangExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected $peminjamanCollection;

    public function __construct()
    {
        $this->peminjamanCollection = PeminjamanBarang::all();
    }

    public function collection()
    {
        return $this->peminjamanCollection;
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Nama Barang',
            'Status',
            'Quantity',
            'Kelas',
            'Image',
            'Guru Mapel',
            'Ruangan',
            'Deskripsi',
        ];
    }

    public function title(): string
    {
        return 'Peminjaman Barang';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(20);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function map($peminjaman): array
    {
        return [
            $peminjaman->nama_siswa,
            $peminjaman->nama_barang,
            $peminjaman->status,
            $peminjaman->quantity,
            $peminjaman->kelas,
            $peminjaman->image,
            $peminjaman->guru_mapel,
            $peminjaman->ruangan,
            $peminjaman->description,
        ];
    }

    public function groupData(Worksheet $sheet)
    {
        $row = 2;
        $currentNamaSiswa = '';

        foreach ($this->peminjamanCollection as $peminjaman) {
            if ($currentNamaSiswa !== $peminjaman->nama_siswa) {
                $currentNamaSiswa = $peminjaman->nama_siswa;
                $sheet->setCellValue("A{$row}", $currentNamaSiswa);
                $sheet->mergeCells("A{$row}:I{$row}");
                $row++;
            }

            $sheet->setCellValue("A{$row}", $peminjaman->nama_siswa);
            $sheet->setCellValue("B{$row}", $peminjaman->nama_barang);
            $sheet->setCellValue("C{$row}", $peminjaman->status);
            $sheet->setCellValue("D{$row}", $peminjaman->quantity);
            $sheet->setCellValue("E{$row}", $peminjaman->kelas);
            $sheet->setCellValue("F{$row}", $peminjaman->image);
            $sheet->setCellValue("G{$row}", $peminjaman->guru_mapel);
            $sheet->setCellValue("H{$row}", $peminjaman->ruangan);
            $sheet->setCellValue("I{$row}", $peminjaman->description);

            $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true); // Set bold untuk data
            $row++;
        }
    }
}
