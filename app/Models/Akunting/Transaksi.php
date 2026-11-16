<?php

namespace App\Models\Akunting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi';
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'mutasi_id');
    }
}
