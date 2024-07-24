<?php

namespace App\Http\Controllers\Admin;

use App\Traits\FileUpload;
use Illuminate\Http\Request;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class LPjBddController extends Controller
{
    use FileUpload;
    public $validator = [
        'perjanjian' => 'required',
        'kwitansi' => 'required',
    ];
    
    public function index()
    {
        $data['title'] = 'LPJ BDD';
        $data['lpj_bdd'] = PengajuanBdd::where('status_proses', 'lpj')->get();
        return view('admin.lpj-bdd.index', $data);
    }

    public function show($id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('admin.lpj-bdd.detail', $data);
    }

    public function edit($id)
    {
        $data['title'] = 'Buat LPJ BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('admin.lpj-bdd.create', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan_bdd = PengajuanBdd::Find($id);
            $pengajuan_bdd->status_proses = 'lpj_finance';
            
            if ($request->hasFile('perjanjian')) {
                $pengajuan_bdd->perjanjian = $this->upload_file($request->file('perjanjian'), 'pengajuan/bdd/perjanjian');
            }

            if ($request->hasFile('kwitansi')) {
                $pengajuan_bdd->kwitansi = $this->upload_file($request->file('kwitansi'), 'pengajuan/bdd/kwitansi');
            }
            
            $pengajuan_bdd->save();
            DB::commit();
            return redirect()->route('admin.lpj-bdd.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }
}
