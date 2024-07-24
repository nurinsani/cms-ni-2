<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\Akunting\JenisPeralatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanBdd extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function jenis_bdd()
    {
        return $this->belongsTo(JenisPeralatan::class, 'jenis_bdd_id');
    }

    public function jenis_peralatans()
    {
        return $this->hasMany(PengajuanDetail::class, 'pengajuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'kode_branch');
    }

}
