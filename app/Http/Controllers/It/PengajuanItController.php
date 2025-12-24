<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Admin\TempPengajuanDetail;
use App\Models\Akunting\JenisPeralatan;
use App\Models\Akunting\ParamPeralatan;
use App\Models\Branch;
use App\Models\Rekening;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanItController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Pembelian';
        $data['pengajuan'] = Pengajuan::where('code_area', auth()->user()->code_area)
        ->where('user_id', 2)
        ->get();

        return view('it.pengajuan-peralatan.index', $data);
    }

    public function pengajuan_peralatan()
    {
        $data['title'] = 'Pengajuan Peralatan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('param_peralatan', function($query) {
            $query->where('group_inventaris', 'tidak');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('it.pengajuan-peralatan.tambah-peralatan', $data);
    }

    public function pengajuan_inventaris()
    {
        $data['title'] = 'Pengajuan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('param_peralatan', function($query) {
            $query->where('group_inventaris', 'ya');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('it.pengajuan-peralatan.tambah-inventaris', $data);
    }

    public function store(Request $request)
    {
        $pengajuan = Pengajuan::latest()->first();
        $kode_branch = auth()->user()->code_area;
        $kode_tahun = date('Y');
        $kode_bulan = date('m');
        $kode_tanggal = date('d');
        // dd($kode_tanggal);

        if ($pengajuan == null) {
            $nomor_urut = '0001';
        } else {
            $nomor_urut = substr($pengajuan->nomor, 16, 4) + 1;
            // $nomor_urut = strval(substr($pengajuan->nomor, 18, 4) + 1);
            // $nomor_urut = substr($pengajuan->nomor, 18, 4) + 1;
            $nomor_urut = "000". $nomor_urut;
            // $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
        }

        $data['nomor'] = $kode_branch.'/'.$kode_tanggal.$kode_bulan.$kode_tahun.'/'.$nomor_urut;
        $data['tanggal_pengajuan'] = Carbon::now(); 
        $data['user_id'] = auth()->user()->id;
        $data['code_area'] = auth()->user()->code_area;
        $data['rekening_id'] = $request->rekening_id;
        $data['status_approve'] = 'atasan-it';
        $data['approval_1'] = auth()->user()->id;
        
        $temp_pengajuan = TempPengajuanDetail::all();
        $pengajuan = Pengajuan::create($data);
        
        foreach ($temp_pengajuan as $item) {
            $pengajuan_detail = New PengajuanDetail;
            $pengajuan_detail->pengajuan_id = $pengajuan->id;
            $pengajuan_detail->rekening_id = $item->rekening_id;
            $pengajuan_detail->kode_branch = $item->kode_branch;
            $pengajuan_detail->kode_area = $item->kode_area;
            $pengajuan_detail->jenis_peralatan_id = $item->jenis_peralatan_id;
            $pengajuan_detail->merk = $item->merk;
            $pengajuan_detail->type = $item->type;
            $pengajuan_detail->kuantitas = $item->kuantitas;
            $pengajuan_detail->harga = $item->harga;
            $pengajuan_detail->nama_toko = $item->nama_toko;
            $pengajuan_detail->no_telp = $item->no_telp;
            $pengajuan_detail->pengguna = $item->pengguna;
            $pengajuan_detail->status_lpj = $item->status_lpj;
            $pengajuan_detail->moa = $item->moa;
            $pengajuan_detail->group_approval = $item->group_approval;
            $pengajuan_detail->keterangan = $item->keterangan;
            $pengajuan_detail->save();
        }
        
        TempPengajuanDetail::query()->delete();
        return redirect()->route('it.pengajuan.index')->with('success', 'Data berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function show(Pengajuan $pengajuan)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = $pengajuan;
        return view('it.pengajuan-peralatan.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pengajuan';
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        $data['param_peralatan'] = ParamPeralatan::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['pengajuan'] = Pengajuan::findOrFail($id);
        return view('admin.pengajuan-peralatan.edit', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrFail($id);
            $pengajuan->delete();
            DB::commit();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function daftar_peralatan()
    {
        $data['title'] = 'Daftar Peralatan';
        $data['daftar_peralatan'] = Pengajuan::where('status_proses', 'selesai')->get();
        return view('admin.peralatan.index', $data);
    }

    public function pdf($id)
    {
        $data['title'] = 'Cetak PDF';
        $data['pengajuan'] = Pengajuan::find($id);

        // $data = PDF::loadview('admin.pengajuan-peralatan.export-pdf', $data);

    }
}
