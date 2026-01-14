<?php

namespace App\Http\Controllers;

use App\Models\PengajuanMutasiGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiGol2Controller extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['pengajuan_mutasi'] = PengajuanMutasiGol2::where('status_approve', 'admin-head')->get();
        return view('admin-head.approval.pengajuan-mutasi-gol-2.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasiGol2::with('inventaris_gol_2')->findOrFail($id);
        return view('admin-head.approval.pengajuan-mutasi-gol-2.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $mutasi = PengajuanMutasiGol2::findOrfail($id);
            $mutasi->status_approve = 'akunting-it';
            $mutasi->atasan_1 = auth()->user()->id;
            $mutasi->update();

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
            $mutasi = PengajuanMutasiGol2::findOrfail($id);
            $mutasi->status_approve = 'admin';
            $mutasi->atasan_1 = auth()->user()->id;
            $mutasi->update();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil Ditolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }
}
