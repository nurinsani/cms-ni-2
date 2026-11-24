<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Admin\TempPengajuanDetail;
use App\Models\Akunting\JenisPeralatan;
use App\Models\Akunting\ParamPeralatan;
use App\Models\Branch;
use App\Models\Rekening;
use App\Models\ParamJabatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminKlaimController extends Controller
{
    public function index()
    {
        $data['title'] = 'Klaim Pembelian';
        $codeArea = auth()->user()->code_area;
        $query = Pengajuan::where('status_approve', 'like', 'klaim%');
        if (!empty($codeArea)) {
            $query->where('code_area', $codeArea);
        }
        $data['pengajuan'] = $query->latest('id')->get();
        return view('admin.klaim-pembelian.index', $data);
    }

    public function klaim_peralatan()
    {
        $data['title'] = 'Tambah Klaim Peralatan';
        $data['temp_pengajuan'] = TempPengajuanDetail::with(['jenis_peralatan.param_peralatan', 'ap', 'area'])
            ->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })
            ->get();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('param_peralatan', function($query) {
            $query->where('group_inventaris', 'tidak');
        })->get();
        $codeArea = auth()->user()->code_area;

        $branchQuery = Branch::whereNotNull('code_area')
            ->where('code_area', '!=', '');
        if (!empty($codeArea)) {
            $branchQuery->where('code_area', $codeArea);
        }
        $data['branch'] = $branchQuery->orderBy('unit', 'asc')->get();

        $areaQuery = Branch::whereNotNull('code_area')
            ->where('code_area', '!=', '')
            ->where('area', '!=', '')
            ->where('area', '!=', '0000');
        if (!empty($codeArea)) {
            $areaQuery->where('code_area', $codeArea);
        }
        $data['area'] = $areaQuery->select('code_area', 'area')
            ->distinct()
            ->orderBy('area', 'asc')
            ->get();
        return view('admin.klaim-pembelian.klaim-peralatan', $data);
    }

    public function klaim_inventaris()
    {
        $data['title'] = 'Tambah Klaim Inventaris';
        $data['temp_pengajuan'] = TempPengajuanDetail::with(['jenis_peralatan.param_peralatan', 'ap', 'area'])
            ->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })
            ->get();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('param_peralatan', function($query) {
            $query->where('group_inventaris', 'ya');
        })->get();
        $data['param_jabatan'] = ParamJabatan::aktif()
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jabatan', 'asc')
            ->get();

        $codeArea = auth()->user()->code_area;

        $branchQuery = Branch::whereNotNull('code_area')
            ->where('code_area', '!=', '');
        if (!empty($codeArea)) {
            $branchQuery->where('code_area', $codeArea);
        }
        $data['branch'] = $branchQuery->orderBy('unit', 'asc')->get();

        $areaQuery = Branch::whereNotNull('code_area')
            ->where('code_area', '!=', '')
            ->where('area', '!=', '')
            ->where('area', '!=', '0000');
        if (!empty($codeArea)) {
            $areaQuery->where('code_area', $codeArea);
        }
        $data['area'] = $areaQuery->select('code_area', 'area')
            ->distinct()
            ->orderBy('area', 'asc')
            ->get();
        return view('admin.klaim-pembelian.klaim-inventaris', $data);
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
        $temp_pengajuan = TempPengajuanDetail::where('user_id', auth()->user()->id)->get();
        $data['rekening_id'] = $request->rekening_id ?? $temp_pengajuan->first()?->rekening_id;
        if (auth()->user()->code_area == 1010) {
            $data['status_approve'] = 'klaim-akunting-it';
        } else {
            $data['status_approve'] = 'klaim-admin-head';
        }
        
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
            $pengajuan_detail->moa = $item->moa;
            $pengajuan_detail->umur = $item->umur;
            $pengajuan_detail->keterangan = $request->keterangan;
            $pengajuan_detail->status_branch = $request->status_branch;
            $pengajuan_detail->save();
        }
        
        TempPengajuanDetail::where('user_id', auth()->user()->id)->delete();
        
        return redirect('admin/klaim-pembelian')->with('success', 'Data berhasil dibuat');
    }

    public function detail($id)
    {
        $title = 'Detail';
        $klaim = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.klaim-pembelian.detail', compact('title', 'klaim'));
    }
}