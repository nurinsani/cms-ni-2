<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Branch;
use App\Models\HistoryApprove;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class klaimController extends Controller
{
    public function index()
    {
        $data['title'] = 'Klaim Pembelian';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'klaim-finance')
        ->get();
        return view('finance.approval.klaim.index', $data);
    }

    public function detail($id)
    {
        $title = 'Detail Klaim';
        $pengajuan = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', 'kode_area')->get();
        return view('finance.approval.klaim.detail', compact('title', 'pengajuan'));
    }

    public function approve_klaim()
    {   
        $data['title'] = 'Cetak Klaim';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'klaim-selesai')->orWhere('status_approve', 'lpj-atasan-akunting')->get();
        return view('finance.approval.pengajuan-peralatan.index', $data);
    }

    public function detail_cetak($id)
    {
        $data['title'] = 'Detail Cetak Klaim';
        $data['pengajuan'] = $lpj = Pengajuan::findOrFail($id);

        if ($lpj->approval1) {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }
            
            $data['diajukan'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->user->id.'/'.$nomor_urut;
            $data['kode_approve1'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval1->id.'/'.$nomor_urut;
            $data['kode_approve2'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval2->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval4->id.'/'.$nomor_urut;
        } else {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }

            $data['diajukan'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->user->id.'/'.$nomor_urut;
            $data['kode_approve2'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval2->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval4->id.'/'.$nomor_urut;
            // $data['kode_approve5'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval5->id.'/'.$nomor_urut;

        }

        HistoryApprove::updateOrCreate([
            'pengajuan_id' => $lpj->id,
            'user_id' => $lpj->user_id,
            'barcode' => $kode_approve4,
        ]);

        $lpj->update([
            'status_cetak' => 'sudah-cetak-fpd',
        ]);

        return view('finance.approval.pengajuan-peralatan.detail-cetak', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'klaim-finance';
            $pengajuan->approval_4 = auth()->user()->id;
            $pengajuan->save();
            
            
            foreach ($pengajuan->pengajuan_details as $item) {

            } // jurnal klaim untuk cabang

            if ($item->kode_area == true) {
                $keterangan = strtoupper('KLAIM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'WILAYAH' . ' ' . $item->area->area);
            } elseif ($item->kode_branch == '1010') {
                $keterangan = strtoupper('KLAIM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'KANTOR PUSAT');
            } else {
                $keterangan = strtoupper('KLAIM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'AP' . ' ' .  $item->ap->unit);
            }

            $url_api = 'http://192.168.1.11:8080/api/transaksi';

            if ($item->kode_branch == '1010') {
                $kode_transaksi = date('m').random_int(100000, 999999);
                $TransaksiKp = TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-'.$item->jenis_peralatan->param_peralatan->coa,
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => $keterangan,
                    'debet' => $item->harga_pembelian * $item->kuantitas,
                    'kredit' => 0
                ]);

                // $data = $TransaksiKp;

                $data = [
                    'unit' => $TransaksiKp->kode_branch,
                    'kode_transaksi' => $TransaksiKp->kode_transaksi,
                    'kode_rekening' => $TransaksiKp->kode_rekening,
                    'tanggal_transaksi' => $TransaksiKp->tanggal_transaksi->toDateString(),
                    'jenis_transaksi' => $TransaksiKp->jenis_transaksi,
                    'keterangan_transaksi' => $TransaksiKp->keterangan,
                    'debet' => $TransaksiKp->debet,
                    'kredit' => $TransaksiKp->kredit,
                    'tanggal_posting' => now()->toDateString(),
                    'keterangan_posting' => 'Posting otomatis dari sistem Laravel',
                    'id_admin' => auth()->id() ?? 1,
                ];

                
                // dd($data);

                $response = Http::timeout(30)
                    ->withHeaders([
                        'Accept' => 'application/json',
                    ])
                    ->post($url_api, $data);

                if ($response->successful()) {
                    Log::info("Transaksi berhasil", $response->json());
                } else {
                    Log::error("Error API", [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                }


                $TransaksiKp = TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1312000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => $keterangan,
                    'debet' => 0,
                    'kredit' => $item->harga_pembelian * $item->kuantitas,
                ]);

                $data = [
                    'unit' => $TransaksiKp->kode_branch,
                    'kode_transaksi' => $TransaksiKp->kode_transaksi,
                    'kode_rekening' => $TransaksiKp->kode_rekening,
                    'tanggal_transaksi' => $TransaksiKp->tanggal_transaksi->toDateString(),
                    'jenis_transaksi' => $TransaksiKp->jenis_transaksi,
                    'keterangan_transaksi' => $TransaksiKp->keterangan,
                    'debet' => $TransaksiKp->debet,
                    'kredit' => $TransaksiKp->kredit,
                    'tanggal_posting' => now()->toDateString(),
                    'keterangan_posting' => 'Posting otomatis dari sistem Laravel',
                    'id_admin' => auth()->id() ?? 1,
                ];

                $response = Http::timeout(30)
                    ->withHeaders([
                        'Accept' => 'application/json',
                    ])
                    ->post($url_api, $data);

                if ($response->successful()) {
                    Log::info("Transaksi berhasil", $response->json());
                } else {
                    Log::error("Error API", [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                }
            } else {
                if ($item->kode_branch == NULL) {
                    foreach ($pengajuan->pengajuan_details as $key => $cabang) {
                        $subtotal = 0;
                        $total = $cabang->harga * $cabang->kuantitas;
                        $subtotal = $subtotal + $total;
                        // Jurnal LPJ Pembelian Peralatan Cabang
                        $branch = Branch::where('code_area', $cabang->kode_area)
                        ->where('status_aktif', 'aktif')
                        ->get();
                        $total_branch = $branch->count();
    
                        // jurnal LPJ pembelian untuk cabang di KP
                        $kode_transaksi = date('m').random_int(100000, 999999);
                        foreach ($branch as $key => $data) {
                            $TransaksiKp = TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                                'kode_rekening' => '1010'.'-'.$data->GL,
                                'tanggal_transaksi' => now()->format('Y-m-d H:i:s'),
                                'jenis_transaksi' => 'Bukti SYSTEMs',
                                'keterangan' => $keterangan,
                                'debet' => ($cabang->harga_pembelian * $cabang->kuantitas) / $total_branch,
                                'kredit' => 0
                            ]);

                        $data = [
                            'unit' => $TransaksiKp->kode_branch,
                            'kode_transaksi' => $TransaksiKp->kode_transaksi,
                            'kode_rekening' => $TransaksiKp->kode_rekening,
                            'tanggal_transaksi' => $TransaksiKp->tanggal_transaksi->toDateString(),
                            'jenis_transaksi' => $TransaksiKp->jenis_transaksi,
                            'keterangan_transaksi' => $TransaksiKp->keterangan,
                            'debet' => $TransaksiKp->debet,
                            'kredit' => $TransaksiKp->kredit,
                            'tanggal_posting' => now()->toDateString(),
                            'keterangan_posting' => 'Posting otomatis dari sistem Laravel',
                            'id_admin' => auth()->id() ?? 1,
                        ];

                            $response = Http::timeout(30)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($url_api, $data);

                            if ($response->succiessful()) {
                                Log::info("Transaksi berhasil", $response->json());
                            } else {
                                Log::error("Error API", [
                                    'status' => $response->status(),
                                    'response' => $response->json()
                                ]);
                            }
                        }

                        $TransaksiKp = TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-1312000',
                            'tanggal_transaksi' => now()->format('Y-m-d H:i:s'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => 0,
                            'kredit' => $cabang->harga_pembelian * $cabang->kuantitas
                        ]);

                        $data = [
                            'unit' => $TransaksiKp->kode_branch,
                            'kode_transaksi' => $TransaksiKp->kode_transaksi,
                            'kode_rekening' => $TransaksiKp->kode_rekening,
                            'tanggal_transaksi' => $TransaksiKp->tanggal_transaksi->toDateString(),
                            'jenis_transaksi' => $TransaksiKp->jenis_transaksi,
                            'keterangan_transaksi' => $TransaksiKp->keterangan,
                            'debet' => $TransaksiKp->debet,
                            'kredit' => $TransaksiKp->kredit,
                            'tanggal_posting' => now()->toDateString(),
                            'keterangan_posting' => 'Posting otomatis dari sistem Laravel',
                            'id_admin' => auth()->id() ?? 1,
                        ];

                        $response = Http::timeout(30)
                            ->withHeaders([
                                'Accept' => 'application/json',
                            ])
                            ->post($url_api, $data);

                        if ($response->successful()) {
                            Log::info("Transaksi berhasil", $response->json());
                        } else {
                            Log::error("Error API", [
                                'status' => $response->status(),
                                'response' => $response->json()
                            ]);
                        }
                        // end jurnal LPJ pembelian untuk cabang di KP
    
                        // jurnal LPJ   pembelian untuk cabang di AP
                        $kode_transaksi = date('m').random_int(100000, 999999);
                        foreach ($branch as $key => $data) {
                            $TransaksiAp = TransaksiAp::create([
                                'kode_branch' => $data->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                                'kode_rekening' => $cabang->jenis_peralatan->param_peralatan->coa,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $keterangan,
                                'debet' => ($cabang->harga_pembelian * $cabang->kuantitas) / $total_branch,
                                'kredit' => 0
                            ]);

                            $data = $TransaksiAp;

                            $data = [
                    'unit' => $TransaksiKp->kode_branch,
                    'kode_transaksi' => $TransaksiKp->kode_transaksi,
                    'kode_rekening' => $TransaksiKp->kode_rekening,
                    'tanggal_transaksi' => $TransaksiKp->tanggal_transaksi->toDateString(),
                    'jenis_transaksi' => $TransaksiKp->jenis_transaksi,
                    'keterangan_transaksi' => $TransaksiKp->keterangan,
                    'debet' => $TransaksiKp->debet,
                    'kredit' => $TransaksiKp->kredit,
                    'tanggal_posting' => now()->toDateString(),
                    'keterangan_posting' => 'Posting otomatis dari sistem Laravel',
                    'id_admin' => auth()->id() ?? 1,
                ];

                            $response = Http::timeout(30)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($url_api, $data);

                            if ($response->successful()) {
                                Log::info("Transaksi berhasil", $response->json());
                            } else {
                                Log::error("Error API", [
                                    'status' => $response->status(),
                                    'response' => $response->json()
                                ]);
                            }
    
                            $TransaksiAp = TransaksiAp::create([
                                'kode_branch' => $data->kode_branch,
                                'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                                'kode_rekening' => '2500000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $keterangan,
                                'debet' => 0,
                                'kredit' => ($cabang->harga_pembelian * $cabang->kuantitas) / $total_branch
                            ]);

                            $data = $TransaksiAp;

                            if (isset($data['kode_branch'])) {
                                $data['unit'] = $data['kode_branch'];
                                unset($data['kode_branch']);
                            }

                            $response = Http::timeout(30)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($url_api, $data);

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
                } else {
                    foreach ($pengajuan->pengajuan_details as $item) {
                        $kode_transaksi = date('m').random_int(100000, 999999);
                        $TransaksiAp = TransaksiAp::create([
                            'kode_branch' => $item->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $item->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => $item->harga_pembelian * $item->kuantitas,
                            'kredit' => 0
                        ]);

                        $data = $TransaksiAp;

                        if (isset($data['kode_branch'])) {
                            $data['unit'] = $data['kode_branch'];
                            unset($data['kode_branch']);
                        }

                        $response = Http::timeout(30)
                            ->withHeaders([
                                'Accept' => 'application/json',
                            ])
                            ->post($url_api, $data);

                        if ($response->successful()) {
                            Log::info("Transaksi berhasil", $response->json());
                        } else {
                            Log::error("Error API", [
                                'status' => $response->status(),
                                'response' => $response->json()
                            ]);
                        }
        
                        $TransaksiAp = TransaksiAp::create([
                            'kode_branch' => $item->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => 0,
                            'kredit' => $item->harga_pembelian * $item->kuantitas,
                        ]);

                        $data = $TransaksiAp;

                        if (isset($data['kode_branch'])) {
                            $data['unit'] = $data['kode_branch'];
                            unset($data['kode_branch']);
                        }

                        $response = Http::timeout(30)
                            ->withHeaders([
                                'Accept' => 'application/json',
                            ])
                            ->post($url_api, $data);

                        if ($response->successful()) {
                            Log::info("Transaksi berhasil", $response->json());
                        } else {
                            Log::error("Error API", [
                                'status' => $response->status(),
                                'response' => $response->json()
                            ]);
                        }
        
                        $TransaksiKp = TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010'.'-'.$item->ap->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => $item->harga_pembelian * $item->kuantitas,
                            'kredit' => 0
                        ]);
                        
                        $data = $TransaksiKp;

                        if (isset($data['kode_branch'])) {
                            $data['unit'] = $data['kode_branch'];
                            unset($data['kode_branch']);
                        }

                        $response = Http::timeout(30)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($url_api, $data);

                            if ($response->successful()) {
                                Log::info("Transaksi berhasil", $response->json());
                            } else {
                                Log::error("Error API", [
                                    'status' => $response->status(),
                                    'response' => $response->json()
                                ]);
                            }

                        $TransaksiKp = TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-1312000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $keterangan,
                            'debet' => 0,
                            'kredit' => $item->harga_pembelian * $item->kuantitas
                        ]);

                        $data = $TransaksiKp;

                            if (isset($data['kode_branch'])) {
                                $data['unit'] = $data['kode_branch'];
                                unset($data['kode_branch']);
                            }

                        $response = Http::timeout(30)
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                ])
                                ->post($url_api, $data);

                            if ($response->successful()) {
                                Log::info("Transaksi berhasil", $response->json());
                            } else {
                                Log::error("Error API", [
                                    'status' => $response->status(),
                                    'response' => $response->json()
                                ]);
                            }
                    } // jurnal klaim untuk cabang
                }
            }

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
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'klaim-pengurus';
            $pengajuan->approval_4 = auth()->user()->id;
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil ditolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }
}