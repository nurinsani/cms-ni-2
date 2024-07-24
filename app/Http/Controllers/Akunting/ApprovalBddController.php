<?php

namespace App\Http\Controllers\Akunting;

use App\Models\DaftarBdd;
use Illuminate\Http\Request;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ApprovalBddController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan BDD';
        $data['pengajuan_bdd'] = PengajuanBdd::where('status_approve', 'akunting-it')->whereHas('jenis_bdd', function($query){
        $query->where('group_approval', 'umum');
        })->get();
        return view('akunting.approval.pengajuan-bdd.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('akunting.approval.pengajuan-bdd.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_approve = 'pengurus';
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

    public function approve_lpj_bdd(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanBdd::findOrfail($id);
            $pengajuan->status_proses = 'selesai';

            $approve = PengajuanBdd::where('status_proses', 'selesai')->get();

            DaftarBdd::create([
                'pengajuan_bdd_id' => $pengajuan->id,
                'user_id' => auth()->user()->id,
                'kode_ap' => $pengajuan->kode_branch,
                'tanggal_awal' => Carbon::now(),
                'jangka_waktu' => $pengajuan->jangka_waktu_amortisasi,
                'status_amortisasi' => 1
            ]);

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
