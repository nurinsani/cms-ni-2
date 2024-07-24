<?php

namespace App\Exports;

use App\Models\Admin\Peralatan as AdminPeralatan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeralatanExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return AdminPeralatan::select('kode_peralatan', 'kode_branch', 'coa', 'nama_peralatan', 'tanggal_pembelian')->get();
        $data = DB::table('peralatans')
                    // ->join('pengajuan_details', 'pengajuan_details.id', '=', 'peralatans.pengajuan_detail_id')
                    ->select('kode_branch','kode_peralatan', 'jenis', 'merk', 'type', 'tanggal_pembelian', 'jumlah', 'harga_perolehan')
                    ->get();
        return $data;
    }

    public function headings(): array
    {
        return ['KODE_BRANCH','BARCODE', 'JENIS', 'MERK', 'TYPE', 'TANGGAL PEROLEHAN', 'JUMLAH', 'HARGA PEROLEHAN'];
    }
}
