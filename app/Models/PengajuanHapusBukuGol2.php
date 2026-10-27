<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use App\Models\InventarisGol2;
use App\Models\PengajuanHapusBukuDetailGol2;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHapusBukuGol2 extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hapus_buku_gol_2';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public function details()
    {
        return $this->hasMany(PengajuanHapusBukuDetailGol2::class, 'pengajuan_hapus_buku_id');
    }

    public function inventaris_gol_2()
    {
        return $this->belongsToMany(InventarisGol2::class, 'pengajuan_hapus_buku_detail_gol_2', 'pengajuan_hapus_buku_id', 'inventaris_gol_2_id')
                    ->withPivot('id', 'foto', 'kondisi');
    }

    public function admin_head()
    {
        return $this->belongsTo(User::class, 'atasan_1');
    }

    public function akunting()
    {
        return $this->belongsTo(User::class, 'atasan_2');
    }
}
