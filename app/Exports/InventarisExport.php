<?php
namespace App\Exports;

use App\Models\Inventaris;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarisExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected $inventarisCollection;

    public function __construct()
    {
        $this->inventarisCollection = Inventaris::all();
    }

    public function collection()
    {
        return $this->inventarisCollection;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Jumlah',
            'Kode Barang',
            'Status Kerusakan',
            'Deskripsi',
        ];
    }

    public function title(): string
    {
        return 'Inventaris';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(20);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function map($inventaris): array
    {
        return [
            $inventaris->name,
            $inventaris->quantity,
            $inventaris->kd_barang,
            $inventaris->kerusakan,
            $inventaris->description,
        ];
    }

    public function groupData(Worksheet $sheet)
    {
        $row = 2;
        $currentName = '';

        foreach ($this->inventarisCollection as $inventaris) {
            if ($currentName !== $inventaris->name) {
                $currentName = $inventaris->name;
                $sheet->setCellValue("A{$row}", $currentName);
                $sheet->mergeCells("A{$row}:E{$row}");
                $row++;
            }

            $sheet->setCellValue("A{$row}", $inventaris->name);
            $sheet->setCellValue("B{$row}", $inventaris->quantity);
            $sheet->setCellValue("C{$row}", $inventaris->kd_barang);
            $sheet->setCellValue("D{$row}", $inventaris->kerusakan);
            $sheet->setCellValue("E{$row}", $inventaris->description);

            $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true); // Set bold untuk data
            $row++;
        }
    }
}
