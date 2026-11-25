<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\PengajuanMutasi;
use App\Http\Controllers\Controller;
use App\Models\Admin\Peralatan;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Auth;

class PengajuanMutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Pengajuan Mutasi';
        $data['pengajuan_mutasi'] = PengajuanMutasi::with('inventaris')->where('user_id', auth()->user()->id)->get();
        return view('admin.pengajuan-mutasi.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Tambah Pengajuan Mutasi';
        $data['inventaris'] = Inventaris::all();
        $data['mutasi_ke'] = Branch::all();
        $data['pilih_ap'] = Branch::where('code_area', auth()->user()->code_area)
        ->distinct()->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)
        ->distinct()
        ->get();
        return view('admin.pengajuan-mutasi.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'dari_cabang' => 'required|exists:branch,kode_branch',
            'ke_cabang' => 'required|different:dari_cabang|exists:branch,kode_branch',
            'bulan_mutasi' => 'required|string',
            'peralatan_id' => 'required|array|min:1',
            'peralatan_id.*' => 'required|exists:inventaris,id',
        ], [
            'dari_cabang.required' => 'AP Asal wajib dipilih.',
            'dari_cabang.exists' => 'AP Asal tidak valid.',
            'ke_cabang.required' => 'AP Tujuan wajib dipilih.',
            'ke_cabang.different' => 'AP Tujuan tidak boleh sama dengan AP Asal.',
            'ke_cabang.exists' => 'AP Tujuan tidak valid.',
            'bulan_mutasi.required' => 'Bulan mutasi wajib dipilih.',
            'peralatan_id.required' => 'Pilih minimal satu barang inventaris yang akan dimutasi.',
            'peralatan_id.min' => 'Pilih minimal satu barang inventaris yang akan dimutasi.',
            'peralatan_id.*.exists' => 'Barang inventaris yang dipilih tidak valid.',
        ]);

        $kode = date('m').random_int(100000, 999999);

        // Simpan Pengajuan
        $pengajuan = PengajuanMutasi::create([
            'kode_mutasi' => 'MT/' . $request->dari_cabang . '/'. $kode,
            'user_id' => Auth::user()->id,
            'dari_cabang' => $request->dari_cabang,
            'ke_cabang' => $request->ke_cabang,
            'bulan_mutasi' => $request->bulan_mutasi,
            'status_approve' => 'admin-head'
        ]);

        // Simpan Peralatan yang dipilih ke tabel pivot `pengajuan_mutasi_detail`
        $pengajuan->peralatans()->attach($request->peralatan_id);
        
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data pengajuan mutasi berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PengajuanMutasi  $pengajuanMutasi
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasi::with('inventaris')->findOrFail($id);
        return view('admin.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PengajuanMutasi  $pengajuanMutasi
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pengajuan Mutasi';
        $data['branch'] = Branch::all();
        $data['peralatan'] = Peralatan::all();
        $data['pengajuan_mutasi'] = PengajuanMutasi::find($id);
        return view('admin.pengajuan-mutasi.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PengajuanMutasi  $pengajuanMutasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'peralatan_id' => 'required',
            'ke_cabang' => 'required',
            'bulan_mutasi' => 'required',
        ]);

        $mutasi = PengajuanMutasi::find($id);
        $mutasi->peralatan_id = $request->peralatan_id;
        $mutasi->ke_cabang = $request->ke_cabang;
        $mutasi->bulan_mutasi = $request->bulan_mutasi;
        $mutasi->update();
        
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PengajuanMutasi  $pengajuanMutasi
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pengajuanMutasi = PengajuanMutasi::findOrFail($id);
        $pengajuanMutasi->peralatans()->detach();
        $pengajuanMutasi->delete();
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data berhasil dihapus');
    }

    public function getInventaris($kodeBranch)
    {
        // Ambil data inventaris berdasarkan kode_branch yang dipilih, kecuali yang berstatus "AUDIT" atau "hapus_buku"
        $inventaris = Inventaris::where('kode_branch', $kodeBranch)
                                ->where('status_branch', '!=', 'AUDIT')
                                ->where(function($q) {
                                    $q->whereNull('status')->orWhere('status', '!=', 'hapus_buku');
                                })
                                ->get();
    
        return response()->json($inventaris);
    }    

}
