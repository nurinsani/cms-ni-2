<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $guarded = [];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function penyusutans()
    {
        return $this->hasMany(Penyusutan::class, 'inventaris_id');
    }

    public function penyusutan_bulan()
    {
        // Ambil inventaris terkait
        $inventaris = $this->inventaris;
        
        if (!$inventaris) {
            return 0;
        }

        // Hitung penyusutan per bulan
        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
        
        // Return akumulasi
        return $penyusutanPerBulan;
    }

    public function akumulasi_penyusutan()
    {
        // Ambil inventaris terkait
        $inventaris = $this->inventaris;
        
        if (!$inventaris) {
            return 0;
        }

        // Hitung penyusutan per bulan
        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
        
        // Hitung jumlah bulan penyusutan (count dari table penyusutan)
        $jumlahBulan = $inventaris->penyusutans()->count();
        
        // Return akumulasi
        return $penyusutanPerBulan * $jumlahBulan;
    }

    public function penyusutan_ke()
    {
        // Ambil inventaris terkait
        $inventaris = $this->inventaris;
        
        if (!$inventaris) {
            return 0;
        }
        
        // Hitung jumlah bulan penyusutan (count dari table penyusutan)
        $jumlahBulan = $inventaris->penyusutans()->count();
        
        // Return akumulasi
        return $jumlahBulan;
    }

    public function akumulasi_penyusutan_tahun($tahun)
    {
        $inventaris = $this->inventaris;
        
        if (!$inventaris) {
            return 0;
        }

        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
        
        // Count penyusutan di tahun tertentu
        $jumlahBulan = $inventaris->penyusutans()
            ->whereYear('tanggal_penyusutan', $tahun)
            ->count();
        
        return $penyusutanPerBulan * $jumlahBulan;
    }

    public function akumulasi_penyusutan_berjalan()
    {
        return $this->akumulasi_penyusutan_tahun(now()->year);
    }

    public function nilai_buku()
    {
        $inventaris = $this->inventaris;
        
        if (!$inventaris) {
            return 0;
        }

        $hargaPerolehan = $inventaris->harga_perolehan;
        $totalAkumulasi = $this->akumulasi_penyusutan();
        
        return $hargaPerolehan - $totalAkumulasi;
    }

}
