<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventarisGol2;
use App\Models\PenyusutanGol2;
use App\Models\TransaksiPembebananApGol2;
use App\Models\TransaksiPembebananKpGol2;
use App\Models\TransaksiPenyusutanApGol2;
use App\Models\TransaksiPenyusutanKpGol2;
use App\Services\PembebananGol2Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenyusutanGol2Controller extends Controller
{
    protected $pembebananGol2Service;

    public function __construct(PembebananGol2Service $pembebananGol2Service)
    {
        $this->pembebananGol2Service = $pembebananGol2Service;
    }

    public function index()
    {
        $title = 'Proses Penyusutan & Pembebanan Gol 2';
        $currentMonth = date('m');
        $currentYear = date('Y');
        $periode = Carbon::now()->translatedFormat('F Y');

        // Status Langkah 1: Penyusutan Aset Gol 2
        $penyusutanCount = PenyusutanGol2::whereMonth('tanggal_penyusutan', $currentMonth)
            ->whereYear('tanggal_penyusutan', $currentYear)
            ->count();
        $penyusutanTotal = PenyusutanGol2::whereMonth('tanggal_penyusutan', $currentMonth)
            ->whereYear('tanggal_penyusutan', $currentYear)
            ->sum('penyusutan_per_bulan');
        $isPenyusutanDone = $penyusutanCount > 0;

        // Status Langkah 2: Jurnal Penyusutan Gol 2
        $countJurnalPenyusutanKp = TransaksiPenyusutanKpGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $countJurnalPenyusutanAp = TransaksiPenyusutanApGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $isJurnalPenyusutanDone = ($countJurnalPenyusutanKp > 0 || $countJurnalPenyusutanAp > 0);

        $isPenyusutanUploaded = TransaksiPenyusutanKpGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists() || TransaksiPenyusutanApGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();

        // Status Langkah 3: Jurnal Pembebanan Gol 2
        $countJurnalPembebananKp = TransaksiPembebananKpGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $countJurnalPembebananAp = TransaksiPembebananApGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $isJurnalPembebananDone = ($countJurnalPembebananKp > 0 || $countJurnalPembebananAp > 0);

        $isPembebananKpUploaded = TransaksiPembebananKpGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();
        $isPembebananApUploaded = TransaksiPembebananApGol2::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();

        return view('transaksi.penyusutan_gol_2.index', compact(
            'title',
            'periode',
            'penyusutanCount',
            'penyusutanTotal',
            'isPenyusutanDone',
            'countJurnalPenyusutanKp',
            'countJurnalPenyusutanAp',
            'isJurnalPenyusutanDone',
            'isPenyusutanUploaded',
            'countJurnalPembebananKp',
            'countJurnalPembebananAp',
            'isJurnalPembebananDone',
            'isPembebananKpUploaded',
            'isPembebananApUploaded'
        ));
    }

    public function jalankanPenyusutanGol2(Request $request)
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_penyusutan_gol2_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses penyusutan Gol 2 sedang berjalan. Silakan tunggu beberapa saat.');
        }

        try {
            // Cek apakah penyusutan sudah dilakukan di bulan ini
            $penyusutanSudahAda = PenyusutanGol2::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->exists();

            if ($penyusutanSudahAda) {
                return redirect()->back()->with('error', 'Proses penyusutan Gol 2 untuk bulan ini sudah dilakukan.');
            }

            DB::beginTransaction();

            $i = 0;
            $inventaris_gol_2 = InventarisGol2::where('status', 'aktif')->with('susut')->get();

            foreach ($inventaris_gol_2 as $value) {
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
                    (int) round($harga_perolehan / $penyusutan) : 0;

                $branch = Branch::where('kode_branch', $kode_branch)->first();

                if ((count($value->susut) + $value->penyusutan_ke) < 24) {
                    PenyusutanGol2::create([
                        'inventaris_gol_2_id' => $value->id,
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
                    $i++;
                }
            }

            DB::commit();
            Log::info("Proses penyusutan Gol 2 dijalankan: {$i} data ditambahkan.");

            return redirect()->back()->with('success', "Proses penyusutan Gol 2 berhasil dijalankan ({$i} data disusutkan).");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error pada jalankanPenyusutanGol2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses penyusutan Gol 2: ' . $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_penyusutan_gol2_lock')");
        }
    }

    public function jalankanJurnalPenyusutanGol2(Request $request)
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_jurnal_penyusutan_gol2_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses pembuatan jurnal penyusutan Gol 2 sedang berjalan. Silakan tunggu.');
        }

        try {
            // Validasi: Pastikan Langkah 1 (Penyusutan Aset Gol 2) sudah dijalankan
            $adaPenyusutan = PenyusutanGol2::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->exists();

            if (!$adaPenyusutan) {
                return redirect()->back()->with('error', 'Proses penyusutan aset Gol 2 (Langkah 1) belum dijalankan untuk bulan ini.');
            }

            // Validasi: Cek apakah jurnal sudah di-upload ke sistem NI
            $sudahUpload = TransaksiPenyusutanKpGol2::whereMonth('tanggal_transaksi', date('m'))
                ->whereYear('tanggal_transaksi', date('Y'))
                ->where('status', 'uploaded')
                ->exists() || TransaksiPenyusutanApGol2::whereMonth('tanggal_transaksi', date('m'))
                ->whereYear('tanggal_transaksi', date('Y'))
                ->where('status', 'uploaded')
                ->exists();

            if ($sudahUpload) {
                return redirect()->back()->with('error', 'Jurnal penyusutan Gol 2 bulan ini sudah di-upload ke sistem NI dan tidak dapat digenerate ulang.');
            }

            DB::beginTransaction();

            $branches = Branch::all();
            $countProcessed = 0;

            foreach ($branches as $branch) {
                $penyusutan_per_bulan = PenyusutanGol2::where('kode_branch', $branch->kode_branch)
                    ->whereMonth('tanggal_penyusutan', date('m'))
                    ->whereYear('tanggal_penyusutan', date('Y'))
                    ->sum('penyusutan_per_bulan');

                if ($penyusutan_per_bulan <= 0) {
                    continue;
                }

                if ($branch->kode_branch == '1010') {
                    $transaksiKpDebet = TransaksiPenyusutanKpGol2::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_transaksi', date('m'))
                        ->whereYear('tanggal_transaksi', date('Y'))
                        ->where('debet', '>', 0)
                        ->first();

                    $transaksiKpKredit = TransaksiPenyusutanKpGol2::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_transaksi', date('m'))
                        ->whereYear('tanggal_transaksi', date('Y'))
                        ->where('kredit', '>', 0)
                        ->first();

                    $kode_transaksi = date('m') . random_int(100000, 999999);
                    if ($transaksiKpDebet && $transaksiKpKredit) {
                        $transaksiKpDebet->update(['debet' => $penyusutan_per_bulan]);
                        $transaksiKpKredit->update(['kredit' => $penyusutan_per_bulan]);
                    } else {
                        TransaksiPenyusutanKpGol2::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-52913',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris Gol 2 ' . date('M Y'),
                            'debet' => $penyusutan_per_bulan,
                            'kredit' => 0,
                        ]);

                        TransaksiPenyusutanKpGol2::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-1674000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris Gol 2 ' . date('M Y'),
                            'debet' => 0,
                            'kredit' => $penyusutan_per_bulan,
                        ]);
                    }
                    $countProcessed++;
                } else {
                    $transaksiApKredit = TransaksiPenyusutanApGol2::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_transaksi', date('m'))
                        ->whereYear('tanggal_transaksi', date('Y'))
                        ->where('kredit', '>', 0)
                        ->first();

                    $transaksiApDebet = TransaksiPenyusutanApGol2::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_transaksi', date('m'))
                        ->whereYear('tanggal_transaksi', date('Y'))
                        ->where('debet', '>', 0)
                        ->first();

                    $kode_transaksi = date('m') . random_int(100000, 999999);
                    if ($transaksiApKredit && $transaksiApDebet) {
                        $transaksiApDebet->update(['debet' => $penyusutan_per_bulan]);
                        $transaksiApKredit->update(['kredit' => $penyusutan_per_bulan]);
                    } else {
                        TransaksiPenyusutanApGol2::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '52913',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris Gol 2 ' . date('M Y'),
                            'debet' => $penyusutan_per_bulan,
                            'kredit' => 0,
                        ]);

                        TransaksiPenyusutanApGol2::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1674000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris Gol 2 ' . date('M Y'),
                            'debet' => 0,
                            'kredit' => $penyusutan_per_bulan,
                        ]);
                    }
                    $countProcessed++;
                }
            }

            DB::commit();
            Log::info("Jurnal penyusutan Gol 2 berhasil dibuat/diperbarui untuk {$countProcessed} cabang.");

            return redirect()->back()->with('success', 'Jurnal penyusutan Gol 2 berhasil dibuat/diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error pada jalankanJurnalPenyusutanGol2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses jurnal penyusutan Gol 2: ' . $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_jurnal_penyusutan_gol2_lock')");
        }
    }

    public function jalankanJurnalPembebananGol2(Request $request)
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_pembebanan_gol2_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses pembebanan Gol 2 sedang berjalan. Silakan tunggu.');
        }

        try {
            $this->pembebananGol2Service->processAllCurrentMonth();
            return redirect()->back()->with('success', 'Proses pembebanan Gol 2 berhasil dijalankan.');
        } catch (\Throwable $th) {
            Log::error('Error pada jalankanJurnalPembebananGol2: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_pembebanan_gol2_lock')");
        }
    }
}
