<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Inventaris extends Model
{
    use HasFactory;
    protected $table = 'inventaris';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function susut()
    {
        return $this->hasMany(Penyusutan::class);
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public static function penyusutan()
    {
        try {
            $inventaris = Inventaris::all();
                foreach ($inventaris as $key => $value) {
                    $tanggal_pembelian = date('d', strtotime($value->tanggal_pembelian));
                    $tanggal_sekarang = date('d', time());
                    if ($tanggal_sekarang == 26) {
                        Penyusutan::create([
                            'inventaris_id' => $value->id,
                            'user_id' => $value->user_id,
                            'kode_branch' => $value->kode_branch,
                            'kode_peralatan' => $value->kode_peralatan,
                            'nama_peralatan' => $value->jenis,
                            'tanggal_penyusutan' => Carbon::now(),
                            'penyusutan_per_bulan' => $value->harga_perolehan / $value->masa_penyusutan,
                        ]);

                        // $branch = Branch::all();
                        $ap = Branch::where('code_area', $value->ap->code_area)->get();
                        $total_ap = $ap->count();

                        $kp = Branch::where('code_area', $value->ap->code_area)->get();
                        
                        // jurnal Amortisasi Peralatan KP (pembenanan)
                        foreach ($kp as $key => $data) {
                            $penyusutan = Penyusutan::all();
                            TransaksiKp::create([
                                'kode_branch' => $data->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.Str::random(10),
                                'kode_rekening' => '1010'.'-'.$data->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK Aktiva - Unit'.' '.$data->unit,
                                'debet' => 11875 / $total_ap,
                                'kredit' => 0
                            ]);
                        }

                        TransaksiKp::create([
                            'kode_branch' => $data->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '1010'.'-'.$data->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK Aktiva - Unit'.' '.$data->unit,
                            'debet' => 0,
                            'kredit' => 11875
                        ]);
                        // end jurnal Amortisasi Peralatan KP (pembenanan)

                        // jurnal Amortisasi Peralatan AP (pembebanan)
                        foreach ($ap as $key => $ap) {
                            TransaksiAp::create([
                                'kode_branch' => $ap->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$ap->kode_branch.'/'.Str::random(10),
                                'kode_rekening' => '58312',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                                'debet' => $value->harga_perolehan / $total_ap,
                                'kredit' => 0
                            ]);
                            
                            TransaksiAp::create([
                                'kode_branch' => $ap->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$ap->kode_branch.'/'.Str::random(10),
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK PASIVA - KP',
                                'debet' => 0,
                                'kredit' => $value->harga_perolehan / $total_ap
                            ]);
                        }
                        
                        TransaksiAp::create([
                            'kode_branch' => $ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$ap->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK PASIVA - KP',
                            'debet' => $value->harga_perolehan,
                            'kredit' => 0
                        ]);
                        
                        TransaksiAp::create([
                            'kode_branch' => $ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$ap->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '57202',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan
                        ]);
                        // end jurnal Amortisasi Peralatan AP (pembebanan)

                        // jurnal Amortisasi di KP
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '1010-52915',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->masa_penyusutan,
                            'kredit' => 0
                        ]);
            
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '1010-1672000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->masa_penyusutan,
                        ]);

                        // jurnal Amortisasi di AP
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '52915',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->masa_penyusutan,
                            'kredit' => 0
                        ]);
            
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.Str::random(10),
                            'kode_rekening' => '1672000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->masa_penyusutan,
                        ]);                        
                    }
                }
                Log::info('schadule inventaris dijalankan');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
