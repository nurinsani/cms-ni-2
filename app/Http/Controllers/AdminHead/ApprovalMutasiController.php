<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Admin\Peralatan;
use App\Models\PengajuanMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Mutasi Inventaris Gol 1';
        $data['pengajuan_mutasi'] = PengajuanMutasi::with(['dari_ap', 'branch', 'inventaris'])
            ->where('status_approve', 'admin-head')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin-head.approval.pengajuan-mutasi.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi Inventaris Gol 1';
        $pengajuan = PengajuanMutasi::with(['inventaris.penyusutans', 'dari_ap', 'branch'])->findOrFail($id);
        return view('admin-head.approval.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $mutasi = PengajuanMutasi::findOrfail($id);
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
            $mutasi = PengajuanMutasi::findOrfail($id);
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
