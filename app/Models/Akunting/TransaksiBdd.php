<?php

namespace App\Models\Akunting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBdd extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'transaksi_bdd';
}
