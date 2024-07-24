<?php

namespace App\Models\Admin;

use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\Penyusutan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Peralatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function pengajuan_detail()
    {
        return $this->belongsTo(PengajuanDetail::class, 'pengajuan_detail_id');
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public static function penyusutan()
    {
        try {
            $penyusutan = Peralatan::all();
                foreach ($penyusutan as $key => $value) {
                    $tanggal_pembelian = date('d', strtotime($value->tanggal_pembelian));
                    $tanggal_sekarang = date('d', time());
                    if ($tanggal_sekarang == 20) {
                        Penyusutan::create([
                            'peralatan_id' => $value->id,
                            'user_id' => $value->user_id,
                            'kode_branch' => $value->kode_branch,
                            'kode_peralatan' => $value->kode_peralatan,
                            'nama_peralatan' => $value->jenis,
                            'tanggal_penyusutan' => Carbon::now(),
                            'penyusutan_per_bulan' => $value->harga_perolehan / $value->masa_penyusutan,
                        ]);

                        // $branch = Branch::all();
                        $ap = Branch::where('code_area', $value->ap->code_area)->get();
                        $total = $ap->count();

                        $kp = Branch::where('code_area', $value->ap->code_area)->get();

                        // jurnal Amortisasi Peralatan AP (pembebanan)
                        foreach ($ap as $key => $ap) {
                            Transaksi::create([
                                'kode_branch' => $value->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(1234567890,10),
                                'kode_rekening' => '58312',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                                'debet' => $value->harga_perolehan / $total,
                                'kredit' => 0
                            ]);
                            
                            Transaksi::create([
                                'kode_branch' => $value->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK PASIVA - KP',
                                'debet' => 0,
                                'kredit' => $value->harga_perolehan / $total
                            ]);
                        }
                        
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                            'debet' => $value->harga_perolehan,
                            'kredit' => 0
                        ]);
                        
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '57202',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK PASIVA - KP',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan
                        ]);
                        // end jurnal Amortisasi Peralatan AP (pembebanan)

                        // jurnal Amortisasi Peralatan KP (pembenanan)
                        foreach ($kp as $key => $data) {
                            Transaksi::create([
                                'kode_branch' => $value->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                                'kode_rekening' => '1010'.'-'.$data->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK Aktiva - Unit'.' '.$data->unit,
                                'debet' => $value->harga_perolehan / $total,
                                'kredit' => 0
                            ]);
                            
                        }

                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '1010'.'-'.$data->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK Aktiva - Unit'.' '.$data->unit,
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan
                        ]);
                        // end jurnal Amortisasi Peralatan KP (pembenanan)

                        // jurnal Amortisasi di KP
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '1010-52915',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->masa_penyusutan,
                            'kredit' => 0
                        ]);
            
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.rand(10),
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
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '52915',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->masa_penyusutan,
                            'kredit' => 0
                        ]);
            
                        Transaksi::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU'.'-'.$value->kode_branch.'/'.rand(10),
                            'kode_rekening' => '1672000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->masa_penyusutan,
                        ]);                        
                    }
                }
                Log::info('schadule dijalankan');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    
}
