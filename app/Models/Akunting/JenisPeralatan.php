<?php

namespace App\Models\Akunting;

use App\Models\Admin\PengajuanDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPeralatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function param_peralatan()
    {
        return $this->belongsTo(ParamPeralatan::class, 'param_peralatan_id');
    }

    public function pengajuan_details()
    {
        return $this->belongsTo(PengajuanDetail::class);
    }
}