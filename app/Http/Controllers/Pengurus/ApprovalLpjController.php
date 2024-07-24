<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalLpjController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'lpj-pengurus')->get();
        return view('pengurus.approval.pengajuan-lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        return view('pengurus.approval.pengajuan-lpj.detail', $data);
    }   

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'lpj-finance';
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
}
