<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\StockOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StokOpnameController extends Controller
{
    public function index()
    {
        $title = 'Stock Opname';
        $inventaris = Inventaris::where('kode_branch', '1010')->orderBy('tanggal_perolehan', 'DESC')->get();        
        $so = StockOpname::with('inventaris')->orderBy('created_at', 'DESC')->get();

        return view('stok_opname.index', compact('so', 'title', 'inventaris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'keadaan' => 'required|string',
            'kondisi' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $stokOpname = StockOpname::create([
                'inventaris_id' => $request->inventaris_id,
                'keadaan' => $request->keadaan,
                'kondisi' => $request->kondisi,
                'keterangan' => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data stok opname berhasil ditambahkan!',
                'data' => $stokOpname
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'inventaris_id' => 'required|exists:inventaris,id',
                'keadaan' => 'required|string',
                'kondisi' => 'required|string',
                'keterangan' => 'nullable|string'
            ]);

            $stokOpname = StockOpname::findOrFail($id);
            $stokOpname->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data stok opname berhasil diupdate!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $stokOpname = StockOpname::findOrFail($id);
            $stokOpname->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data stok opname berhasil dihapus!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cetakKartu()
    {
        $items = Inventaris::where('kode_branch', '1010')->orderBy('tanggal_perolehan', 'DESC')->get();


        return view('stok_opname.kartu_stok', compact('items'));
    }

}
