<?php

namespace App\Http\Controllers\AtasanIt;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Peralatan';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'atasan-it')->get();
        return view('atasan-it.approval.pengajuan-peralatan.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = Pengajuan::findOrFail($id);
        return view('atasan-it.approval.pengajuan-peralatan.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'akunting-it';
            $pengajuan->approval_6 = auth()->user()->id;
            $pengajuan->save();

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
