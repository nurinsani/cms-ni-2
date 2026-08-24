<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarisGol2TemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'status_branch',
            'kode_branch',
            'unit',
            'kode_peralatan',
            'jenis',
            'merk',
            'type',
            'imei',
            'keterangan',
            'tanggal_perolehan',
            'jumlah',
            'harga_perolehan',
            'umur',
            'nip',
            'pengguna',
            'jabatan',
            'penyusutan_ke',
        ];
    }

    public function array(): array
    {
        return [
            [
                'status_branch'     => 'ALL',
                'kode_branch'       => '1010',
                'unit'              => 'KAP',
                'kode_peralatan'    => 'PRLT-001',
                'jenis'             => 'Laptop Lenovo',
                'merk'              => 'Lenovo',
                'type'              => 'ThinkPad T14',
                'imei'              => '864201049281726',
                'keterangan'        => 'Perangkat Kerja IT',
                'tanggal_perolehan' => '2025-01-15',
                'jumlah'            => 1,
                'harga_perolehan'   => '12000000',
                'umur'              => '48',
                'nip'               => 'NIP-10023',
                'pengguna'          => 'Budi Santoso',
                'jabatan'           => 'Staff IT',
                'penyusutan_ke'     => '0',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F497D']
                ]
            ],
        ];
    }
}
