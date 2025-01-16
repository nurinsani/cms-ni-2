<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPenyusutanKpGol2 extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'transaksi_penyusutan_kp_gol_2';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];
}
