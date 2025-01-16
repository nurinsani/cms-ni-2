<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembebananApGol2 extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'transaksi_pembebanan_ap_gol_2';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];
}
