<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPenyusutanAp extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'transaksi_penyusutan_ap';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];
}
