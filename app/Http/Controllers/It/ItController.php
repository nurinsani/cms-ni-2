<?php

namespace App\Http\Controllers\It;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ItController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'IT Dashboard';
        $data['belum_approve'] = Pengajuan::where('status_approve', 'akunting-it')->whereHas('pengajuan_details', function($query){
            $query->where('group_approval', 'it');
            })->get();
        $data['total_user'] = \App\Models\User::count();
        $data['total_pengajuan_it'] = Pengajuan::where('user_id', auth()->id())->count();
        // $data['belum_approve_bdd'] = PengajuanBdd::where('status_approve', 'akunting-it')->whereHas('jenis_bdd', function($query){
        //     $query->where('group_approval', 'it');
        //     })->get();
        return view('it.dashboard', $data);
    }

    public function pengajuan_peralatan()
    {
        $data['title'] = 'Pengajuan Peralatan';
        // $data['belum_approve'] = Pengajuan::where('status_proses', 'it')->where('cabang_id', auth()->user()->cabang_id)->get();
        $data['pengajuan'] = Pengajuan::where('status_approve', 'akunting-it')->get();
        return view('it.approval.pengajuan-peralatan.index', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'pengurus';
            $pengajuan->approval_2 = auth()->user()->id;
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
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_proses = 'supervisor';
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }

    public function pengajuan_detail($id)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = Pengajuan::findOrFail($id);
        return view('it.approval.pengajuan-peralatan.detail', $data);
    }

    public function pengajuan_bdd()
    {
        $data['title'] = 'Approval Pengajuan BDD';
        // $data['belum_approve'] = Pengajuan::where('status_proses', 'it')->where('cabang_id', auth()->user()->cabang_id)->get();
        $data['pengajuan_bdd'] = PengajuanBdd::where('status_proses', 'pending')->whereHas('jenis_bdd', function($query){
            $query->where('group_approval', 'it');
            })->get();
        return view('it.approval.pengajuan-bdd', $data);
    }

    public function approve_bdd(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_proses = 'finance';
            $pengajuan->atasan_2 = auth()->user()->id;
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
