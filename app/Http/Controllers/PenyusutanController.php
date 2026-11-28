<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\Penyusutan;
use App\Models\TransaksiPembebananAp;
use App\Models\TransaksiPembebananKp;
use App\Models\TransaksiPenyusutanAp;
use App\Models\TransaksiPenyusutanKp;
use App\Services\PembebananService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenyusutanController extends Controller
{
    protected $pembebananService;

    public function __construct(PembebananService $pembebananService)
    {
        $this->pembebananService = $pembebananService;
    }

    public function index()
    {
        $title = 'Proses Penyusutan & Pembebanan';
        $currentMonth = date('m');
        $currentYear = date('Y');
        $periode = Carbon::now()->translatedFormat('F Y');

        // Status Langkah 1: Penyusutan Aset
        $penyusutanCount = Penyusutan::whereMonth('tanggal_penyusutan', $currentMonth)
            ->whereYear('tanggal_penyusutan', $currentYear)
            ->count();
        $penyusutanTotal = Penyusutan::whereMonth('tanggal_penyusutan', $currentMonth)
            ->whereYear('tanggal_penyusutan', $currentYear)
            ->sum('penyusutan_per_bulan');
        $isPenyusutanDone = $penyusutanCount > 0;

        // Status Langkah 2: Jurnal Penyusutan
        $penyusutanKp = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->first();
        $penyusutanAp = TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->first();

        $countJurnalPenyusutanKp = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $countJurnalPenyusutanAp = TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $isJurnalPenyusutanDone = ($countJurnalPenyusutanKp > 0 || $countJurnalPenyusutanAp > 0);

        $isPenyusutanUploaded = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists() || TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();

        // Status Langkah 3: Jurnal Pembebanan
        $transaksiKp = TransaksiPembebananKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->first();
        $transaksiAp = TransaksiPembebananAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->first();

        $countJurnalPembebananKp = TransaksiPembebananKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $countJurnalPembebananAp = TransaksiPembebananAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->count();
        $isJurnalPembebananDone = ($countJurnalPembebananKp > 0 || $countJurnalPembebananAp > 0);

        $isPembebananKpUploaded = TransaksiPembebananKp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();
        $isPembebananApUploaded = TransaksiPembebananAp::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('status', 'uploaded')
            ->exists();

        return view('transaksi.penyusutan.index', compact(
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
            'isPembebananApUploaded',
            'transaksiKp',
            'transaksiAp',
            'penyusutanKp',
            'penyusutanAp'
        ));
    }

    public function jalankanPenyusutan(Request $request)
    {
        // Mutex lock untuk mencegah klik ganda atau race condition
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_penyusutan_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses penyusutan sedang berjalan. Silakan tunggu beberapa saat.');
        }

        try {
            // Cek apakah penyusutan sudah dilakukan di bulan ini
            $penyusutanSudahAda = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->exists();

            if ($penyusutanSudahAda) {
                return redirect()->back()->with('error', 'Proses penyusutan untuk bulan ini sudah pernah dilakukan.');
            }

            DB::beginTransaction();

            $i = 0;
            $inventaris = Inventaris::where('status', 'aktif')->with('susut')->get();
            $branches = Branch::all()->keyBy('kode_branch');
            $branchesByArea = Branch::all()->groupBy('code_area');

            foreach ($inventaris as $value) {
                $harga_perolehan = intval($value->harga_perolehan);
                $penyusutanUmur = intval($value->umur);
                $kode_branch = $value->kode_branch;

                $branch = $branches->get($kode_branch);
                if (!$branch) {
                    continue;
                }

                $areaBranches = $branchesByArea->get($branch->code_area);
                if (!$areaBranches || $areaBranches->isEmpty()) {
                    continue;
                }

                $hasil_penyusutan = $harga_perolehan > 0 && $penyusutanUmur > 0 ? $harga_perolehan / $penyusutanUmur : 0;
                $totalSusut = $value->penyusutan_ke + count($value->susut);

                if ($totalSusut < 48) {
                    // Logika untuk penyusutan dua kali jika tanggal perolehan antara 16-31 dan penyusutan awal 0
                    if (count($value->susut) == 0 && intval(date('d', strtotime($value->tanggal_perolehan))) >= 15) {
                        if ($value->status_branch !== 'AUDIT') {
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
                                $i++;
                            }
                        }
                    } else {
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
                            $i++;
                        }
                    }
                }
            }

            DB::commit();
            Log::info("Proses penyusutan manual dijalankan: {$i} data berhasil ditambahkan.");

            return redirect()->back()->with('success', "Proses penyusutan berhasil dijalankan ({$i} data disusutkan).");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error pada jalankanPenyusutan: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses penyusutan: ' . $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_penyusutan_lock')");
        }
    }

    public function jalankanJurnalPenyusutan(Request $request)
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_jurnal_penyusutan_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses pembuatan jurnal penyusutan sedang berjalan. Silakan tunggu.');
        }

        try {
            // Validasi: Pastikan Langkah 1 (Penyusutan Aset) sudah dijalankan
            $adaPenyusutan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->exists();

            if (!$adaPenyusutan) {
                return redirect()->back()->with('error', 'Proses penyusutan aset (Langkah 1) belum dijalankan untuk bulan ini.');
            }

            // Validasi: Cek apakah jurnal sudah di-upload ke sistem NI
            $sudahUpload = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', date('m'))
                ->whereYear('tanggal_transaksi', date('Y'))
                ->where('status', 'uploaded')
                ->exists() || TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', date('m'))
                ->whereYear('tanggal_transaksi', date('Y'))
                ->where('status', 'uploaded')
                ->exists();

            if ($sudahUpload) {
                return redirect()->back()->with('error', 'Jurnal penyusutan bulan ini sudah di-upload ke sistem NI dan tidak dapat digenerate ulang.');
            }

            DB::beginTransaction();

            $branches = Branch::all();
            $countProcessed = 0;

            foreach ($branches as $branch) {
                $penyusutan_per_bulan = Penyusutan::where('kode_branch', $branch->kode_branch)
                    ->whereMonth('tanggal_penyusutan', date('m'))
                    ->whereYear('tanggal_penyusutan', date('Y'))
                    ->sum('penyusutan_per_bulan');

                if ($penyusutan_per_bulan <= 0) {
                    continue;
                }

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
                        $transaksiKpDebet->update(['debet' => $penyusutan_per_bulan]);
                        $transaksiKpKredit->update(['kredit' => $penyusutan_per_bulan]);
                    } else {
                        TransaksiPenyusutanKp::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris ' . date('M Y'),
                            'debet' => $penyusutan_per_bulan,
                            'kredit' => 0,
                        ]);

                        TransaksiPenyusutanKp::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris ' . date('M Y'),
                            'debet' => 0,
                            'kredit' => $penyusutan_per_bulan,
                        ]);
                    }
                    $countProcessed++;
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
                        $transaksiApDebet->update(['debet' => $penyusutan_per_bulan]);
                        $transaksiApKredit->update(['kredit' => $penyusutan_per_bulan]);
                    } else {
                        TransaksiPenyusutanAp::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris ' . date('M Y'),
                            'debet' => $penyusutan_per_bulan,
                            'kredit' => 0,
                        ]);

                        TransaksiPenyusutanAp::create([
                            'kode_branch' => $branch->kode_branch,
                            'kode_transaksi' => 'BU/' . $branch->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Jurnal Penyusutan Inventaris ' . date('M Y'),
                            'debet' => 0,
                            'kredit' => $penyusutan_per_bulan,
                        ]);
                    }
                    $countProcessed++;
                }
            }

            DB::commit();
            Log::info("Jurnal penyusutan berhasil dibuat/diperbarui untuk {$countProcessed} cabang.");

            return redirect()->back()->with('success', 'Jurnal penyusutan berhasil dibuat/diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error pada jalankanJurnalPenyusutan: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses jurnal penyusutan: ' . $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_jurnal_penyusutan_lock')");
        }
    }

    public function jalankanJurnalPembebanan(Request $request)
    {
        $lockAcquired = DB::select("SELECT GET_LOCK('proses_pembebanan_lock', 2) as locked")[0]->locked ?? 0;
        if (!$lockAcquired) {
            return redirect()->back()->with('error', 'Proses pembebanan sedang berjalan. Silakan tunggu.');
        }

        try {
            $this->pembebananService->processAllCurrentMonth();
            return redirect()->back()->with('success', 'Proses pembebanan berhasil dijalankan.');
        } catch (\Throwable $th) {
            Log::error('Error pada jalankanJurnalPembebanan: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        } finally {
            DB::statement("SELECT RELEASE_LOCK('proses_pembebanan_lock')");
        }
    }
}
