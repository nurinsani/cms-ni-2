<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembebananAp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi_pembebanan_ap';
    protected $dates = ['tanggal_transaksi'];
}
