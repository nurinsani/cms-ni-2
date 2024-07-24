<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembebananKp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi_pembebanan_kp';
    protected $dates = ['tanggal_transaksi'];
}
