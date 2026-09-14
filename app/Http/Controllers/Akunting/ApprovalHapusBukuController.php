<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
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
            ->where('status_approve', 'akunting')
            ->latest()
            ->get();

        return view('akunting.approval.pengajuan-hapus-buku.index', $data);
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

        return view('akunting.approval.pengajuan-hapus-buku.show', compact(
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

            $pengajuan = PengajuanHapusBuku::with('details.inventaris')->findOrFail($id);
            $pengajuan->status_approve = 'selesai';
            $pengajuan->atasan_2 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_2 = now();
            if ($request->filled('catatan')) {
                $pengajuan->catatan_atasan_2 = $request->catatan;
            }
            $pengajuan->save();

            $tanggalApprove = now();

            // Buat jurnal dan tandai inventaris sebagai hapus buku
            foreach ($pengajuan->details as $detail) {
                $inv = $detail->inventaris;
                if (!$inv) continue;

                // Tandai status inventaris
                $inv->status = 'hapus_buku';
                $inv->save();

                $kodeBranch    = $inv->kode_branch;
                $hargaPerolehan = intval($inv->harga_perolehan);

                // Hitung akumulasi penyusutan & nilai buku
                // Mengikuti logika InventarisController:
                // akumulasi = total_penyusutan_awal (dari kolom) + sum(penyusutan_per_bulan) dari tabel penyusutan
                $akumulasiDariPenyusutan = $inv->penyusutans()
                    ->sum('penyusutan_per_bulan');

                $totalPenyusutanAwal = floatval($inv->total_penyusutan_awal ?? 0);

                // Tambah akumulasi dari kolom jika status AUDIT
                $auditAkumulasi = 0;
                if ($inv->status_branch === 'AUDIT') {
                    $auditAkumulasi = floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0);
                }

                $akumulasi = $totalPenyusutanAwal + $akumulasiDariPenyusutan + $auditAkumulasi;
                $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

                $keterangan = 'HAPUS BUKU INVENTARIS GOL 1 - ' . strtoupper($inv->keterangan);

                // Generate kode transaksi unik per inventaris
                $kodeTransaksi = 'HB/' . $kodeBranch . '/' . date('m') . random_int(100000, 999999);

                if ($nilaiBuku == 0) {
                    /**
                     * Skenario 1: Nilai Buku = 0
                     * Db  1672000  ATI-API-Gol I-Inventaris -/-    [harga perolehan]
                     * Cr  1652000  ATI-Inventaris-Gol I-Inventaris  [harga perolehan]
                     */
                    $jurnalEntries = [
                        [
                            'kode_rekening' => '1672000',
                            'debet'         => $hargaPerolehan,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1652000',
                            'debet'         => 0,
                            'kredit'        => $hargaPerolehan,
                        ],
                    ];
                } else {
                    /**
                     * Skenario 2: Nilai Buku ≠ 0
                     * Db  58114    BNO-Rugi Penjualan/Kehilangan-AT  [nilai buku]
                     * Cr  1672000  ATI-API-Gol I-Inventaris -/-       [nilai buku]
                     *
                     * Db  1672000  ATI-API-Gol I-Inventaris -/-       [harga perolehan]
                     * Cr  1652000  ATI-Inventaris-Gol I-Inventaris    [harga perolehan]
                     */
                    $jurnalEntries = [
                        [
                            'kode_rekening' => '58114',
                            'debet'         => $nilaiBuku,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1672000',
                            'debet'         => 0,
                            'kredit'        => $nilaiBuku,
                        ],
                        [
                            'kode_rekening' => '1672000',
                            'debet'         => $hargaPerolehan,
                            'kredit'        => 0,
                        ],
                        [
                            'kode_rekening' => '1652000',
                            'debet'         => 0,
                            'kredit'        => $hargaPerolehan,
                        ],
                    ];
                }

                // Insert ke transaksi_ap dan transaksi_kp
                foreach ($jurnalEntries as $entry) {
                    $row = [
                        'kode_branch'      => $kodeBranch,
                        'kode_transaksi'   => $kodeTransaksi,
                        'kode_rekening'    => $entry['kode_rekening'],
                        'tanggal_transaksi' => $tanggalApprove,
                        'jenis_transaksi'  => 'Bukti SYSTEM',
                        'keterangan'       => $keterangan,
                        'debet'            => $entry['debet'],
                        'kredit'           => $entry['kredit'],
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];

                    DB::table('transaksi')->insert($row);
                }
            }

            DB::commit();

            return redirect()->route('akunting.approval-hapus-buku.index')
                ->with('success', 'Pengajuan Hapus Buku berhasil disetujui. Jurnal transaksi telah dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error approve akunting hapus buku: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan: ' . $th->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pengajuan = PengajuanHapusBuku::findOrFail($id);
            $pengajuan->status_approve = 'ditolak';
            $pengajuan->atasan_2 = Auth::user()->id;
            $pengajuan->tanggal_approve_atasan_2 = now();
            $pengajuan->catatan_atasan_2 = $request->catatan ?? 'Ditolak oleh Akunting';
            $pengajuan->save();

            DB::commit();

            return redirect()->route('akunting.approval-hapus-buku.index')
                ->with('success', 'Pengajuan Hapus Buku telah ditolak.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error tolak akunting hapus buku: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menolak pengajuan: ' . $th->getMessage());
        }
    }
}
