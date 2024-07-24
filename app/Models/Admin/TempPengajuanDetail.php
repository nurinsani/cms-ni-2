<?php

namespace App\Models\Admin;

use App\Models\Akunting\JenisPeralatan;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPengajuanDetail extends Model
{
    use HasFactory;

    protected $table = 'temp_pengajuan_detail';
    protected $guarded = [];

    public function jenis_peralatan()
    {
        return $this->belongsTo(JenisPeralatan::class);
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public function area()
    {
        return $this->belongsTo(Branch::class, 'kode_area', 'code_area');
    }
}
