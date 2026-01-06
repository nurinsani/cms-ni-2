<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\PengajuanMutasi;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['mutasi'] = PengajuanMutasi::where('status_approve', 'akunting-it')->get();
        return view('akunting.approval.pengajuan-mutasi.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasi::with('inventaris')->findOrFail($id);
        return view('akunting.approval.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // ambil data mutasi
            $mutasi = PengajuanMutasi::with('inventaris', 'branch', 'dari_ap')->findOrFail($id);

            // update status approve
            $mutasi->status_approve = 'akunting-it';
            $mutasi->atasan_2 = auth()->user()->id;
            $mutasi->save();

            // ===============================
            // INISIALISASI TOTAL
            // ===============================
            $totalHargaPerolehan = 0;
            $totalAkumulasiPenyusutan = 0;
            $totalNilaiBuku = 0;

            // ===============================
            // LOOP INVENTARIS
            // ===============================
            foreach ($mutasi->inventaris as $inventaris) {

                // pindah cabang inventaris
                $inventaris->kode_branch = $mutasi->ke_cabang;
                $inventaris->save();

                // hitung nilai per barang
                $akumulasi = 
                    $inventaris->akumulasi_penyusutan_awal() +
                    $inventaris->akumulasi_penyusutan();

                $nilaiBuku = $inventaris->harga_perolehan - $akumulasi;

                // akumulasi total
                $totalHargaPerolehan += $inventaris->harga_perolehan;
                $totalAkumulasiPenyusutan += $akumulasi;
                $totalNilaiBuku += $nilaiBuku;
            }

            $branchBerkurang = Branch::where('kode_branch', $mutasi->ke_cabang)->first();

            $glBerkurang = $branchBerkurang?->GL;

            $branchBertambah = Branch::where('kode_branch', $mutasi->dari_cabang)->first();

            $glBertambah = $branchBertambah?->GL;

            $unitDari = $mutasi->dari_ap->unit ?? $mutasi->dari_cabang;
            $unitKe   = $mutasi->branch->unit ?? $mutasi->ke_cabang;

            // ===============================
            // JURNAL KP
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            Transaksi::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU/1010/' . $kode_transaksi,
                'kode_rekening' => '1010-' . $glBerkurang,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalAkumulasiPenyusutan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU/1010/' . $kode_transaksi,
                'kode_rekening' => '1010-' . $glBertambah,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalAkumulasiPenyusutan
            ]);

            // ===============================
            // JURNAL BERKURANG (CABANG ASAL)
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1672000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalHargaPerolehan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalAkumulasiPenyusutan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1652000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalHargaPerolehan
            ]);

            // ===============================
            // JURNAL BERTAMBAH (CABANG TUJUAN)
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1652000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalHargaPerolehan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1672000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalHargaPerolehan
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalAkumulasiPenyusutan
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil di-approve');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return redirect()->back()->with('error', 'Data gagal di-approve');
        }
    }

}
