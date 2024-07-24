<?php

namespace App\Models\Admin;

use App\Models\Akunting\JenisPeralatan;
use App\Models\Akunting\ParamPeralatan;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengajuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['tanggal_pengajuan'];

    public function pengajuan_details()
    {
        return $this->hasMany(PengajuanDetail::class, 'pengajuan_id');
    }

    // public function ap()
    // {
    //     return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    // }

    public function area()
    {
        return $this->belongsTo(Branch::class, 'code_area', 'code_area');
    }

    public function lpj()
    {
        return $this->hasMany(Lpj::class, 'pengajuan_detail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approval1()
    {
        return $this->belongsTo(User::class, 'approval_1');
    }

    public function approval2()
    {
        return $this->belongsTo(User::class, 'approval_2');
    }

    public function approval3()
    {
        return $this->belongsTo(User::class, 'approval_3');
    }

    public function approval4()
    {
        return $this->belongsTo(User::class, 'approval_4');
    }

    public function approval5()
    {
        return $this->belongsTo(User::class, 'approval_5');
    }

    public function approval6()
    {
        return $this->belongsTo(User::class, 'approval_6');
    }

    public function subtotal()
    {
        $subtotal = 0;
        foreach ($this->pengajuan_details as $item) {
            $total = $item->harga * $item->kuantitas;
            $subtotal = $subtotal + $total;
        }

        return $subtotal;
    }

    public function total_pembelian()
    {
        $total_pembelian = 0;
        foreach ($this->pengajuan_details as $item) {
            $total = $item->harga_pembelian;
            $total_pembelian = $total_pembelian + $total;
        }

        return $total_pembelian;
    }

    public function total_pembelian_ap()
    {
        foreach ($this->pengajuan_details as $item) {}

        $branch = Branch::where('code_area', $item->kode_area)->get();
        foreach ($branch as $b) {
            $a = $item->harga_pembelian / $branch->count();
        }
               
        return $a;
    }

}
