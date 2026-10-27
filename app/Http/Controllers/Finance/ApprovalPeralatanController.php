<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\HistoryApprove;
use App\Models\TransaksiKp;
use Illuminate\Support\Facades\Http;

class ApprovalPeralatanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Peralatan';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'finance')->orWhere('status_approve', 'kp-finance')->get();
        return view('finance.approval.pengajuan-peralatan.index', $data);
    }

    public function detail($id)
    {
        $title = 'Detail Pengajuan';
        $pengajuan = Pengajuan::findOrFail($id);
        return view('finance.approval.pengajuan-peralatan.detail', compact('pengajuan', 'title'));
    }

    public function detail_cetak($id)
    {
        $data['title'] = 'Cetak Detail Pengajuan';
        $data['pengajuan'] = $pengajuan = Pengajuan::findOrFail($id);

        $history_approve = HistoryApprove::latest()->first();
        if ($history_approve == null || empty($history_approve->barcode)) {
            $nomor_urut = '0001';
        } else {
            $barcodeParts = explode('/', $history_approve->barcode);
            $lastPart = end($barcodeParts);
            $lastNumber = is_numeric($lastPart) ? (int) $lastPart : 0;
            $nomor_urut = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
        }

        if ($pengajuan->approval1) {
            $data['diajukan'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->user->id.'/'.$nomor_urut;
            $data['kode_approve1'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval1->id.'/'.$nomor_urut;
            $data['kode_approve2'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval2->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval4->id.'/'.$nomor_urut;
        } else {
            $data['diajukan'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->user->id.'/'.$nomor_urut;
            $data['kode_approve2'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval2->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.$pengajuan->approval4->id.'/'.$nomor_urut;
            $data['kode_approve6'] = $pengajuan->id.'/'.$pengajuan->updated_at->format('Y-m-d-Hid').'/'.(optional($pengajuan->approval6)->id ?? 0) .'/'.$nomor_urut;
        }

        HistoryApprove::updateOrCreate([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $pengajuan->user_id,
            'barcode' => $kode_approve4,
        ]);

        $pengajuan->update([
            'status_cetak' => 'sudah-cetak-fpd',
        ]);

        // dd($data);

        return view('finance.approval.pengajuan-peralatan.detail-cetak', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'lpj-admin';
            $pengajuan->approval_4 = auth()->user()->id;
            $pengajuan->save();

            $uniqueKodeArea = $pengajuan->pengajuan_details->pluck('kode_branch')->unique()->count();


            foreach ($pengajuan->pengajuan_details as $item) {
                if ($uniqueKodeArea == 1) {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'WILAYAH' . ' ' . $pengajuan->area->area);
                } elseif ($item->kode_branch == '1010') {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'KANTOR PUSAT');
                } elseif ($uniqueKodeArea > 1 ) {
                    $keterangan = strtoupper('UM PEMBELIAN' . ' ' . $item->jenis_peralatan->nama_jenis . ' ' . 'AP' . ' ' . $item->ap->unit);
                }
                
                // $url_api = 'http://127.0.0.1:8000/api/transaksi'; // URL API lokal
                // $url_api = 'http://36.64.7.130:7117/nurinsani/api/transaksi_cms.php?function=insert_transaksi'; // URL API test
                // $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi';// URL API production
                
                $url_api = 'http://192.168.88.4/nurinsani/api/transaksi_cms.php?function=insert_transaksi'; // URL API production
    
    
                $kode_transaksi = date('m').random_int(100000, 999999);
                $transaksiKp = TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1929000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => $keterangan,
                    'debet' => $item->harga * $item->kuantitas,
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
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1312000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => $keterangan,
                    'debet' => 0,
                    'kredit' => $item->harga * $item->kuantitas,
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
            $pengajuan->status_approve = 'pengurus';
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

    public function approve_peralatan()
    {   
        // TODO belum hilang saat approve
        $data['title'] = 'Cetak FPD';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'lpj-admin')->orWhere('status_approve', 'lpj-atasan-akunting')->get();
        return view('finance.approval.pengajuan-peralatan.index', $data);
    }
}
