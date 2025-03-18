<?php

namespace App\Http\Controllers\Finance;

use Carbon\Carbon;
use App\Models\Admin\Lpj;
use Illuminate\Support\Str;
use App\Models\Akunting\TransaksiBdd;
use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;
use App\Models\Akunting\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanDetail;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Finance Dashboard';
        $data['approval_um'] = Pengajuan::where('status_approve', 'finance');
        $data['approval_lpj'] = Pengajuan::where('status_approve', 'lpj-finance');
        return view('finance.dashboard', $data);
    }

    public function bukti_lpj_detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['bukti_lpj_detail'] = PengajuanDetail::findOrFail($id);
        return view('finance.approval.lpj.peralatan.bukti-lpj-detail', $data);
    }

    public function tolak_bdd(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_proses = 'akunting';
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }

    public function pengajuan_lpj_bdd()
    {
        $data['title'] = 'Pengajuan LPJ BDD';
        $data['lpj_bdd'] = PengajuanBdd::where('status_proses', 'lpj_finance')->where('code_area', auth()->user()->code_area)->get();
        return view('finance.approval.pengajuan-lpj-bdd', $data);
    }

    public function approve_lpj_bdd(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_proses = 'lpj_akunting';

            $approve_lpj = PengajuanBdd::where('status_proses', 'lpj_akunting')->get();

            foreach ($approve_lpj as $item) {
                TransaksiBdd::create([
                    'unit' => $item->branch->kode_branch,
                    'kode_transaksi' => Str::uuid(),
                    'kode_rekening' => '1010-1936000',
                    'tanggal_transaksi' => Carbon::now(),
                    // 'jenis_transaksi' => $item->keterangan,
                    'debet' => $item->harga_pertanggungan,
                    'kredit' => 0
                ]);

                TransaksiBdd::create([
                    'unit' => $item->branch->kode_branch,
                    'kode_transaksi' => Str::uuid(),
                    'kode_rekening' => '1010-1311000',
                    'tanggal_transaksi' => Carbon::now(),
                    // 'jenis_transaksi' => $item->keterangan,
                    'debet' => 0,
                    'kredit' => $item->harga_pertanggungan
                ]);
            }

            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }

    }
}
