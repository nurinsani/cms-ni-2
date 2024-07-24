<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;
use App\Models\Admin\TempPengajuanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TempPengajuanDetailController extends Controller
{
    public function index()
    {
        
    }
    
    public function store(Request $request)
    {
        
        // dd($request->all());
        $temp_pengajuan_detail = New TempPengajuanDetail;
        $temp_pengajuan_detail->jenis_peralatan_id = $request->jenis_peralatan_id;
        $temp_pengajuan_detail->rekening_id = $request->rekening_id;
        $temp_pengajuan_detail->kode_branch = $request->kode_branch;
        $temp_pengajuan_detail->kode_area = $request->kode_area;
        $temp_pengajuan_detail->kode_user = $request->kode_user;
        $temp_pengajuan_detail->merk = $request->merk;
        $temp_pengajuan_detail->type = $request->type;
        $temp_pengajuan_detail->kuantitas = $request->kuantitas;
        $temp_pengajuan_detail->harga = $request->harga;
        $temp_pengajuan_detail->nama_toko = $request->nama_toko;
        $temp_pengajuan_detail->no_telp = $request->no_telp;
        $temp_pengajuan_detail->pengguna = $request->pengguna;
        $temp_pengajuan_detail->moa = $request->moa;
        $temp_pengajuan_detail->tanggal_pembelian = $request->tanggal_pembelian;
        $temp_pengajuan_detail->struk_pembelian = $request->struk_pembelian;
        $temp_pengajuan_detail->keterangan = $request->keterangan;
        $temp_pengajuan_detail->status_lpj = 'belum';

        if ($request->has('moa')) {
            $file = $request->moa;
            $namafile = time().'.'.$file->getClientOriginalExtension();
            $file->move('files/moa', $namafile);
            $temp_pengajuan_detail->moa = $namafile;
        }

        if ($request->has('struk_pembelian')) {
            $file = $request->struk_pembelian;
            $namafile = time().'.'.$file->getClientOriginalExtension();
            $file->move('files/struk_pembelian', $namafile);
            $temp_pengajuan_detail->struk_pembelian = $namafile;
        }


        $temp_pengajuan_detail->save();
        return redirect()->back()->with('success', 'Data berhasil dibuat');

        try {
            DB::beginTransaction();


            // DB::commit();


        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal disimpan');
        }
    }

    public function hapus($id)
    {
        $temp_pengajuan_detail = TempPengajuanDetail::find($id);
        $temp_pengajuan_detail->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
