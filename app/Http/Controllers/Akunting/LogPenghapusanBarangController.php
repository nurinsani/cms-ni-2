<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\InventarisGol2;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class LogPenghapusanBarangController extends Controller
{
    public function index(Request $request)
    {
        $gol = $request->input('gol', '1');
        $title = 'Log Hapus Buku ' . ($gol == '2' ? 'Inventaris Gol 2' : 'Inventaris Gol 1');

        if ($gol == '2') {
            $query = InventarisGol2::with([
                'branch',
                'pengajuan_hapus_buku' => function ($q) {
                    $q->with(['admin_head', 'akunting', 'user']);
                },
                'penyusutans'
            ])->where('status', 'hapus_buku');
        } else {
            $query = Inventaris::with([
                'branch',
                'pengajuan_hapus_buku' => function ($q) {
                    $q->with(['admin_head', 'akunting', 'user']);
                },
                'penyusutans'
            ])->where('status', 'hapus_buku');
        }

        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // 1. Filter Unit (Cabang / AP)
        $selectedUnit = $request->input('unit', $request->input('kode_branch'));
        if (!empty($selectedUnit)) {
            $query->where('kode_branch', $selectedUnit);
        }

        // 2. Filter Bulan
        $selectedBulan = $request->input('bulan');
        if (!empty($selectedBulan)) {
            $monthIndex = null;
            if (is_numeric($selectedBulan)) {
                $mNum = (int)$selectedBulan;
                if ($mNum >= 1 && $mNum <= 12) {
                    $monthIndex = $mNum;
                    $bulanNama = $bulanList[$mNum - 1];
                } else {
                    $bulanNama = $selectedBulan;
                }
            } else {
                $bulanNama = $selectedBulan;
                $foundKey = array_search(strtolower($selectedBulan), array_map('strtolower', $bulanList));
                if ($foundKey !== false) {
                    $monthIndex = $foundKey + 1;
                }
            }

            $query->whereHas('pengajuan_hapus_buku', function ($q) use ($bulanNama, $monthIndex) {
                $q->where(function ($sub) use ($bulanNama, $monthIndex) {
                    $sub->where('bulan_hapus_buku', $bulanNama);
                    if ($monthIndex) {
                        $sub->orWhereMonth('tanggal_approve_atasan_2', $monthIndex)
                            ->orWhereMonth('created_at', $monthIndex);
                    }
                });
            });
        }

        // 3. Filter Tahun
        $selectedTahun = $request->input('tahun');
        if (!empty($selectedTahun)) {
            $query->where(function ($q) use ($selectedTahun) {
                $q->whereHas('pengajuan_hapus_buku', function ($sub) use ($selectedTahun) {
                    $sub->whereYear('tanggal_approve_atasan_2', $selectedTahun)
                        ->orWhereYear('created_at', $selectedTahun);
                })->orWhereYear('updated_at', $selectedTahun);
            });
        }

        $inventaris = $query->latest('updated_at')->get();

        // Hitung akumulasi dan nilai buku per item untuk tabel
        foreach ($inventaris as $item) {
            $hargaPerolehan = intval($item->harga_perolehan ?? 0);
            $akumDariPenyusutan = $item->penyusutans->sum('penyusutan_per_bulan');
            $penyusutanAwal = floatval($item->total_penyusutan_awal ?? 0);
            $auditAkum = ($item->status_branch === 'AUDIT')
                ? floatval($item->getRawOriginal('akumulasi_penyusutan') ?? 0)
                : 0;

            $akumulasi = $penyusutanAwal + $akumDariPenyusutan + $auditAkum;
            $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

            $item->calc_akumulasi = $akumulasi;
            $item->calc_nilai_buku = $nilaiBuku;
        }

        $branches = Branch::where('status_aktif', 'aktif')->orderBy('unit', 'asc')->get();
        if ($branches->isEmpty()) {
            $branches = Branch::orderBy('unit', 'asc')->get();
        }

        // Generate daftar tahun yang tersedia
        $dbYears = DB::table('pengajuan_hapus_buku')
            ->selectRaw('YEAR(created_at) as yr')
            ->union(
                DB::table('pengajuan_hapus_buku_gol_2')->selectRaw('YEAR(created_at) as yr')
            )
            ->pluck('yr')
            ->filter()
            ->toArray();

        $currentYear = (int) date('Y');
        $years = array_unique(array_merge([$currentYear, $currentYear - 1, $currentYear - 2], $dbYears));
        rsort($years);

        return view('akunting.log-penghapusan-barang.index', compact(
            'inventaris',
            'branches',
            'title',
            'gol',
            'bulanList',
            'years',
            'selectedUnit',
            'selectedBulan',
            'selectedTahun'
        ));
    }

    public function show(Request $request, $id)
    {
        $gol = $request->input('gol', '1');
        $title = 'Detail Log Hapus Buku ' . ($gol == '2' ? 'Inventaris Gol 2' : 'Inventaris Gol 1');

        if ($gol == '2') {
            $inventaris = InventarisGol2::with([
                'branch',
                'pengajuan_hapus_buku' => function ($q) {
                    $q->with(['admin_head', 'akunting', 'user']);
                },
                'penyusutans'
            ])->where('status', 'hapus_buku')->findOrFail($id);
        } else {
            $inventaris = Inventaris::with([
                'branch',
                'pengajuan_hapus_buku' => function ($q) {
                    $q->with(['admin_head', 'akunting', 'user']);
                },
                'penyusutans'
            ])->where('status', 'hapus_buku')->findOrFail($id);
        }

        $hargaPerolehan = intval($inventaris->harga_perolehan ?? 0);
        $akumDariPenyusutan = $inventaris->penyusutans->sum('penyusutan_per_bulan');
        $penyusutanAwal = floatval($inventaris->total_penyusutan_awal ?? 0);
        $auditAkum = ($inventaris->status_branch === 'AUDIT')
            ? floatval($inventaris->getRawOriginal('akumulasi_penyusutan') ?? 0)
            : 0;

        $inventaris->calc_akumulasi = $penyusutanAwal + $akumDariPenyusutan + $auditAkum;
        $inventaris->calc_nilai_buku = max(0, $hargaPerolehan - $inventaris->calc_akumulasi);

        // Ambil data pengajuan terkait jika ada
        $pengajuan = $inventaris->pengajuan_hapus_buku->first();

        return view('akunting.log-penghapusan-barang.show', compact('inventaris', 'pengajuan', 'title', 'gol'));
    }
}
