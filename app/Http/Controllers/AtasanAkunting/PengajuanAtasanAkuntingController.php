<?php

namespace App\Http\Controllers\AtasanAkunting;

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

class PengajuanAtasanAkuntingController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Pembelian';
        $data['pengajuan'] = Pengajuan::where('code_area', auth()->user()->code_area)->get();
        return view('atasan-akunting.pengajuan.index', $data);
    }

    public function pengajuan_peralatan()
    {
        $data['title'] = 'Pengajuan Peralatan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('jenis_bdd', function($query) {
            $query->where('group_inventaris', 'tidak');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('atasan-akunting.pengajuan.tambah-peralatan', $data);
    }

    public function pengajuan_inventaris()
    {
        $data['title'] = 'Pengajuan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('jenis_bdd', function($query) {
            $query->where('group_inventaris', 'ya');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('atasan-akunting.pengajuan.tambah-inventaris', $data);
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
        if (auth()->user()->code_area == 1010) {
            $data['status_approve'] = 'pengurus';
        } else {
            $data['status_approve'] = 'akunting-it';

        }
        
        $temp_pengajuan = TempPengajuanDetail::all();
        $pengajuan = Pengajuan::create($data);
        
        foreach ($temp_pengajuan as $item) {
            $pengajuan_detail = New PengajuanDetail;
            $pengajuan_detail->pengajuan_id = $pengajuan->id;
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
            $pengajuan_detail->save();
        }
        
        TempPengajuanDetail::query()->delete();
        return redirect('atasan-akunting/pengajuan')->with('success', 'Data berhasil dibuat');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = Pengajuan::find($id);
        return view('atasan-akunting.pengajuan.detail', $data);
    }
}
