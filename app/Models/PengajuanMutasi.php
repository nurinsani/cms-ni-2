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

    public function pengajuan_detail()
    {
        return $this->belongsTo(PengajuanDetail::class);
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'ke_cabang');
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_id');
    }
}
