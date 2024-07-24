<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\DaftarBdd;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Akunting\JenisPeralatan;
use Illuminate\Support\Facades\Validator;

class PengajuanBddController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Pengajuan BDD';
        $data['bdd'] = PengajuanBdd::all();
        $data['lpj_bdd'] = PengajuanBdd::where('status_proses', 'lpj');
        return view('admin.pengajuan-bdd.index', $data);
    }

    public function lpj()
    {
        $data['title'] = 'Pengajuan LPJ BDD';
        $data['bdd'] = PengajuanBdd::all();
        $data['lpj_bdd'] = PengajuanBdd::where('status_proses', 'lpj')->get();
        return view('admin.lpj-bdd.index', $data);
    }

    public function pengajuan_sewa()
    {
        $data['title'] = 'Buat Pengajuan BDD Sewa';
        $data['sewa'] = JenisPeralatan::where('group_bdd', 'sewa')->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-bdd.form-sewa', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pengajuan_asuransi()
    {
        $data['title'] = 'Buat Pengajuan BDD Asuransi';
        $data['jenis'] = JenisPeralatan::where('group_bdd', 'asuransi')->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-bdd.form-asuransi', $data);
    }

    public function store_sewa(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'jenis_bdd_id' => 'required',
            'kode_branch' => 'required',
            'lokasi' => 'required',
            'jenis_sewa' => 'required',
            'type_bangunan' => 'required',
            'nama_pemilik' => 'required',
            'status_kepemilikan' => 'required',
            'no_langganan_listrik' => 'required',
            'no_langganan_pam' => 'required',
            'no_langganan_telp' => 'required',
            'harga_sewa' => 'required',
            'no_rek_bank_pemilik' => 'required',
            'atas_nama' => 'required',
            'no_hp_pemilik' => 'required',
            'jangka_waktu_amortisasi' => 'required',
            'no_shm_hgb' => 'required',
        ]);
        
        $pengajuan_bdd = New PengajuanBdd();
        $pengajuan_bdd->jenis_bdd_id = $request->jenis_bdd_id;
        $pengajuan_bdd->kode_branch = $request->kode_branch;
        
        $pengajuan_bdd->lokasi = $request->lokasi;
        $pengajuan_bdd->jenis_sewa = $request->jenis_sewa;
        $pengajuan_bdd->type_bangunan = $request->type_bangunan;
        $pengajuan_bdd->nama_pemilik = $request->nama_pemilik;
        $pengajuan_bdd->status_kepemilikan = $request->status_kepemilikan;
        $pengajuan_bdd->no_langganan_listrik = $request->no_langganan_listrik;
        $pengajuan_bdd->no_langganan_pam = $request->no_langganan_pam;
        $pengajuan_bdd->no_langganan_telp = $request->no_langganan_telp;
        $pengajuan_bdd->harga_sewa = $request->harga_sewa;
        // $pengajuan_bdd->pajak_ps = $request->pajak_ps;
        // $pengajuan_bdd->harga_sewa_net = $request->harga_sewa_net;
        $pengajuan_bdd->no_rek_bank_pemilik = $request->no_rek_bank_pemilik;
        $pengajuan_bdd->atas_nama = $request->atas_nama;
        $pengajuan_bdd->no_hp_pemilik = $request->no_hp_pemilik;
        $pengajuan_bdd->jangka_waktu_amortisasi = $request->jangka_waktu_amortisasi;
        
        $pengajuan_bdd->user_id = auth()->user()->id;
        $pengajuan_bdd->tanggal = Carbon::now();
        $pengajuan_bdd->code_area = auth()->user()->code_area;

        $pengajuan_bdd->nomor = auth()->user()->code_area.'/'.date('n').'/'.date('Y');
        $pengajuan_bdd->status_approve = 'admin-head';
        
        if ($request->hasFile('perjanjian')) {
            $gambar = $request->perjanjian;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/bdd-sewa/perjanjian', $namafile);
        }

        if ($request->hasFile('no_shm_hgb')) {
            $gambar = $request->no_shm_hgb;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/bdd-sewa/no_shm_hgb', $namafile);
        }
        
        $pengajuan_bdd->save();
        // DB::commit();
        return redirect()->route('admin.pengajuan-bdd.index')->with('success', 'Data berhasil dibuat');
    }

    public function pengajuan_sewa_edit($id)
    {
        $data['title'] = 'Edit Pengajuan BDD Sewa';
        $data['bdd_sewa'] = PengajuanBdd::find($id);
        $data['sewa'] = JenisPeralatan::where('group_bdd', 'sewa')->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-bdd.edit-sewa', $data);
    }

    public function update_sewa(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'jenis_bdd_id' => 'required',
            'kode_branch' => 'required',
            'lokasi' => 'required',
            'jenis_sewa' => 'required',
            'type_bangunan' => 'required',
            'nama_pemilik' => 'required',
            'status_kepemilikan' => 'required',
            'no_langganan_listrik' => 'required',
            'no_langganan_pam' => 'required',
            'no_langganan_telp' => 'required',
            'harga_sewa' => 'required',
            'no_rek_bank_pemilik' => 'required',
            'atas_nama' => 'required',
            'no_hp_pemilik' => 'required',
            'jangka_waktu_amortisasi' => 'required',
        ]);
        
        $pengajuan_bdd = PengajuanBdd::find($id);
        $pengajuan_bdd->jenis_bdd_id = $request->jenis_bdd_id;
        $pengajuan_bdd->kode_branch = $request->kode_branch;
        
        $pengajuan_bdd->lokasi = $request->lokasi;
        $pengajuan_bdd->jenis_sewa = $request->jenis_sewa;
        $pengajuan_bdd->type_bangunan = $request->type_bangunan;
        $pengajuan_bdd->nama_pemilik = $request->nama_pemilik;
        $pengajuan_bdd->status_kepemilikan = $request->status_kepemilikan;
        $pengajuan_bdd->no_langganan_listrik = $request->no_langganan_listrik;
        $pengajuan_bdd->no_langganan_pam = $request->no_langganan_pam;
        $pengajuan_bdd->no_langganan_telp = $request->no_langganan_telp;
        $pengajuan_bdd->harga_sewa = $request->harga_sewa;
        // $pengajuan_bdd->pajak_ps = $request->pajak_ps;
        // $pengajuan_bdd->harga_sewa_net = $request->harga_sewa_net;
        $pengajuan_bdd->no_rek_bank_pemilik = $request->no_rek_bank_pemilik;
        $pengajuan_bdd->atas_nama = $request->atas_nama;
        $pengajuan_bdd->no_hp_pemilik = $request->no_hp_pemilik;
        $pengajuan_bdd->jangka_waktu_amortisasi = $request->jangka_waktu_amortisasi;

        if ($request->hasFile('perjanjian')) {
            $gambar = $request->perjanjian;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/bdd-sewa/perjanjian', $namafile);
        }

        if ($request->hasFile('no_shm_hgb')) {
            $gambar = $request->no_shm_hgb;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/bdd-sewa/no_shm_hgb', $namafile);
        }
        
        $pengajuan_bdd->update();
        // DB::commit();
        return redirect()->route('admin.pengajuan-bdd.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_asuransi(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'jenis_bdd_id' => 'required',
            'kode_branch' => 'required',
            'jenis_asuransi' => 'required',
            'produk_asuransi' => 'required',
            'penjaminan' => 'required',
            'nama_perusahaan' => 'required',
            'harga_pertanggungan' => 'required',
            'no_pic_ar' => 'required',
            'jangka_waktu_amortisasi' => 'required',

        ]);
        
        $pengajuan_bdd = New PengajuanBdd();
        $pengajuan_bdd->jenis_bdd_id = $request->jenis_bdd_id;
        $pengajuan_bdd->kode_branch = $request->kode_branch;
        $pengajuan_bdd->jenis_asuransi = $request->jenis_asuransi;
        $pengajuan_bdd->produk_asuransi = $request->produk_asuransi;
        $pengajuan_bdd->nama_perusahaan = $request->nama_perusahaan;
        $pengajuan_bdd->penjaminan = $request->penjaminan;
        $pengajuan_bdd->harga_pertanggungan = $request->harga_pertanggungan;
        $pengajuan_bdd->no_pic_ar = $request->no_pic_ar;
        $pengajuan_bdd->jangka_waktu_amortisasi = $request->jangka_waktu_amortisasi;

        $pengajuan_bdd->user_id = auth()->user()->id;
        $pengajuan_bdd->tanggal = Carbon::now();
        $pengajuan_bdd->code_area = auth()->user()->code_area;

        $pengajuan_bdd->nomor = auth()->user()->code_area.'/'.date('n').'/'.date('Y');
        $pengajuan_bdd->status_proses = 'supervisor';
        
        if ($request->hasFile('perjanjian')) {
            $pengajuan_bdd->perjanjian = $this->upload_file($request->file('perjanjian'), 'pengajuan/bdd/perjanjian');
        }

        if ($request->hasFile('no_polis')) {
            $pengajuan_bdd->no_polis = $this->upload_file($request->file('no_polis'), 'pengajuan/bdd/no_polis');
        }
        
        $pengajuan_bdd->save();
        // DB::commit();
        return redirect()->route('admin.pengajuan-bdd.index')->with('success', 'Data berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PengajuanBdd  $pengajuanBdd
     * @return \Illuminate\Http\Response
     */
    public function show(PengajuanBdd $pengajuanBdd)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = $pengajuanBdd;
        return view('admin.pengajuan-bdd.detail', $data);
    }

    public function buat_lpj(Request $request, $id)
    {
        $data['title'] = 'Detail Pengajuan BDD';
        $data['detail'] = PengajuanBdd::find($id);
        return view('admin.lpj-bdd.buat-lpj-bdd', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PengajuanBdd  $pengajuanBdd
     * @return \Illuminate\Http\Response
     */
    public function pengajuan_asuransi_edit($id)
    {
        $data['title'] = 'Edit Pengajuan BDD Asuransi';
        $data['bdd_asuransi'] = PengajuanBdd::find($id);
        $data['asuransi'] = JenisPeralatan::where('group_bdd', 'asuransi')->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-bdd.edit-asuransi', $data);
    }

    public function update_asuransi(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'jenis_bdd_id' => 'required',
            'kode_branch' => 'required',
            'jenis_asuransi' => 'required',
            'produk_asuransi' => 'required',
            'penjaminan' => 'required',
            'nama_perusahaan' => 'required',
            'harga_pertanggungan' => 'required',
            'no_pic_ar' => 'required',
            'jangka_waktu_amortisasi' => 'required',

        ]);
        
        $pengajuan_bdd = PengajuanBdd::find($id);
        $pengajuan_bdd->jenis_bdd_id = $request->jenis_bdd_id;
        $pengajuan_bdd->kode_branch = $request->kode_branch;
        $pengajuan_bdd->jenis_asuransi = $request->jenis_asuransi;
        $pengajuan_bdd->produk_asuransi = $request->produk_asuransi;
        $pengajuan_bdd->nama_perusahaan = $request->nama_perusahaan;
        $pengajuan_bdd->penjaminan = $request->penjaminan;
        $pengajuan_bdd->harga_pertanggungan = $request->harga_pertanggungan;
        $pengajuan_bdd->no_pic_ar = $request->no_pic_ar;
        $pengajuan_bdd->jangka_waktu_amortisasi = $request->jangka_waktu_amortisasi;
        
        if ($request->hasFile('perjanjian')) {
            $pengajuan_bdd->perjanjian = $this->upload_file($request->file('perjanjian'), 'pengajuan/bdd/perjanjian');
        }

        if ($request->hasFile('no_polis')) {
            $pengajuan_bdd->no_polis = $this->upload_file($request->file('no_polis'), 'pengajuan/bdd/no_polis');
        }
        
        $pengajuan_bdd->update();
        // DB::commit();
        return redirect()->route('admin.pengajuan-bdd.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PengajuanBdd  $pengajuanBdd
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pengajuan_bdd = PengajuanBdd::findOrFail($id);
            $pengajuan_bdd->delete();
            DB::commit();

            return redirect()->route('admin.pengajuan-bdd.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function daftar_bdd()
    {
        $data['title'] = 'Daftar BDD';
        $data['daftar_bdd'] = DaftarBdd::all();
        return view('admin.daftar-bdd.index', $data);
    }
}
