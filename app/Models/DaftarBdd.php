<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Admin\PengajuanBdd;
use App\Models\Akunting\TransaksiBdd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class DaftarBdd extends Model
{
    use HasFactory;

    protected $table = 'daftar_bdd';
    protected $guarded = [];

    public function pengajuan_bdd()
    {
        return $this->belongsTo(PengajuanBdd::class, 'pengajuan_bdd_id');
    }

    public static function jurnal_bdd()
    {
        try {
            $daftar_bdd = DaftarBdd::where('status_amortisasi', 1)->get();
                foreach ($daftar_bdd as $key => $value) {
                    $tanggal_awal = date('d', strtotime($value->tanggal_awal));
                    $tanggal_sekarang = date('d', time());
                    if ($tanggal_awal == $tanggal_sekarang) {
                        TransaksiBdd::create([
                            'unit' => $value->kode_ap,
                            'kode_transaksi' => Str::uuid(),
                            'kode_rekening' => '1010-1936000',
                            'tanggal_transaksi' => Carbon::now(),
                            'debet' => $value->pengajuan_bdd->harga_sewa_net / $value->jangka_waktu,
                            'kredit' => 0
                        ]);
        
                        TransaksiBdd::create([
                            'unit' => $value->kode_ap,
                            'kode_transaksi' => Str::uuid(),
                            'kode_rekening' => '1010-1311000',
                            'tanggal_transaksi' => Carbon::now(),
                            'debet' => 0,
                            'kredit' => $value->pengajuan_bdd->harga_sewa_net / $value->jangka_waktu,
                        ]);
                    }
                }
                Log::info('schadule dijalankan');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
