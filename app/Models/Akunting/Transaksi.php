<?php

namespace App\Models\Akunting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi';
    protected $dates = ['tanggal_transaksi'];
}
