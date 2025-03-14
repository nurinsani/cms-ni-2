<?php

namespace App\Models\Admin;

use App\Models\Akunting\JenisPeralatan;
use App\Models\Branch;
use App\Models\Rekening;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PengajuanDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function jenis_peralatan()
    {
        return $this->belongsTo(JenisPeralatan::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public function area()
    {
        return $this->belongsTo(Branch::class, 'kode_area', 'code_area');
    }

    public function region()
    {
        return $this->belongsTo(Branch::class, 'kode_user', 'code_region');
    }

    public function subtotal()
    {
        $subtotal = 0;
        foreach ($this->pengajuan_details as $item) {
            $total = $item->harga * $item->kuantitas;
            $subtotal = $subtotal + $total;
        }

        return $subtotal;
    }

    public function avarage($unit = null)
    {
        $branch = Branch::where('code_area', $this->kode_area)->get();
        $hasil = $this->harga_pembelian / $branch->count();

        $data = [];
        foreach ($branch as $b) {
            $data[] = (object)[
                'unit' => $b->unit,
                'harga' => $hasil
            ];
        }

        Log::info($unit);
        if ($unit) {
            $harga = 0;
            foreach ($branch as $b) {
                if ($unit = $b->unit) {
                    $harga += $hasil;
                }
            }
            return $harga;
        }
        return collect($data);
    }
}
