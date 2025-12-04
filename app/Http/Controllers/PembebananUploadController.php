<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembebananAp;
use App\Models\TransaksiPembebananApGol2;
use App\Models\TransaksiPembebananKp;
use App\Models\TransaksiPembebananKpGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PembebananUploadController extends Controller
{
    public function pembebananUploadApiKp(Request $request)
    {
        try {

            // $url_api = 'http://127.0.0.1:5500/api/transaksi';
            $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production
            

            $transaksi = TransaksiPembebananKp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();


            if (!$transaksi) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

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

                // UPDATE STATUS
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            Log::info("Upload transaksi berhasil", $response->json());

            return back()->with('success', 'Upload transaksi berhasil');

        } catch (\Throwable $e) {

            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function pembebananUploadApiAp(Request $request)
    {
        try {

            // $url_api = 'http://127.0.0.1:5500/api/transaksi';
            $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production

            $transaksi = TransaksiPembebananAp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();


            if (!$transaksi) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

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

                // UPDATE STATUS
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            Log::info("Upload transaksi berhasil", $response->json());

            return back()->with('success', 'Upload transaksi berhasil');

        } catch (\Throwable $e) {

            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function pembebananUploadApiGol2Kp(Request $request)
    {
        try {

            // $url_api = 'http://127.0.0.1:5500/api/transaksi';
            $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production


            $transaksi = TransaksiPembebananKpGol2::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();


            if (!$transaksi) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

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

                // UPDATE STATUS
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            Log::info("Upload transaksi berhasil", $response->json());

            return back()->with('success', 'Upload transaksi berhasil');

        } catch (\Throwable $e) {

            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function pembebananUploadApiGol2Ap(Request $request)
    {
        try {

            // $url_api = 'http://127.0.0.1:5500/api/transaksi';
            $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production


            $transaksi = TransaksiPembebananApGol2::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();


            if (!$transaksi) {
                throw new \Exception("Tidak ada transaksi untuk diupload.");
            }

            foreach ($transaksi as $row) {

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

                // UPDATE STATUS
                $row->update([
                    'status' => 'uploaded'
                ]);
            }

            Log::info("Upload transaksi berhasil", $response->json());

            return back()->with('success', 'Upload transaksi berhasil');

        } catch (\Throwable $e) {

            Log::error("Upload error", [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }
}
