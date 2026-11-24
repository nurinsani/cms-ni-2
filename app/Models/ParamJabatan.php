<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamJabatan extends Model
{
    use HasFactory;

    protected $table = 'param_jabatans';

    protected $guarded = ['id'];

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 'aktif');
    }
}

