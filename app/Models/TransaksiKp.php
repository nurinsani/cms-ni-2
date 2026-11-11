<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi_kp';
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];
}
