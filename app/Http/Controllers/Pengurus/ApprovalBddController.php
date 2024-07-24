<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalBddController extends Controller
{
    
    public function index()
    {
        $data['title'] = 'Pengajuan BDD';
        // $data['belum_approve'] = Pengajuan::where('status_uji', 'it')->where('cabang_id', auth()->user()->cabang_id)->get();
        $data['pengajuan_bdd'] = PengajuanBdd::where('status_approve', 'pengurus')->get();
        return view('pengurus.approval.pengajuan-bdd.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('pengurus.approval.pengajuan-bdd.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_approve = 'finance';
            $pengajuan->atasan_4 = auth()->user()->id;
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
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_approve = 'akunting-it';
            $pengajuan->atasan_4 = auth()->user()->id;
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
