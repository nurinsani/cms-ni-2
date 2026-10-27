<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\InventarisGol2;
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
            ->where('status_approve', 'akunting')
            ->latest()
            ->get();

        return view('akunting.approval.pengajuan-hapus-buku-gol-2.index', $data);
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

        return view('akunting.approval.pengajuan-hapus-buku-gol-2.show', compact(
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

            $pengajuan = PengajuanHapusBukuGol2::with('details.inventaris_gol_2')->findOrFail($id);
            $pengajuan->status_approve = 'selesai';
            $pengajuan->atasan_2 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_2 = now();
            if ($request->filled('catatan')) {
                $pengajuan->catatan_atasan_2 = $request->catatan;
            }
            $pengajuan->save();

            $tanggalApprove = now();

            // Buat jurnal dan tandai inventaris gol 2 sebagai hapus buku
            foreach ($pengajuan->details as $detail) {
                $inv = $detail->inventaris_gol_2;
                if (!$inv) continue;

                // Tandai status inventaris gol 2
                $inv->status = 'hapus_buku';
                $inv->save();

                $kodeBranch     = $inv->kode_branch;
                $hargaPerolehan = intval($inv->harga_perolehan ?? 0);
                $akumulasi      = $inv->penyusutans()->sum('penyusutan_per_bulan');
                $nilaiBuku      = max(0, $hargaPerolehan - $akumulasi);

                $keteranganItem = $inv->keterangan ?? $inv->jenis ?? 'INVENTARIS GOL 2';
                $keterangan = 'HAPUS BUKU INVENTARIS GOL 2 - ' . strtoupper($keteranganItem) . ($inv->imei ? ' (IMEI: ' . $inv->imei . ')' : '');

                // Generate kode transaksi unik per inventaris
                $kodeTransaksi = 'HB2/' . $kodeBranch . '/' . date('m') . random_int(100000, 999999);

                /**
                 * Chart of Accounts Golongan 2:
                 * Aset (ATI-Inventaris-Gol II-Inventaris): 1654000
                 * Akumulasi Penyusutan (ATI-API-Gol II-Inventaris -/-): 1674000
                 * Rugi Kehilangan / Penjualan AT: 58114
                 */
                if ($nilaiBuku == 0) {
                    /**
                     * Skenario 1: Nilai Buku = 0
                     * Db  1674000  ATI-API-Gol II-Inventaris -/-    [harga perolehan]
                     * Cr  1654000  ATI-Inventaris-Gol II-Inventaris  [harga perolehan]
                     */
                    $jurnalEntries = [
                        [
                            'kode_rekening' => '1674000',
                            'debet'         => $hargaPerolehan,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1654000',
                            'debet'         => 0,
                            'kredit'        => $hargaPerolehan,
                        ],
                    ];
                } else {
                    /**
                     * Skenario 2: Nilai Buku ≠ 0
                     * Db  58114    BNO-Rugi Penjualan/Kehilangan-AT  [nilai buku]
                     * Cr  1674000  ATI-API-Gol II-Inventaris -/-      [nilai buku]
                     *
                     * Db  1674000  ATI-API-Gol II-Inventaris -/-      [harga perolehan]
                     * Cr  1654000  ATI-Inventaris-Gol II-Inventaris    [harga perolehan]
                     */
                    $jurnalEntries = [
                        [
                            'kode_rekening' => '58114',
                            'debet'         => $nilaiBuku,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1674000',
                            'debet'         => 0,
                            'kredit'        => $nilaiBuku,
                        ],
                        [
                            'kode_rekening' => '1674000',
                            'debet'         => $hargaPerolehan,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1654000',
                            'debet'         => 0,
                            'kredit'        => $hargaPerolehan,
                        ],
                    ];
                }

                // Insert ke transaksi
                foreach ($jurnalEntries as $entry) {
                    $row = [
                        'kode_branch'       => $kodeBranch,
                        'kode_transaksi'    => $kodeTransaksi,
                        'kode_rekening'     => $entry['kode_rekening'],
                        'tanggal_transaksi' => $tanggalApprove,
                        'jenis_transaksi'   => 'Bukti SYSTEM',
                        'keterangan'        => $keterangan,
                        'debet'             => $entry['debet'],
                        'kredit'            => $entry['kredit'],
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];

                    DB::table('transaksi')->insert($row);
                }
            }

            DB::commit();

            return redirect()->route('akunting.approval-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan Hapus Buku Gol 2 berhasil disetujui. Jurnal transaksi telah dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error approve akunting hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan: ' . $th->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanHapusBukuGol2::findOrFail($id);
            $pengajuan->status_approve = 'ditolak';
            $pengajuan->atasan_2 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_2 = now();
            $pengajuan->catatan_atasan_2 = $request->catatan ?? 'Ditolak oleh Akunting';
            $pengajuan->save();

            DB::commit();

            return redirect()->route('akunting.approval-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan Hapus Buku Gol 2 telah ditolak.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error tolak akunting hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menolak pengajuan: ' . $th->getMessage());
        }
    }
}
