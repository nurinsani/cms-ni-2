<?php

namespace App\Models;

use App\Models\Inventaris;
use App\Models\PengajuanHapusBuku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHapusBukuDetail extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hapus_buku_detail';
    protected $guarded = ['id'];

    public function pengajuan_hapus_buku()
    {
        return $this->belongsTo(PengajuanHapusBuku::class, 'pengajuan_hapus_buku_id');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }
}
