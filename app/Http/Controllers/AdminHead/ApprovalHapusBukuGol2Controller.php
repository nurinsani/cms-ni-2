<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\PengajuanHapusBukuGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalHapusBukuGol2Controller extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Hapus Buku Inventaris Gol 2';
        $data['pengajuan_hapus_buku'] = PengajuanHapusBukuGol2::with(['branch', 'user', 'details.inventaris_gol_2'])
            ->where('status_approve', 'admin-head')
            ->latest()
            ->get();

        return view('admin-head.approval.pengajuan-hapus-buku-gol-2.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Approval Hapus Buku Gol 2';
        $pengajuan = PengajuanHapusBukuGol2::with(['branch', 'user', 'details.inventaris_gol_2', 'admin_head', 'akunting'])->findOrFail($id);

        $totalHargaPerolehan      = 0;
        $totalAkumulasiPenyusutan = 0;
        $totalNilaiBuku           = 0;

        foreach ($pengajuan->details as $detail) {
            $inv = $detail->inventaris_gol_2;
            if (!$inv) continue;

            $hargaPerolehan = intval($inv->harga_perolehan ?? 0);
            $totalHargaPerolehan += $hargaPerolehan;

            $akumulasi = $inv->penyusutans()->sum('penyusutan_per_bulan');
            $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

            $totalAkumulasiPenyusutan += $akumulasi;
            $totalNilaiBuku           += $nilaiBuku;
        }

        return view('admin-head.approval.pengajuan-hapus-buku-gol-2.show', compact(
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

            $pengajuan = PengajuanHapusBukuGol2::findOrFail($id);
            $pengajuan->status_approve = 'akunting';
            $pengajuan->atasan_1 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_1 = now();
            if ($request->filled('catatan')) {
                $pengajuan->catatan_atasan_1 = $request->catatan;
            }
            $pengajuan->save();

            DB::commit();

            return redirect()->route('admin-head.approval-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan Hapus Buku Gol 2 berhasil disetujui dan diteruskan ke Akunting.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error approve admin-head hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan: ' . $th->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanHapusBukuGol2::findOrFail($id);
            $pengajuan->status_approve = 'ditolak';
            $pengajuan->atasan_1 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_1 = now();
            $pengajuan->catatan_atasan_1 = $request->catatan ?? 'Ditolak oleh Admin Head';
            $pengajuan->save();

            DB::commit();

            return redirect()->route('admin-head.approval-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan Hapus Buku Gol 2 telah ditolak.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error tolak admin-head hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menolak pengajuan: ' . $th->getMessage());
        }
    }
}
