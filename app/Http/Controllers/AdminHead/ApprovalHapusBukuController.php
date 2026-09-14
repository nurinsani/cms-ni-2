<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\PengajuanHapusBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalHapusBukuController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Hapus Buku Inventaris Gol 1';
        $data['pengajuan_hapus_buku'] = PengajuanHapusBuku::with(['branch', 'user', 'details.inventaris'])
            ->where('status_approve', 'admin-head')
            ->latest()
            ->get();

        return view('admin-head.approval.pengajuan-hapus-buku.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Approval Hapus Buku';
        $pengajuan = PengajuanHapusBuku::with(['branch', 'user', 'details.inventaris', 'admin_head', 'akunting'])->findOrFail($id);

        // Hitung ringkasan nilai buku dan penyusutan (mengikuti logika InventarisController)
        $totalHargaPerolehan      = 0;
        $totalAkumulasiPenyusutan = 0;
        $totalNilaiBuku           = 0;

        foreach ($pengajuan->details as $detail) {
            $inv = $detail->inventaris;
            if (!$inv) continue;

            $hargaPerolehan = intval($inv->harga_perolehan);
            $totalHargaPerolehan += $hargaPerolehan;

            $akumulasiDariPenyusutan = $inv->penyusutans()->sum('penyusutan_per_bulan');
            $totalPenyusutanAwal     = floatval($inv->total_penyusutan_awal ?? 0);
            $auditAkumulasi         = ($inv->status_branch === 'AUDIT')
                ? floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0)
                : 0;

            $akumulasi = $totalPenyusutanAwal + $akumulasiDariPenyusutan + $auditAkumulasi;
            $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

            $totalAkumulasiPenyusutan += $akumulasi;
            $totalNilaiBuku           += $nilaiBuku;
        }

        return view('admin-head.approval.pengajuan-hapus-buku.show', compact(
            'pengajuan',
            'title',
            'totalHargaPerolehan',
            'totalAkumulasiPenyusutan',
            'totalNilaiBuku'
        ));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanHapusBuku::findOrFail($id);
            $pengajuan->status_approve = 'akunting';
            $pengajuan->atasan_1 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_1 = now();
            if ($request->filled('catatan')) {
                $pengajuan->catatan_atasan_1 = $request->catatan;
            }
            $pengajuan->save();

            DB::commit();

            return redirect()->route('admin-head.approval-hapus-buku.index')
                ->with('success', 'Pengajuan Hapus Buku berhasil disetujui dan diteruskan ke Akunting.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error approve admin-head hapus buku: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan: ' . $th->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanHapusBuku::findOrFail($id);
            $pengajuan->status_approve = 'ditolak';
            $pengajuan->atasan_1 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_1 = now();
            $pengajuan->catatan_atasan_1 = $request->catatan ?? 'Ditolak oleh Admin Head';
            $pengajuan->save();

            DB::commit();

            return redirect()->route('admin-head.approval-hapus-buku.index')
                ->with('success', 'Pengajuan Hapus Buku telah ditolak.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error tolak admin-head hapus buku: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menolak pengajuan: ' . $th->getMessage());
        }
    }
}
