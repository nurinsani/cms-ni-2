<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalLpjController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval LPJ Peralatan';
        $data['lpj'] = Pengajuan::with(['area', 'pengajuan_details.jenis_peralatan', 'user'])
            ->where('status_approve', 'lpj-admin-head')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin-head.approval.pengajuan-lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ Peralatan';
        $data['lpj'] = Pengajuan::with([
            'area',
            'pengajuan_details.jenis_peralatan.param_peralatan',
            'pengajuan_details.ap',
            'pengajuan_details.area',
            'user'
        ])->findOrFail($id);
        return view('admin-head.approval.pengajuan-lpj.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'lpj-akunting-it';
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
            $pengajuan->status_approve = 'lpj-admin';
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }
}
