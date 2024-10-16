<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\PengajuanMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['mutasi'] = PengajuanMutasi::where('status_approve', 'mutasi-akunting')->get();
        return view('akunting.approval.pengajuan-mutasi.index', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $mutasi = PengajuanMutasi::findOrfail($id);
            $mutasi->status_approve = 'mutasi-selesai';
            $mutasi->approve_akunting = auth()->user()->id;
            $mutasi->save();

            $pengajuan = Inventaris::findOrfail($mutasi->peralatan_id);
            $pengajuan->kode_branch = $mutasi->ke_cabang;
            $pengajuan->update();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }
    }
}
