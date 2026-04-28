<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\HistoryApprove;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApprovalLpjController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'lpj-finance')->get();
        // $data['lpj'] = Pengajuan::where('status_approve', 'selesai')->get();
        return view('finance.approval.pengajuan-lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        return view('finance.approval.pengajuan-lpj.detail', $data);
    }

    public function detail_cetak($id)
    {
        $data['title'] = 'Detail Cetak LPJ';
        $data['lpj'] = $lpj = Pengajuan::findOrFail($id);

        if ($lpj->approval1) {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = (int) substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }

            $data['diajukan'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->user->id . '/' . $nomor_urut;
            $data['kode_approve1'] = ($lpj->id ?? '') . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . ($lpj->approval1?->id ?? '') . '/' . $nomor_urut;
            $data['kode_approve2'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval2->id . '/' . $nomor_urut;
            $data['kode_approve3'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval3->id . '/' . $nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval4->id . '/' . $nomor_urut;
        } else {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = (int) substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }

            $data['diajukan'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->user->id . '/' . $nomor_urut;
            $data['kode_approve3'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval3->id . '/' . $nomor_urut;
            $data['kode_approve2'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval2->id . '/' . $nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval4->id . '/' . $nomor_urut;

            // $data['kode_approve5'] = $lpj->id . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval5->id . '/' . $nomor_urut;

            // $data['kode_approve5'] = $lpj->id ?? '' . '/' . $lpj->updated_at->format('Y-m-d-Hid') . '/' . $lpj->approval5->id ?? '' . '/' . $nomor_urut;
        }

        HistoryApprove::updateOrCreate([
            'pengajuan_id' => $lpj->id,
            'user_id' => $lpj->user_id,
            'barcode' => $kode_approve4,
        ]);

        $lpj->update([
            'status_cetak' => 'sudah-cetak-fpd',
        ]);

        return view('finance.approval.pengajuan-lpj.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'selesai';
            $pengajuan->save();


            $uniqueKodeArea = $pengajuan->pengajuan_details->pluck('kode_branch')->unique()->count();


            // jurnal untuk KP
            foreach ($pengajuan->pengajuan_details as $row) {

                if ($uniqueKodeArea == 1) {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $row->jenis_peralatan->nama_jenis . ' ' . 'WILAYAH' . ' ' . $pengajuan->area->area);
                } elseif ($row->kode_branch == '1010') {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $row->jenis_peralatan->nama_jenis . ' ' . 'KANTOR PUSAT');
                } elseif ($uniqueKodeArea > 1 ) {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $row->jenis_peralatan->nama_jenis . ' ' . 'AP' . ' ' . $row->ap->unit);
                }

                // $url_api = 'http://127.0.0.1:8000/api/transaksi'; // URL API lokal
                // $url_api = 'http://36.64.7.130:7117/nurinsani/api/transaksi_cms.php?function=insert_transaksi'; // URL API test
                $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production

                $kode_transaksi_kp = date('m') . random_int(100000, 999999);

                // jika pembelian ada di KP
                if ($row->kode_branch == '1010') {
                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-' . $row->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => $row->harga_pembelian,
                        'kredit' => 0
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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

                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1311000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => $pengajuan->nominal_pengembalian ?? 0,
                        'kredit' => 0
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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

                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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
                }

                $area = Branch::where('code_area', $row->kode_area)
                    ->where('status_aktif', 'aktif')
                    ->get();
                $total_branch = $area->count();

                if ($row->kode_branch !== '1010') {
                    if ($row->kode_branch == null) {
                        foreach ($area as $key => $value) {
                            $transaksiKp = TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                                'kode_rekening' => '1010-' . $value->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $keterangan,
                                'debet' => $row->harga_pembelian / $total_branch,
                                'kredit' => 0
                            ]);

                            $transaksiApi = [
                                'unit' => $transaksiKp->kode_branch,
                                'kode_transaksi' => $transaksiKp->kode_transaksi,
                                'kode_rekening' => $transaksiKp->kode_rekening,
                                'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                                'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                                'keterangan_transaksi' => $transaksiKp->keterangan,
                                'debet' => $transaksiKp->debet,
                                'kredit' => $transaksiKp->kredit,
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
                        }
                    } else {
                        $transaksiKp = TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                            'kode_rekening' => '1010-' . $row->ap->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => $row->harga_pembelian,
                            'kredit' => 0
                        ]);

                        $transaksiApi = [
                            'unit' => $transaksiKp->kode_branch,
                            'kode_transaksi' => $transaksiKp->kode_transaksi,
                            'kode_rekening' => $transaksiKp->kode_rekening,
                            'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                            'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                            'keterangan_transaksi' => $transaksiKp->keterangan,
                            'debet' => $transaksiKp->debet,
                            'kredit' => $transaksiKp->kredit,
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
                    }
                }
            }

            // pengajuan kektika pembelian pas - jurnal untuk KP
            if ($row->kode_branch !== '1010') {
                if ($pengajuan->subtotal() <= $pengajuan->total_pembelian()) {
                    
                } else {
                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1311000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => $pengajuan->nominal_pengembalian ?? 0,
                        'kredit' => 0
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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
                }
                
                if ($pengajuan->subtotal() < $pengajuan->total_pembelian()) {

                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1312000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => 0,
                        'kredit' => $row->harga_pembelian - $pengajuan->subtotal()
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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

                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);

                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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

                } else{
                    $transaksiKp = TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi_kp,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $keterangan,
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
    
                    $transaksiApi = [
                        'unit' => $transaksiKp->kode_branch,
                        'kode_transaksi' => $transaksiKp->kode_transaksi,
                        'kode_rekening' => $transaksiKp->kode_rekening,
                        'tanggal_transaksi' => $transaksiKp->tanggal_transaksi->toDateString(),
                        'jenis_transaksi' => $transaksiKp->jenis_transaksi,
                        'keterangan_transaksi' => $transaksiKp->keterangan,
                        'debet' => $transaksiKp->debet,
                        'kredit' => $transaksiKp->kredit,
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
                }
                
            }

            // jurnal untuk AP
            foreach ($pengajuan->pengajuan_details as $row) {
                $kode_transaksi_kp = date('m') . random_int(100000, 999999);

                $area = Branch::where('code_area', $row->kode_area)
                    ->where('status_aktif', 'aktif')
                    ->get();
                $total_branch = $area->count();

                if ($row->kode_branch !== '1010') {
                    if ($row->kode_branch == null) {

                        foreach ($area as $key => $value) {
                            $transaksiAp = TransaksiAp::create([
                                'kode_branch' => $value->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi_kp,
                                'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $keterangan,
                                'debet' => $row->harga_pembelian / $total_branch,
                                'kredit' => 0
                            ]);

                            $transaksiApi = [
                                'unit' => $transaksiAp->kode_branch,
                                'kode_transaksi' => $transaksiAp->kode_transaksi,
                                'kode_rekening' => $transaksiAp->kode_rekening,
                                'tanggal_transaksi' => $transaksiAp->tanggal_transaksi->toDateString(),
                                'jenis_transaksi' => $transaksiAp->jenis_transaksi,
                                'keterangan_transaksi' => $transaksiAp->keterangan,
                                'debet' => $transaksiAp->debet,
                                'kredit' => $transaksiAp->kredit,
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

                            $transaksiAp = TransaksiAp::create([
                                'kode_branch' => $value->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi_kp,
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $keterangan,
                                'debet' => 0,
                                'kredit' => $row->harga_pembelian / $total_branch
                            ]);

                            $transaksiApi = [
                                'unit' => $transaksiAp->kode_branch,
                                'kode_transaksi' => $transaksiAp->kode_transaksi,
                                'kode_rekening' => $transaksiAp->kode_rekening,
                                'tanggal_transaksi' => $transaksiAp->tanggal_transaksi->toDateString(),
                                'jenis_transaksi' => $transaksiAp->jenis_transaksi,
                                'keterangan_transaksi' => $transaksiAp->keterangan,
                                'debet' => $transaksiAp->debet,
                                'kredit' => $transaksiAp->kredit,
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
                        }
                    } else {

                        $transaksiAp = TransaksiAp::create([
                            'kode_branch' => $row->ap->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $row->ap->kode_branch . '/' . $kode_transaksi_kp,
                            'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => $row->harga_pembelian,
                            'kredit' => 0
                        ]);

                        $transaksiApi = [
                            'unit' => $transaksiAp->kode_branch,
                            'kode_transaksi' => $transaksiAp->kode_transaksi,
                            'kode_rekening' => $transaksiAp->kode_rekening,
                            'tanggal_transaksi' => $transaksiAp->tanggal_transaksi->toDateString(),
                            'jenis_transaksi' => $transaksiAp->jenis_transaksi,
                            'keterangan_transaksi' => $transaksiAp->keterangan,
                            'debet' => $transaksiAp->debet,
                            'kredit' => $transaksiAp->kredit,
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

                        $transaksiAp = TransaksiAp::create([
                            'kode_branch' => $row->ap->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $row->ap->kode_branch . '/' . $kode_transaksi_kp,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => 0,
                            'kredit' => $row->harga_pembelian
                        ]);

                        $transaksiApi = [
                            'unit' => $transaksiAp->kode_branch,
                            'kode_transaksi' => $transaksiAp->kode_transaksi,
                            'kode_rekening' => $transaksiAp->kode_rekening,
                            'tanggal_transaksi' => $transaksiAp->tanggal_transaksi->toDateString(),
                            'jenis_transaksi' => $transaksiAp->jenis_transaksi,
                            'keterangan_transaksi' => $transaksiAp->keterangan,
                            'debet' => $transaksiAp->debet,
                            'kredit' => $transaksiAp->kredit,
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
                    }
                }
            }

            // end jurnal LPJ pembelian lebih kecil dari total pengajuan

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }
    }

    public function tolak(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'lpj-akunting-it';
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }

    public function approve_lpj()
    {
        $data['title'] = 'Pengajuan LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'selesai')->get();
        return view('finance.approval.pengajuan-lpj.index', $data);
    }
}