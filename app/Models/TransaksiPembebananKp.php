<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembebananKp extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'transaksi_pembebanan_kp';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];
}
