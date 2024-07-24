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
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItKlaimController extends Controller
{
    public function index()
    {
        $data['title'] = 'Klaim Pembelian';
        $data['pengajuan'] = Pengajuan::where('code_area', 1010)
        ->where('status_approve', 'klaim-akunting-it')
        ->get();
        return view('it.klaim.index', $data);
    }

    public function klaim_peralatan()
    {
        $data['title'] = 'Pengajuan Peralatan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('jenis_bdd', function($query) {
            $query->where('group_inventaris', 'tidak');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('it.klaim.tambah-klaim', $data);
    }   

    public function klaim_store(Request $request)
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
        if (auth()->user()->code_area == 1010) {
            $data['status_approve'] = 'klaim-akunting-it';
        } else {
            $data['status_approve'] = 'klaim-admin-head';
        }
        
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
            $pengajuan_detail->harga_pembelian = $item->harga;
            $pengajuan_detail->tanggal_perolehan = $item->tanggal_pembelian;
            $pengajuan_detail->struk_pembelian = $item->struk_pembelian;
            $pengajuan_detail->save();
        }
        
        TempPengajuanDetail::query()->delete();
        return redirect('it/klaim')->with('success', 'Data berhasil dibuat');
    }

    public function detail($id)
    {
        $title = 'Detail';
        $klaim = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', 'kode_area')->get();
        return view('admin.klaim-pembelian.detail', compact('title', 'klaim'));
    }
}
