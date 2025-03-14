<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyusutan extends Model
{
    use HasFactory;

    protected $table = 'penyusutan';
    protected $guarded = [];

    protected $casts = [
        'tanggal_penyusutan' => 'date',
    ];
    
    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }


    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function inventaris_gol_2()
    {
        return $this->belongsTo(InventarisGol2::class, 'inventaris_gol_2_id');
    }
}
