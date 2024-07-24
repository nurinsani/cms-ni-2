<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class klaimController extends Controller
{
    public function index()
    {
        $data['title'] = 'Klaim Pembelian';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'klaim-admin-head')
        ->get();
        return view('admin-head.approval.klaim.index', $data);
    }

    public function detail($id)
    {
        $title = 'Detail';
        $pengajuan = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', 'kode_area')->get();
        return view('admin-head.approval.klaim.detail', compact('title', 'pengajuan'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'klaim-akunting-it';
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
            $pengajuan->status_approve = 'tolak';
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
