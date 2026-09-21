<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use App\Models\Inventaris;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanHapusBuku extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hapus_buku';
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
        return $this->hasMany(PengajuanHapusBukuDetail::class, 'pengajuan_hapus_buku_id');
    }

    public function inventaris()
    {
        return $this->belongsToMany(Inventaris::class, 'pengajuan_hapus_buku_detail', 'pengajuan_hapus_buku_id', 'inventaris_id')
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
