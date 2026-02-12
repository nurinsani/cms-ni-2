<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\Penyusutan;
use App\Models\TransaksiPembebananAp;
use App\Models\TransaksiPembebananKp;
use App\Models\TransaksiPenyusutanAp;
use App\Models\TransaksiPenyusutanKp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// tes lagi

class PenyusutanController extends Controller
{

    public function index()
    {
        $title = 'Proses Penyusutan';
        $transaksiKp = TransaksiPembebananKp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))->first();

        $transaksiAp = TransaksiPembebananAp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))->first();

        $penyusutanKp = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))->first();

        $penyusutanAp = TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))->first();

        return view('transaksi.penyusutan.index', compact(
            'title',
            'transaksiKp',
            'transaksiAp',
            'penyusutanKp',
            'penyusutanAp'
        ));
    }

    public function jalankanPenyusutan(Request $request)
    {
        // Cek apakah penyusutan sudah dilakukan di bulan ini
        $penyusutanSudahAda = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
        ->whereYear('tanggal_penyusutan', date('Y'))
        ->exists(); // Menggunakan exists() untuk pengecekan cepat

        if ($penyusutanSudahAda) {

        return redirect()->back()->with('error', 'Proses penyusutan untuk bulan ini sudah dilakukan.');

        } else {
            try {
                DB::beginTransaction();
    
                $i = 0;
    
                $inventaris = Inventaris::all();
    
                $penyusutan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                    ->whereYear('tanggal_penyusutan', date('Y'))
                    ->first();
    
                if (!$penyusutan) {
                    foreach ($inventaris as $value) {
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
                    
                        $hasil_penyusutan = $harga_perolehan > 0 && $penyusutan > 0 ? $harga_perolehan / $penyusutan : 0;
                    
                        $branch = Branch::where('kode_branch', $kode_branch)->first();
                    
                        if ($tanggal_sekarang == 12) {
                        // if ($tanggal_sekarang == date('d')) {
                            if ($value->penyusutan_ke + count($value->susut) !== 48) {
                                // Tambahkan logika untuk penyusutan dua kali jika tanggal perolehan antara 16-31 dan penyusutan awal 0
                                if (count($value->susut) == 0 && intval(date('d', strtotime($value->tanggal_perolehan))) >= 15) {
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
                    
                    DB::commit();
                    Log::info('schadule inventaris dijalankan ' . $i . ' data ditambahkan');
                }
            } catch (\Throwable $th) {
                DB::rollBack();
    
                dd($th);
                Log::error($th->getMessage());
            }

            return redirect()->back()->with('success', 'Proses penyusutan berhasil dijalankan.');
        }
    }

    public function jalankanJurnalPenyusutan(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $i = 0;
    
            // Mengecek apakah sudah ada data penyusutan untuk bulan dan tahun ini (tidak digunakan di bawah)
            $penyusutan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->first();
    
                // ambil semua branch
                $branches = Branch::all();
    
                foreach ($branches as $branch) {
                    // Ambil data penyusutan untuk branch ini di bulan dan tahun ini, dan status_branch 'AP' atau 'ALL'
                    $penyusutan = Penyusutan::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_penyusutan', date('m'))
                        ->whereYear('tanggal_penyusutan', date('Y'))
                        // ->whereIn('status_branch', ['AP', 'ALL'])
                        ->get();
    
                    if (count($penyusutan) > 0) {
                        // Hitung total penyusutan bulan ini untuk cabang ini
                        $penyusutan_per_bulan = Penyusutan::where('kode_branch', $branch->kode_branch)
                            ->whereMonth('tanggal_penyusutan', date('m'))
                            ->whereYear('tanggal_penyusutan', date('Y'))
                            ->sum('penyusutan_per_bulan');
    
                        if ($branch->kode_branch == '1010') {
                             // Cek apakah sudah ada transaksi debet dan kredit sebelumnya
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
                                // jika transaksi udah ada, lakukan update nominal
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
                            // jurnal penyusutan untuk AP
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
                
                DB::commit();
                Log::info('schadule inventaris dijalankan ' . $i . ' data ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
    
            // dd($th);
            Log::error($th->getMessage());
        }
    
        return redirect()->back()->with('success', 'Proses penyusutan berhasil dijalankan.');
    }

    public function jalankanJurnalPembebanan(Request $request)
    {
        try {
            DB::beginTransaction();

            $i = 0;
    
            // mengambil data penyusutan untuk bulan dan tahun berjalan
            // kecuali yang memiliki status_branch 'AP' atau 'ALL'
            $pembebanan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                    ->whereYear('tanggal_penyusutan', date('Y'))
                    ->whereNotIn('status_branch', ['AP', 'ALL'])
                    ->whereNotIn('kode_branch', ['1010'])
                    ->get();

                // jika ada data penyusutan
                if (count($pembebanan) > 0) {
                    foreach ($pembebanan as $row) {
                        $status_branch = $row->status_branch;

                        $isArea     = Branch::where('code_area', $status_branch)
                            ->where('status_aktif', 'aktif')
                            ->count();

                        $isRegion   = Branch::where('code_region', $status_branch)
                            ->where('status_aktif', 'aktif')
                            ->where(function ($query) {
                                $query->whereNull('status_pembebanan') // Jika null, tetap eksekusi
                                      ->orWhere('status_pembebanan', '!=', 'no'); // Jika bukan 'no', tetap eksekusi
                            })
                            ->count();

                        $credit = (int) round($row->penyusutan_per_bulan); // nilai penyusutan yg dibulatkan

                        $credit = (int) round($credit);

                        $ids = [];
                        $gl = [];

                        // jika termasuk region (pembebanan berdasarkan region)
                        if ($isRegion > 0) {
                            // logic khusus untuk status_branch 9999 (region khusus)
                            if ($status_branch == '9999') {
                                $otherBranches = Branch::whereIn('code_region', ['3333', '2222'])
                                    // ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

                                // Hitung debet dengan membagi credit dengan jumlah branch + 1
                                // (termasuk branch asal)
                                $debet = round(($credit / (count($otherBranches) + 1)));
                            } else {
                                $debet = round(($credit / ($isRegion)));

                                $otherBranches = Branch::where('code_region', $status_branch)
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    // ->where(function ($query) {
                                    //     $query->whereNull('status_pembebanan') // Jika null, tetap eksekusi
                                    //           ->orWhere('status_pembebanan', '!=', 'no'); // Jika bukan 'no', tetap eksekusi
                                    // })
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
                            // Area normal
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
                            } elseif ($status_branch == '8888') {
                                $otherBranches = Branch::whereIn('code_area', ['2206', '2207', '2208'])
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

                                $debet = round(($credit / (count($otherBranches) + 1)));
                                
                            } elseif ($status_branch == '9901') { // QAL jateng 1 khusus
                                $otherBranches = Branch::whereIn('kode_branch', ['2003', '2002', '2001', '2025'])
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

                                $debet = round(($credit / (count($otherBranches) + 1)));

                            } elseif ($status_branch == '9902') { // QAL jateng 2 khusus
                                $otherBranches = Branch::whereIn('kode_branch', ['2003', '2002', '2001', '2025'])
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->where('status_aktif', 'aktif')
                                    ->get();

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

                            // kode yang akan diulang
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
        } catch (\Throwable $th) {
            DB::rollBack();
    
            dd($th);
            Log::error($th->getMessage());
        }
    
        return redirect()->back()->with('success', 'Proses pembebanan berhasil dijalankan.');
    }
}