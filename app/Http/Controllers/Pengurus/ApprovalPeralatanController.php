<?php

namespace App\Http\Controllers\Pengurus;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApprovalPeralatanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Peralatan';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'pengurus')->get();
        return view('pengurus.approval.pengajuan-peralatan.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = Pengajuan::findOrFail($id);
        return view('pengurus.approval.pengajuan-peralatan.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'finance';
            $pengajuan->approval_3 = auth()->user()->id;
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
            $pengajuan->status_approve = 'akunting-it';
            $pengajuan->approval_3 = auth()->user()->id;
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
