<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branch';
    protected $primaryKey = 'code_area';
    public $incrementing = false;

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
