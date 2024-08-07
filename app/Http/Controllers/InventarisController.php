<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        return view('inventaris.index', compact('branches'));
    }

    public function fetchData(Request $request)
    {
        $selectedBranch = $request->input('kode_branch');
        
        // Get inventaris data filtered by the selected branch
        $query = Inventaris::with('penyusutans');
        if ($selectedBranch) {
            $query->where('kode_branch', $selectedBranch);
        }
        $inventaris = $query->get();

        // Pivot table for penyusutan per bulan
        $pivotData = [];
        $months = [];

        foreach ($inventaris as $item) {
            $tanggalPerolehan = Carbon::parse($item->tanggal_perolehan);

            $totalPenyusutanAwal = 0;
            foreach ($item->penyusutans as $penyusutan) {
                $totalPenyusutanAwal += $penyusutan->penyusutan_per_bulan;
            }

            $pivotRow = [
                'kode_branch' => $item->kode_branch,
                'kode_peralatan' => $item->kode_peralatan,
                'jenis' => $item->jenis,
                'type' => $item->type,
                'keterangan' => $item->keterangan,
                'tanggal_perolehan' => $item->tanggal_perolehan,
                'jumlah' => $item->jumlah,
                'harga_perolehan' => $item->harga_perolehan,
                'umur' => $item->umur,
                'penyusutan_perbulan' => $item->penyusutan_perbulan,
                'total_penyusutan_awal' => $item->total_penyusutan_awal,
            ];

            foreach ($item->penyusutans as $penyusutan) {
                $month = Carbon::parse($penyusutan->tanggal_penyusutan)->format('Y-m');
                $pivotRow[$month] = $penyusutan->penyusutan_per_bulan;
                if (!in_array($month, $months)) {
                    $months[] = $month;
                }
            }

            $pivotData[] = $pivotRow;
        }

        // Sort months in ascending order
        sort($months);

        return response()->json(['pivotData' => $pivotData, 'months' => $months]);
    }
}
