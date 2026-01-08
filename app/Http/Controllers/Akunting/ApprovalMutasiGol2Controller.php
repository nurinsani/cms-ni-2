<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\PengajuanMutasiGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiGol2Controller extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['pengajuan_mutasi'] = PengajuanMutasiGol2::where('status_approve', 'akunting-it')->get();
        return view('akunting.approval.pengajuan-mutasi-gol-2.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasiGol2::with('inventaris_gol_2')->findOrFail($id);
        return view('akunting.approval.pengajuan-mutasi-gol-2.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // ambil data mutasi
            $mutasi = PengajuanMutasiGol2::with('inventaris_gol_2', 'branch', 'dari_ap')->findOrFail($id);

            // update status approve
            $mutasi->status_approve = 'selesai';
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
            foreach ($mutasi->inventaris_gol_2 as $inventaris) {

                // pindah cabang inventaris
                $inventaris->kode_branch = $mutasi->ke_cabang;
                $inventaris->save();

                // hitung nilai per barang
                $akumulasi = 
                    $inventaris->akumulasi_penyusutan_awal() +
                    $inventaris->akumulasi_penyusutan();

                $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                $jumlahBulan = $inventaris->penyusutans()->count();
                $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                
                // $nilaiBuku = $inventaris->harga_perolehan - $akumulasi_penyusutan;

                $nilaiBuku = $inventaris->harga_perolehan - $akumulasi_penyusutan;

                if ($nilaiBuku < 100) {
                    $nilaiBuku = $akumulasi_penyusutan;
                }

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
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $akumulasi_penyusutan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU/1010/' . $kode_transaksi,
                'kode_rekening' => '1010-' . $glBertambah,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $akumulasi_penyusutan
            ]);

            // ===============================
            // JURNAL BERKURANG (CABANG ASAL)
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1674000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalNilaiBuku,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $akumulasi_penyusutan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1654000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
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
                'kode_rekening' => '1654000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => $totalHargaPerolehan,
                'kredit' => 0
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1674000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalNilaiBuku
            ]);

            Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'BU/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi Inventaris Gol 2 ' . $unitDari . ' ke ' . $unitKe,
                'debet' => 0,
                'kredit' => $akumulasi_penyusutan
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
