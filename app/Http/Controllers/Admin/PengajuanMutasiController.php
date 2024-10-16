<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\PengajuanMutasi;
use App\Http\Controllers\Controller;
use App\Models\Admin\Peralatan;
use App\Models\Inventaris;

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
        $data['penganjuan_mutasi'] = PengajuanMutasi::all();
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
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)
        // ->select('unit')
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

        // dd($request);
        // $request->validate([
        //     'peralatan_id' => 'required',
        //     'ke_cabang' => 'required',
        //     'bulan_mutasi' => 'required',
        // ]);

        // $peralatan = Peralatan::where('peralatan_id', $request->peralatan_id);

        $mutasi = New PengajuanMutasi;
        $mutasi->peralatan_id = $request->peralatan_id;
        $mutasi->dari_cabang = $request->dari_cabang;
        $mutasi->ke_cabang = $request->ke_cabang;
        $mutasi->bulan_mutasi = $request->bulan_mutasi;
        $mutasi->status_approve = 'mutasi-admin-head';
        $mutasi->user_id = auth()->user()->id;
        $mutasi->save();
        
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PengajuanMutasi  $pengajuanMutasi
     * @return \Illuminate\Http\Response
     */
    public function show(PengajuanMutasi $pengajuanMutasi)
    {
        //
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
    public function destroy(PengajuanMutasi $pengajuanMutasi)
    {
        $pengajuanMutasi->delete();
        return redirect()->route('admin.pengajuan-mutasi.index')->with('success', 'Data berhasil dihapus');
    }

    // public function getInventaris($kodeBranch)
    // {
    //     $inventaris = Inventaris::where('kode_branch', $kodeBranch)->get(); // Mengambil data inventaris berdasarkan kode_branch
    //     // dd($inventaris);
    //     return response()->json($inventaris); // Mengembalikan data dalam format JSON
    // }

    public function getInventaris($kodeBranch)
    {
        // Ambil data inventaris berdasarkan kode_branch yang dipilih, kecuali yang memiliki status_branch "AUDIT"
        $inventaris = Inventaris::where('kode_branch', $kodeBranch)
                                ->where('status_branch', '!=', 'AUDIT')
                                ->get();
    
        // Tambahkan ini untuk melihat data yang diambil
        if ($inventaris->isEmpty()) {
            return response()->json(['message' => 'No data found for this kode_branch: ' . $kodeBranch], 404);
        }
    
        return response()->json($inventaris);
    }    

}
