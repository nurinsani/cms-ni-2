<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\PengajuanMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanMutasiAhController extends Controller
{
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

    public function index()
    {
        $data['title'] = 'Pengajuan Mutasi';
        $data['pengajuan_mutasi'] = PengajuanMutasi::with('inventaris')->get();
        return view('admin-head.pengajuan_mutasi.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Pengajuan Mutasi';
        $data['inventaris'] = Inventaris::all();
        $data['mutasi_ke'] = Branch::all();
        $data['pilih_ap'] = Branch::all();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)
        ->distinct()
        ->get();
        return view('admin-head.pengajuan_mutasi.create', $data);
    }

    public function store(Request $request)
    {

        // dd($request);

        $kode = date('m').random_int(100000, 999999);

        // Simpan Pengajuan
        $pengajuan = PengajuanMutasi::create([
            'kode_mutasi' => 'MT/' . $request->dari_cabang . '/'. $kode,
            'user_id' => Auth::user()->id,
            'dari_cabang' => $request->dari_cabang,
            'ke_cabang' => $request->ke_cabang,
            'bulan_mutasi' => $request->bulan_mutasi,
            'status_approve' => 'akunting'
        ]);

        // Simpan Peralatan yang dipilih ke tabel pivot `pengajuan_mutasi_detail`
        $pengajuan->peralatans()->attach($request->peralatan_id);
        
        return redirect()->route('admin-head.pengajuan-mutasi.index')->with('success', 'Data berhasil dibuat');
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasi::with('inventaris')->findOrFail($id);
        return view('admin-head.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }
}
