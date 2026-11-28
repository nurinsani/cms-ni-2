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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['mutasi'] = PengajuanMutasi::with(['dari_ap', 'branch', 'inventaris.penyusutans'])
            ->where('status_approve', 'akunting-it')
            ->latest()
            ->get();
        return view('akunting.approval.pengajuan-mutasi.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasi::with(['dari_ap', 'branch', 'inventaris.penyusutans'])->findOrFail($id);
        return view('akunting.approval.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // ambil data mutasi
            $mutasi = PengajuanMutasi::with('inventaris', 'branch', 'dari_ap')->findOrFail($id);

            // update status approve
            $mutasi->status_approve = 'selesai';
            $mutasi->atasan_2 = auth()->user()->id;
            $mutasi->save();

            $url_api = 'http://127.0.0.1:8000/api/transaksi'; // URL API lokal
            // $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi'; // URL API production

            // ===============================
            // INISIALISASI TOTAL
            // ===============================
            $totalHargaPerolehan = 0;
            $totalAkumulasiPenyusutan = 0;
            $totalNilaiBuku = 0;

            // ===============================
            // LOOP INVENTARIS
            // ===============================
            foreach ($mutasi->inventaris as $inv) {

                // pindah cabang inventaris
                $inv->kode_branch = $mutasi->ke_cabang;
                $inv->save();

                // hitung nilai per barang
                $akumulasi = 
                    $inv->akumulasi_penyusutan_awal() +
                    $inv->akumulasi_penyusutan();

                $penyusutanPerBulan = $inv->harga_perolehan / 48;
                $jumlahBulan = $inv->penyusutans()->count();
                $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                
                // $nilaiBuku = $inventaris->harga_perolehan - $akumulasi_penyusutan;

                $nilaiBuku = $inv->harga_perolehan - $akumulasi_penyusutan;

                if ($nilaiBuku < 100) {
                    $nilaiBuku = $akumulasi_penyusutan;
                }

                // akumulasi total
                $totalHargaPerolehan += $inv->harga_perolehan;
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

            $transaksiKP = Transaksi::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'MT/1010/' . $kode_transaksi,
                'kode_rekening' => '1010-' . $glBerkurang,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => $totalNilaiBuku,
                'kredit' => 0
            ]);

            $transaksiApi = [
                'unit' => $transaksiKP->kode_branch,
                'kode_transaksi' => $transaksiKP->kode_transaksi,
                'kode_rekening' => $transaksiKP->kode_rekening,
                'tanggal_transaksi' => $transaksiKP->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiKP->jenis_transaksi,
                'keterangan_transaksi' => $transaksiKP->keterangan,
                'debet' => $transaksiKP->debet,
                'kredit' => $transaksiKP->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            $transaksiKP = Transaksi::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'MT/1010/' . $kode_transaksi,
                'kode_rekening' => '1010-' . $glBertambah,
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalNilaiBuku
            ]);

            $transaksiApi = [
                'unit' => $transaksiKP->kode_branch,
                'kode_transaksi' => $transaksiKP->kode_transaksi,
                'kode_rekening' => $transaksiKP->kode_rekening,
                'tanggal_transaksi' => $transaksiKP->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiKP->jenis_transaksi,
                'keterangan_transaksi' => $transaksiKP->keterangan,
                'debet' => $transaksiKP->debet,
                'kredit' => $transaksiKP->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            // ===============================
            // JURNAL BERKURANG (CABANG ASAL)
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            $transaksiApAsal = Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1674000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => $akumulasi_penyusutan,
                'kredit' => 0
            ]);

            $transaksiApi = [
                'unit' => $transaksiApAsal->kode_branch,
                'kode_transaksi' => $transaksiApAsal->kode_transaksi,
                'kode_rekening' => $transaksiApAsal->kode_rekening,
                'tanggal_transaksi' => $transaksiApAsal->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApAsal->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApAsal->keterangan,
                'debet' => $transaksiApAsal->debet,
                'kredit' => $transaksiApAsal->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            $transaksiApAsal = Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => $totalNilaiBuku,
                'kredit' => 0
            ]);

            $transaksiApi = [
                'unit' => $transaksiApAsal->kode_branch,
                'kode_transaksi' => $transaksiApAsal->kode_transaksi,
                'kode_rekening' => $transaksiApAsal->kode_rekening,
                'tanggal_transaksi' => $transaksiApAsal->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApAsal->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApAsal->keterangan,
                'debet' => $transaksiApAsal->debet,
                'kredit' => $transaksiApAsal->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            $transaksiApAsal = Transaksi::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->dari_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1654000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalHargaPerolehan
            ]);

            $transaksiApi = [
                'unit' => $transaksiApAsal->kode_branch,
                'kode_transaksi' => $transaksiApAsal->kode_transaksi,
                'kode_rekening' => $transaksiApAsal->kode_rekening,
                'tanggal_transaksi' => $transaksiApAsal->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApAsal->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApAsal->keterangan,
                'debet' => $transaksiApAsal->debet,
                'kredit' => $transaksiApAsal->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            // ===============================
            // JURNAL BERTAMBAH (CABANG TUJUAN)
            // ===============================
            $kode_transaksi = date('m') . random_int(100000, 999999);

            $transaksiApTujuan = Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1654000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => $totalHargaPerolehan,
                'kredit' => 0
            ]);

            $transaksiApi = [
                'unit' => $transaksiApTujuan->kode_branch,
                'kode_transaksi' => $transaksiApTujuan->kode_transaksi,
                'kode_rekening' => $transaksiApTujuan->kode_rekening,
                'tanggal_transaksi' => $transaksiApTujuan->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApTujuan->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApTujuan->keterangan,
                'debet' => $transaksiApTujuan->debet,
                'kredit' => $transaksiApTujuan->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            $transaksiApTujuan = Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '1674000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => 0,
                'kredit' => $akumulasi_penyusutan
            ]);

            $transaksiApi = [
                'unit' => $transaksiApTujuan->kode_branch,
                'kode_transaksi' => $transaksiApTujuan->kode_transaksi,
                'kode_rekening' => $transaksiApTujuan->kode_rekening,
                'tanggal_transaksi' => $transaksiApTujuan->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApTujuan->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApTujuan->keterangan,
                'debet' => $transaksiApTujuan->debet,
                'kredit' => $transaksiApTujuan->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            $transaksiApTujuan = Transaksi::create([
                'kode_branch' => $mutasi->ke_cabang,
                'kode_transaksi' => 'MT/'.$mutasi->ke_cabang.'/'.$kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'MUTASI INVENTARIS GOL 1 ' . $unitDari . ' KE ' . $unitKe,
                'debet' => 0,
                'kredit' => $totalNilaiBuku
            ]);

            $transaksiApi = [
                'unit' => $transaksiApTujuan->kode_branch,
                'kode_transaksi' => $transaksiApTujuan->kode_transaksi,
                'kode_rekening' => $transaksiApTujuan->kode_rekening,
                'tanggal_transaksi' => $transaksiApTujuan->tanggal_transaksi->toDateString(),
                'jenis_transaksi' => $transaksiApTujuan->jenis_transaksi,
                'keterangan_transaksi' => $transaksiApTujuan->keterangan,
                'debet' => $transaksiApTujuan->debet,
                'kredit' => $transaksiApTujuan->kredit,
                'tanggal_posting' => now()->toDateString(),
                'keterangan_posting' => 'Post',
                'id_admin' => auth()->id() ?? 1,
            ];

            $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post($url_api, $transaksiApi);

            if ($response->successful()) {
                Log::info("Transaksi berhasil", $response->json());
            } else {
                Log::error("Error API", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil di-approve');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return redirect()->back()->with('error', 'Data gagal di-approve');
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $mutasi = PengajuanMutasi::findOrFail($id);
            $mutasi->status_approve = 'admin';
            $mutasi->atasan_2 = auth()->user()->id;
            $mutasi->save();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil ditolak');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }

}
