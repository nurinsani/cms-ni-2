<?php

namespace App\Models\Admin;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpj extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    // public function pengajuan_details()
    // {
    //     return $this->hasMany(PengajuanDetail::class, 'pengajuan_detail_id');
    // }

    public function pengajuan_details()
    {
        return $this->hasMany(PengajuanDetail::class, 'pengajuan_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'kode_branch');
    }
}
