<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Akunting\TransaksiBdd;

class ApprovalBddController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan BDD';
        $data['pengajuan_bdd'] = PengajuanBdd::where('status_approve', 'finance')->get();
        return view('finance.approval.pengajuan-bdd.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('finance.approval.pengajuan-bdd.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_approve = 'selesai';
            $pengajuan->atasan_3 = auth()->user()->id;

            $approve = PengajuanBdd::where('status_approve', 'selesai')->get();

            // jurnal AP
            TransaksiBdd::create([
                'kode_branch' => $pengajuan->branch->kode_branch,
                'kode_transaksi' => Str::uuid(),
                'kode_rekening' => '1941000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'BDD-Sewa',
                'debet' => $pengajuan->harga_sewa + ($pengajuan->harga_sewa / 9),
                'kredit' => 0
            ]);

            TransaksiBdd::create([
                'kode_branch' => $pengajuan->branch->kode_branch,
                'kode_transaksi' => Str::uuid(),
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'RAK PASIVA - KP',
                'debet' => 0,
                'kredit' => $pengajuan->harga_sewa + ($pengajuan->harga_sewa / 9),
            ]);

            foreach ($approve as $key => $item) {
                if ($item->status_approve == 'selesai') {
                    # code...
                }
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
