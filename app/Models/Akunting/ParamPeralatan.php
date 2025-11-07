<?php

namespace App\Models\Akunting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamPeralatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function jenis_peralatan()
    {
        return $this->belongsTo(JenisPeralatan::class, 'jenis_peralatan_id');
    }

    public function param_peralatan()
    {
        return $this->hasMany(JenisPeralatan::class, 'param_peralatan_id');
    }
}