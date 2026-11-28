<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ApprovalPeralatanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval Peralatan';
        $data['pengajuan'] = Pengajuan::with(['area', 'pengajuan_details.jenis_peralatan.param_peralatan', 'pengajuan_details.ap', 'pengajuan_details.area', 'user'])
            ->where('status_approve', 'akunting-it')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('akunting.approval.pengajuan-peralatan.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Peralatan';
        $data['pengajuan'] = Pengajuan::with(['area', 'pengajuan_details.jenis_peralatan.param_peralatan', 'pengajuan_details.ap', 'pengajuan_details.area', 'user'])
            ->findOrFail($id);
        return view('akunting.approval.pengajuan-peralatan.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'pengurus';
            $pengajuan->approval_2 = auth()->user()->id;
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diapprove');
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
            $pengajuan->status_approve = 'admin-head';
            $pengajuan->approval_2 = auth()->user()->id;
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
