<?php

namespace App\Http\Controllers\AdminHead;

use Illuminate\Http\Request;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApprovalBddController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan BDD';
        $data['pengajuan_bdd'] = PengajuanBdd::where('status_approve', 'admin-head')->get();
        return view('admin-head.pengajuan-bdd.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::findOrFail($id);
        return view('admin-head.pengajuan-bdd.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan_bdd = PengajuanBdd::findOrfail($id);
            $pengajuan_bdd->status_approve = 'akunting-it';
            $pengajuan_bdd->atasan_1 = auth()->user()->id;
            $pengajuan_bdd->save();
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
            $pengajuan_bdd = PengajuanBdd::findOrfail($id);
            $pengajuan_bdd->status_approve = 'admin';
            $pengajuan_bdd->atasan_1 = auth()->user()->id;
            $pengajuan_bdd->save();
            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }
    }
}
