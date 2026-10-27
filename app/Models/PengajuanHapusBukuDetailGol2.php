<?php

namespace App\Models;

use App\Models\InventarisGol2;
use App\Models\PengajuanHapusBukuGol2;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHapusBukuDetailGol2 extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hapus_buku_detail_gol_2';
    protected $guarded = ['id'];

    public function pengajuan_hapus_buku()
    {
        return $this->belongsTo(PengajuanHapusBukuGol2::class, 'pengajuan_hapus_buku_id');
    }

    public function inventaris_gol_2()
    {
        return $this->belongsTo(InventarisGol2::class, 'inventaris_gol_2_id');
    }
}
