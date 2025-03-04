<?php

namespace App\Models;

use App\Models\Admin\PengajuanDetail;
use App\Models\Admin\Peralatan;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanMutasi extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function pengajuan_detail()
    {
        return $this->belongsTo(PengajuanDetail::class);
    }

    public function dari_ap()
    {
        return $this->belongsTo(Branch::class, 'dari_cabang', 'kode_branch');
    }
    

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'ke_cabang', 'kode_branch');
    }

    public function peralatan()
    {
        return $this->belongsTo(Inventaris::class, 'peralatan_id', 'id');
    }

    // Relasi Many-to-Many ke tabel Peralatan melalui tabel pengajuan_mutasi_detail
    public function peralatans()
    {
        return $this->belongsToMany(Peralatan::class, 'pengajuan_mutasi_detail', 'pengajuan_mutasi_id', 'inventaris_id');
    }

    // Relasi Many-to-Many ke tabel Inventaris melalui tabel pengajuan_mutasi_detail
    public function inventaris()
    {
        return $this->belongsToMany(Inventaris::class, 'pengajuan_mutasi_detail', 'pengajuan_mutasi_id', 'inventaris_id');
    }
}
