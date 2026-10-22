<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class LogPenghapusanBarangController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Log Penghapusan Barang';

        $query = Inventaris::with([
            'branch',
            'pengajuan_hapus_buku' => function ($q) {
                $q->with(['admin_head', 'akunting', 'user']);
            },
            'penyusutans'
        ])->where('status', 'hapus_buku');

        if ($request->filled('kode_branch')) {
            $query->where('kode_branch', $request->kode_branch);
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

        return view('akunting.log-penghapusan-barang.index', compact(
            'inventaris',
            'branches',
            'title'
        ));
    }

    public function show($id)
    {
        $title = 'Detail Log Penghapusan Barang';

        $inventaris = Inventaris::with([
            'branch',
            'pengajuan_hapus_buku' => function ($q) {
                $q->with(['admin_head', 'akunting', 'user']);
            },
            'penyusutans'
        ])->where('status', 'hapus_buku')->findOrFail($id);

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

        return view('akunting.log-penghapusan-barang.show', compact('inventaris', 'pengajuan', 'title'));
    }
}
