<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKp extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kp';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];
}
