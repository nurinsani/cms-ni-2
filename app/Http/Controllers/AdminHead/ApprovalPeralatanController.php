<?php

namespace App\Http\Controllers\AdminHead;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class ApprovalPeralatanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Peralatan';
        // $data['pengajuan'] = Pengajuan::where('status_approve', 'admin-head')->get();
        $data['pengajuan'] = Pengajuan::join('branch', 'branch.code_area', 'pengajuans.code_area')
        ->select(['pengajuans.*', 'branch.code_area'])
        ->where('pengajuans.status_approve', 'admin-head')
        ->distinct()
        ->where('branch.code_region', auth()->user()->code_region)->get();
        return view('admin-head.approval.pengajuan-peralatan.index', $data);
    }

    public function detail($id)
    {
        $title = 'Detail';
        $pengajuan = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', 'kode_area')->get();
        return view('admin-head.approval.pengajuan-peralatan.detail', compact('title', 'pengajuan'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'akunting-it';
            $pengajuan->approval_1 = auth()->user()->id;
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
            $pengajuan->status_approve = 'admin';
            $pengajuan->approval_1 = auth()->user()->id;
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
