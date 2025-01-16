<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarisGol2 extends Model
{
    use HasFactory;
    protected $table = 'inventaris_gol_2';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function penyusutans()
    {
        return $this->hasMany(PenyusutanGol2::class, 'inventaris_gol_2_id');
    }

    public function susut()
    {
        return $this->hasMany(PenyusutanGol2::class, 'inventaris_gol_2_id');
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public function akumulasi_penyusutan_awal()
    {
        // Log::info($this->susut()->whereYear('tanggal_penyusutan', 2023)->get());
        return ($this->harga_perolehan / 24) * $this->susut()->whereYear('tanggal_penyusutan', Carbon::now()->subYears(1)->format('Y'))->count();
    }

    public function akumulasi_penyusutan()
    {
        return (intval($this->harga_perolehan) / 48) * $this->susut()->whereYear('tanggal_penyusutan', date('Y'))->count();
    }


    public function total_akumulasi_penyusutan()
    {
        $total = 0;
        $inventaris = InventarisGol2::all();

        foreach ($inventaris as $i) {
            $total += $i->akumulasi_penyusutan();
        }

        return $total;
    }

    public function nilai_buku()
    {
        $total = 0;
        $inventaris = InventarisGol2::all();

        foreach ($inventaris as $i) {
            $total += intval($i->harga_perolehan) - ($i->akumulasi_penyusutan());
        }

        return $total;
    }

    public static function penyusutan()
    {
        try {
            DB::beginTransaction();

            $i = 0;

            $inventaris_gol_2 = InventarisGol2::all();
            // $inventaris = Inventaris::where('kode_branch', 2040)->get();
            // $rencana_penyusutan = RencanaPenyusutan::where('status_penyusutan', 1)->get();

            $penyusutan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->first();

            if (!$penyusutan) {
                foreach ($inventaris_gol_2 as $value) {
                    $tanggal_sekarang = date('d', time());
                    $harga_perolehan = intval($value->harga_perolehan);
                    $penyusutan = intval($value->umur);
                    $kode_branch = $value->kode_branch;
                    $kredit = Branch::firstWhere('kode_branch', $kode_branch);
                
                    if (!$kredit) {
                        continue;
                    }
                
                    $debet = Branch::where('code_area', $kredit->code_area)->get();
                
                    if ($debet->isEmpty()) {
                        continue;
                    }
                
                    $hasil_penyusutan = $harga_perolehan > 0 && $penyusutan > 0 ?
                        $harga_perolehan / $penyusutan :
                        0;
                
                    $hasil_pembebanan = $hasil_penyusutan / $debet->count();
                
                    $branch = Branch::where('kode_branch', $kode_branch)->first();
                
                    if ($tanggal_sekarang == 5) {
                        if ($value->penyusutan_ke + count($value->susut) !== 48) {
                            // Tambahkan logika untuk penyusutan dua kali jika tanggal perolehan antara 16-31 dan penyusutan awal 0
                            if (count($value->susut) == 0 && intval(date('d', strtotime($value->tanggal_perolehan))) >= 16) {
                                if ($value->status_branch !== 'AUDIT') {
                                    // Lakukan penyusutan dua kali
                                    for ($j = 0; $j < 2; $j++) {
                                        Penyusutan::create([
                                            'inventaris_id' => $value->id,
                                            'user_id' => $value->user_id,
                                            'kode_branch' => $branch->kode_branch,
                                            'gl' => $branch->GL,
                                            'kode_peralatan' => $value->kode_peralatan,
                                            'nama_peralatan' => $value->jenis,
                                            'keterangan' => $value->keterangan,
                                            'tanggal_penyusutan' => date('Y-m-d'),
                                            'penyusutan_per_bulan' => $hasil_penyusutan,
                                            'status_branch' => $value->status_branch
                                        ]);
                                    }
                                }
                            } else {
                                // Penyusutan normal
                                if ($value->status_branch !== 'AUDIT') {
                                    Penyusutan::create([
                                        'inventaris_id' => $value->id,
                                        'user_id' => $value->user_id,
                                        'kode_branch' => $branch->kode_branch,
                                        'gl' => $branch->GL,
                                        'kode_peralatan' => $value->kode_peralatan,
                                        'nama_peralatan' => $value->jenis,
                                        'keterangan' => $value->keterangan,
                                        'tanggal_penyusutan' => date('Y-m-d'),
                                        'penyusutan_per_bulan' => $hasil_penyusutan,
                                        'status_branch' => $value->status_branch
                                    ]);
                                }
                            }
                        }
                    }
                
                    $i++;
                }
                
                // start jurnal penyusutan
                $branches = Branch::all();

                foreach ($branches as $branch) {
                    $penyusutan = Penyusutan::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_penyusutan', date('m'))
                        ->whereYear('tanggal_penyusutan', date('Y'))
                        ->whereIn('status_branch', ['AP', 'ALL'])
                        ->get();

                    if (count($penyusutan) > 0) {
                        $penyusutan_per_bulan = Penyusutan::where('kode_branch', $branch->kode_branch)
                            ->whereMonth('tanggal_penyusutan', date('m'))
                            ->whereYear('tanggal_penyusutan', date('Y'))
                            ->sum('penyusutan_per_bulan');

                        if ($branch->kode_branch == '1010') {
                            $transaksiKpDebet = TransaksiPenyusutanKp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('debet', '>', 0)
                                ->first();

                            $transaksiKpKredit = TransaksiPenyusutanKp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('kredit', '>', 0)
                                ->first();

                            $kode_transaksi = date('m') . random_int(100000, 999999);
                            if ($transaksiKpDebet && $transaksiKpKredit) {
                                TransaksiPenyusutanKp::where('id', $transaksiKpDebet->id)->update([
                                    'debet' => $penyusutan_per_bulan,
                                ]);

                                TransaksiPenyusutanKp::where('id', $transaksiKpKredit->id)->update([
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            } else {
                                TransaksiPenyusutanKp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1010-52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => $penyusutan_per_bulan,
                                    'kredit' => 0,
                                ]);

                                TransaksiPenyusutanKp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1010-1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            }
                        } else {
                            $transaksiApKredit = TransaksiPenyusutanAp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('kredit', '>', 0)
                                ->first();

                            $transaksiApDebet = TransaksiPenyusutanAp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('debet', '>', 0)
                                ->first();

                            $kode_transaksi = date('m') . random_int(100000, 999999);
                            if ($transaksiApKredit && $transaksiApDebet) {
                                TransaksiPenyusutanAp::where('id', $transaksiApDebet->id)->update([
                                    'debet' => $penyusutan_per_bulan,
                                ]);
                                TransaksiPenyusutanAp::where('id', $transaksiApKredit->id)->update([
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            } else {
                                TransaksiPenyusutanAp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => $penyusutan_per_bulan,
                                    'kredit' => 0,
                                ]);

                                TransaksiPenyusutanAp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            }
                        }
                    } else {
                        continue;
                    }
                }
                // end jurnal penyusutan 
                

                $pembebanan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                    ->whereYear('tanggal_penyusutan', date('Y'))
                    ->whereNotIn('status_branch', ['AP', 'ALL'])
                    ->get();

                if (count($pembebanan) > 0) {
                    foreach ($pembebanan as $row) {
                        $status_branch = $row->status_branch;

                        $isArea     = Branch::where('code_area', $status_branch)
                            ->where('status_aktif', 'aktif')
                            ->count();

                        $isRegion   = Branch::where('code_region', $status_branch)
                            ->where('status_aktif', 'aktif')
                            ->count();

                        $credit = round($row->penyusutan_per_bulan);

                        $credit = round($credit);

                        $ids = [];
                        $gl = [];

                        if ($isRegion > 0) {

                            if ($status_branch == '9999') {
                                $otherBranches = Branch::whereIn('code_region', ['3333', '2222'])
                                    // ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

                                // $debet = round(($credit / (count($otherBranches))));
                                $debet = round(($credit / (count($otherBranches) + 1)));
                            } else {
                                $debet = round(($credit / ($isRegion)));

                                $otherBranches = Branch::where('code_region', $status_branch)
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();
                            }

                            $credit = $credit - $debet;

                            foreach ($otherBranches as $other) {
                                $ids[] = $other->kode_branch;
                                $gl[] = $other->GL;
                            }

                            $kode_transaksi_kp = date('m') . random_int(100000, 999999);

                            for ($j = 0; $j < count($ids); $j++) {
                                TransaksiPembebananKp::create([
                                    'kode_branch' => 1010,
                                    'kode_transaksi' => 'BU' . '/' . 1010 . '/' . $kode_transaksi_kp,
                                    'kode_rekening' => '1010-' . $gl[$j],
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => $debet,
                                    'kredit' => 0,
                                ]);

                                $kode_transaksi_ap = date('m') . random_int(100000, 999999);
                                TransaksiPembebananAp::create([
                                    'kode_branch' => $ids[$j],
                                    'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/' . $kode_transaksi_ap,
                                    'kode_rekening' => '58312',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => $debet,
                                    'kredit' => 0,
                                ]);

                                TransaksiPembebananAp::create([
                                    'kode_branch' => $ids[$j],
                                    'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/' . $kode_transaksi_ap,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $debet,
                                ]);
                            }

                            TransaksiPembebananAp::create([
                                'kode_branch' => $row->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/' . $kode_transaksi_ap,
                                'kode_rekening' => '57202',
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => 0,
                                'kredit' => $credit,
                            ]);

                            TransaksiPembebananAp::create([
                                'kode_branch' => $row->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/' . $kode_transaksi_ap,
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => $credit,
                                'kredit' => 0,
                            ]);

                            TransaksiPembebananKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/' . 1010 . '/' . $kode_transaksi_kp,
                                'kode_rekening' => '1010-' . $row->gl,
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => 0,
                                'kredit' => $credit,
                            ]);
                        } else {
                            if ($isArea != 0) {
                                $debet = round(($credit / $isArea));
                                $credit = $credit;

                            } else {
                                $debet = 0;
                                $credit = $credit;
                            }

                            // $debet = round(($credit / ($isArea)), 2);

                            if ($status_branch == '9999') {
                                $otherBranches = Branch::whereIn('code_region', ['3333', '2222'])
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

                                // $debet = round(($credit / count($otherBranches)));
                                $debet = round(($credit / (count($otherBranches) + 1)));
                            } else {
                                if ($isArea != 0) {
                                    $debet = round(($credit / $isArea));
                                } else {
                                    $debet = 0;
                                }

                                $otherBranches = Branch::where('code_area', $status_branch)
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();
                            }

                            $credit = $credit - $debet;

                            foreach ($otherBranches as $other) {
                                $ids[] = $other->kode_branch;
                                $gl[] = $other->GL;
                            }

                            $kode_transaksi_kp = date('m') . random_int(100000, 999999);

                            for ($j = 0; $j < count($ids); $j++) {
                                TransaksiPembebananKp::create([
                                    'kode_branch' => 1010,
                                    'kode_transaksi' => 'BU' . '/' . 1010 . '/' . $kode_transaksi_kp,
                                    'kode_rekening' => '1010-' .  $gl[$j],
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => $debet,
                                    'kredit' => 0,
                                ]);

                                $kode_transaksi_ap = date('m') . random_int(100000, 999999);
                                TransaksiPembebananAp::create([
                                    'kode_branch' => $ids[$j],
                                    'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/' . $kode_transaksi_ap,
                                    'kode_rekening' => '58312',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => $debet,
                                    'kredit' => 0,
                                ]);

                                TransaksiPembebananAp::create([
                                    'kode_branch' => $ids[$j],
                                    'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/' . $kode_transaksi_ap,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $debet,
                                ]);
                            }

                            TransaksiPembebananAp::create([
                                'kode_branch' => $row->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/' . $kode_transaksi_ap,
                                'kode_rekening' => '57202',
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => 0,
                                'kredit' => $credit
                            ]);

                            TransaksiPembebananAp::create([
                                'kode_branch' => $row->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/' . $kode_transaksi_ap,
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => $credit,
                                'kredit' => 0,
                            ]);

                            TransaksiPembebananKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/' . 1010 . '/' . $kode_transaksi_kp,
                                'kode_rekening' => '1010-' . $row->gl,
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'Jurnal Pembebanan Inventaris' . ' ' . $row->keterangan . ' ' . date('M Y'),
                                'debet' => 0,
                                'kredit' => $credit,
                            ]);
                        }
                    }
                }
                
                DB::commit();
                Log::info('schadule inventaris dijalankan ' . $i . ' data ditambahkan');
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            dd($th);
            Log::error($th->getMessage());
        }
    }
}
