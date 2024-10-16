<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalAp extends Model
{
    use HasFactory;

    protected $table = 'transaksi_ap';
    protected $casts = [
        'tanggal_transaksi' => 'date',  // atau 'datetime'
    ];

}
