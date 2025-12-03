<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenyusutanAp;
use App\Models\TransaksiPenyusutanApGol2;
use App\Models\TransaksiPenyusutanKp;
use App\Models\TransaksiPenyusutanKpGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PenyusutanUploadController extends Controller
{
    public function penyusutanUploadApi(Request $request)
    {
        try {
            $url_api = 'http://127.0.0.1:5500/api/transaksi';
            // $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production


            // Ambil data dari dua tabel
            $kp = TransaksiPenyusutanKp::whereMonth('tanggal_transaksi', date('m'))
                    ->whereYear('tanggal_transaksi', date('Y'))
                    ->get();

            $ap = TransaksiPenyusutanAp::whereMonth('tanggal_transaksi', date('m'))
                    ->whereYear('tanggal_transaksi', date('Y'))
                    ->get();

            // Gabungkan collection
            $transaksi = $kp->merge($ap);

            if ($transaksi->isEmpty()) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

                // jika sudah uploaded → skip
                if ($row->status == 'uploaded') {
                    continue;
                }

                $payload = [
                    'unit' => $row->kode_branch,
                    'kode_transaksi' => $row->kode_transaksi,
                    'kode_rekening' => $row->kode_rekening,
                    'tanggal_transaksi' => $row->tanggal_transaksi->toDateString(),
                    'jenis_transaksi' => $row->jenis_transaksi,
                    'keterangan_transaksi' => $row->keterangan,
                    'debet' => $row->debet,
                    'kredit' => $row->kredit,
                    'tanggal_posting' => now()->toDateString(),
                    'keterangan_posting' => 'Post',
                    'id_admin' => auth()->id() ?? 1,
                ];

                $response = Http::timeout(30)
                    ->asForm()
                    ->post($url_api, $payload);

                if (!$response->successful()) {
                    throw new \Exception("API gagal: " . $response->body());
                }

                // Update status
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            return back()->with('success', 'Upload penyusutan berhasil');

        } catch (\Throwable $e) {
            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function penyusutanUploadApiGol2(Request $request)
    {
        try {
            $url_api = 'http://127.0.0.1:5500/api/transaksi';
            // $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production


            // Ambil data dari dua tabel
            $kp = TransaksiPenyusutanKpGol2::whereMonth('tanggal_transaksi', date('m'))
                    ->whereYear('tanggal_transaksi', date('Y'))
                    ->get();

            $ap = TransaksiPenyusutanApGol2::whereMonth('tanggal_transaksi', date('m'))
                    ->whereYear('tanggal_transaksi', date('Y'))
                    ->get();

            // Gabungkan collection
            $transaksi = $kp->merge($ap);

            if ($transaksi->isEmpty()) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

                // jika sudah uploaded → skip
                if ($row->status == 'uploaded') {
                    continue;
                }

                $payload = [
                    'unit' => $row->kode_branch,
                    'kode_transaksi' => $row->kode_transaksi,
                    'kode_rekening' => $row->kode_rekening,
                    'tanggal_transaksi' => $row->tanggal_transaksi->toDateString(),
                    'jenis_transaksi' => $row->jenis_transaksi,
                    'keterangan_transaksi' => $row->keterangan,
                    'debet' => $row->debet,
                    'kredit' => $row->kredit,
                    'tanggal_posting' => now()->toDateString(),
                    'keterangan_posting' => 'Post',
                    'id_admin' => auth()->id() ?? 1,
                ];

                $response = Http::timeout(30)
                    ->asForm()
                    ->post($url_api, $payload);

                if (!$response->successful()) {
                    throw new \Exception("API gagal: " . $response->body());
                }

                // Update status
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            return back()->with('success', 'Upload penyusutan berhasil');

        } catch (\Throwable $e) {
            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

}
