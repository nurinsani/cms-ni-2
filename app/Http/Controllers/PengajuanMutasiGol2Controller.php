<?php

namespace App\Http\Controllers;

use App\Models\Admin\Peralatan;
use App\Models\Branch;
use App\Models\InventarisGol2;
use App\Models\PengajuanMutasi;
use App\Models\PengajuanMutasiGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanMutasiGol2Controller extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Mutasi Gol 2';
        $data['pengajuan_mutasi_gol_2'] = PengajuanMutasiGol2::with('inventaris_gol_2')->where('user_id', auth()->user()->id)->get();
        return view('admin.pengajuan-mutasi-gol-2.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Pengajuan Mutasi';
        $data['inventaris'] = InventarisGol2::all();
        $data['mutasi_ke'] = Branch::all();
        $data['pilih_ap'] = Branch::where('code_area', auth()->user()->code_area)
        ->distinct()->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)
        ->distinct()
        ->get();
        return view('admin.pengajuan-mutasi-gol-2.create', $data);
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
            'peralatan_id.*' => 'required|exists:inventaris_gol_2,id',
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
        $pengajuan = PengajuanMutasiGol2::create([
            'kode_mutasi' => 'MT/' . $request->dari_cabang . '/'. $kode,
            'user_id' => Auth::user()->id,
            'dari_cabang' => $request->dari_cabang,
            'ke_cabang' => $request->ke_cabang,
            'bulan_mutasi' => $request->bulan_mutasi,
            'status_approve' => 'admin-head'
        ]);

        // Simpan Peralatan yang dipilih ke tabel pivot `pengajuan_mutasi_detail_gol_2`
        $pengajuan->peralatans()->attach($request->peralatan_id);
        
        return redirect()->route('admin.pengajuan-mutasi-gol-2.index')->with('success', 'Data pengajuan mutasi berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasiGol2::with('inventaris_gol_2')->findOrFail($id);
        return view('admin.pengajuan-mutasi-gol-2.show', compact('pengajuan', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pengajuan Mutasi';
        $data['branch'] = Branch::all();
        $data['peralatan'] = Peralatan::all();
        $data['pengajuan_mutasi'] = PengajuanMutasiGol2::findOrFail($id);
        return view('admin.pengajuan-mutasi-gol-2.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'peralatan_id' => 'required',
            'ke_cabang' => 'required',
            'bulan_mutasi' => 'required',
        ]);

        $mutasi = PengajuanMutasiGol2::findOrFail($id);
        $mutasi->peralatan_id = $request->peralatan_id;
        $mutasi->ke_cabang = $request->ke_cabang;
        $mutasi->bulan_mutasi = $request->bulan_mutasi;
        $mutasi->update();
        
        return redirect()->route('admin.pengajuan-mutasi-gol-2.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pengajuanMutasi = PengajuanMutasiGol2::findOrFail($id);
        $pengajuanMutasi->peralatans()->detach();
        $pengajuanMutasi->delete();
        return redirect()->route('admin.pengajuan-mutasi-gol-2.index')->with('success', 'Data berhasil dihapus');
    }

    public function getInventarisGol2($kodeBranch)
    {
        // Ambil data inventaris berdasarkan kode_branch yang dipilih, kecuali yang berstatus "AUDIT" atau "hapus_buku"
        $inventaris = InventarisGol2::where('kode_branch', $kodeBranch)
                                ->where('status_branch', '!=', 'AUDIT')
                                ->where(function($q) {
                                    $q->whereNull('status')->orWhere('status', '!=', 'hapus_buku');
                                })
                                ->get();
    
        return response()->json($inventaris);
    }   
}
