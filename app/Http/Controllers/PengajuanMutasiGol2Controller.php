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
        // dd($request);
        // dd($request->peralatan_id);


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

        // Simpan Peralatan yang dipilih ke tabel pivot `pengajuan_mutasi_detail`
        $pengajuan->peralatans()->attach($request->peralatan_id);
        
        return redirect()->route('admin.pengajuan-mutasi-gol-2.index')->with('success', 'Data berhasil dibuat');
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
        $pengajuan = PengajuanMutasiGol2::with('inventaris_gol_2')->findOrFail($id);
        return view('admin.pengajuan-mutasi-gol-2.show', compact('pengajuan', 'title'));
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
        $data['pengajuan_mutasi'] = PengajuanMutasiGol2::find($id);
        return view('admin.pengajuan-mutasi-gol-2.edit', $data);
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
    public function destroy(PengajuanMutasi $pengajuanMutasi)
    {
        $pengajuanMutasi->delete();
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data berhasil dihapus');
    }

    public function getInventarisGol2($kodeBranch)
    {
        // Ambil data inventaris berdasarkan kode_branch yang dipilih, kecuali yang memiliki status_branch "AUDIT"
        $inventaris = InventarisGol2::where('kode_branch', $kodeBranch)
                                ->where('status_branch', '!=', 'AUDIT')
                                ->get();
    
        // Tambahkan ini untuk melihat data yang diambil
        if ($inventaris->isEmpty()) {
            return response()->json(['message' => 'No data found for this kode_branch: ' . $kodeBranch], 404);
        }
    
        return response()->json($inventaris);
    }   
}
