<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventarisGol2;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventarisGol2Controller extends Controller
{
    public function index(Request $request)
    {
        $title = 'Inventaris Gol 2';

        // Menarik hanya branches dengan status_aktif = 'aktif'
        $branches = Branch::where('status_aktif', 'aktif')->get();

        $regions = [];
        $area = [];

        foreach ($branches as $row) {
            if (!in_array($row->region, $regions) && strlen($row->region) > 0) {
                $regions[] = $row->region;
            }

            if (!in_array($row->area, $area) && strlen($row->area) > 0) {
                $area[] = $row->area;
            }
        }


        sort($regions);
        sort($area);

        // dd($area);

        return view('inventaris_gol_2.index', compact('regions', 'area', 'title'));
    }

    public function fetchData(Request $request)
    {
        $currentYear = 2025;
        $selectedBranch = $request->input('kode_branch');
        $area = $request->input('area');
        $region = $request->input('region');
    
        $query = InventarisGol2::with('penyusutans');
    
        $pivotData = [];
        $areas = [];
        $branches = [];
    
        if ($selectedBranch) {
            $branch = Branch::where('area', $area)
                ->where('kode_branch', $selectedBranch)
                ->pluck('kode_branch');
    
            $query->whereIn('kode_branch', $branch);
        } elseif ($area) {
            $branch = Branch::where('area', $area)
                ->pluck('kode_branch');
    
            $query->whereIn('kode_branch', $branch);
    
            $branch = Branch::where('area', $area)
                ->get();
    
            foreach ($branch as $row) {
                if (!in_array($row->kode_branch, $branches) && strlen($row->kode_branch) > 0) {
                    $branches[] = $row->kode_branch;
                }
            }
    
            sort($branches);
        } elseif ($region) {
            $branch = Branch::where('region', $region)
                ->pluck('kode_branch');
    
            $query->whereIn('kode_branch', $branch);
    
            $branch = Branch::where('region', $region)
                ->get();
    
            foreach ($branch as $row) {
                if (!in_array($row->area, $areas) && strlen($row->area) > 0) {
                    $areas[] = $row->area;
                }
            }
    
            sort($areas);
        }
    
        $inventaris_gol_2 = $query->get();
    
        foreach ($inventaris_gol_2 as $item) {
            $totalPenyusutanTahunSebelumnya = 0;
            $penyusutanBerjalan = 0;
            $penyusutanPerBulan = $item->harga_perolehan / 24;
        
            // Array untuk menyimpan penyusutan per bulan berdasarkan tanggal
            $monthlyDepreciation = [];
        
            foreach ($item->penyusutans as $penyusutan) {
                $penyusutanDate = Carbon::parse($penyusutan->tanggal_penyusutan);
                $penyusutanYear = $penyusutanDate->year;
                $month = $penyusutanDate->format('Y-m');
            
                // Menjumlahkan penyusutan jika terjadi lebih dari satu kali di bulan yang sama
                if (!isset($monthlyDepreciation[$month])) {
                    $monthlyDepreciation[$month] = 0;
                }
                
                $monthlyDepreciation[$month] += $penyusutan->penyusutan_per_bulan;
            
                // Menambahkan logika untuk tahun sebelumnya
                if ($penyusutanYear < $currentYear) {
                    $totalPenyusutanTahunSebelumnya += $penyusutan->penyusutan_per_bulan;
                } elseif ($penyusutanYear == $currentYear) {
                    // Data penyusutan untuk tahun lalu
                    $penyusutanBerjalan += $penyusutan->penyusutan_per_bulan;
                }
            }
            
            $penyusutanKe = count($item->penyusutans) + $item->penyusutan_ke;
            $totalPenyusutanAwal = $totalPenyusutanTahunSebelumnya + $item->total_penyusutan_awal;
            $totalPenyusutanBerjalan = $penyusutanBerjalan;
            $akumulasiPenyusutan = $totalPenyusutanAwal + $totalPenyusutanBerjalan;
        
            // Tambahkan akumulasi penyusutan dari item yang status_branch-nya AUDIT
            if ($item->status_branch === 'AUDIT') {
                $akumulasiPenyusutan += $item->akumulasi_penyusutan;
            }
        
            $nilaiBukuTerakhir = null;
            if ($item->status_branch !== 'AUDIT') {
                $nilaiBukuTerakhir = $item->harga_perolehan - $akumulasiPenyusutan;
            }
        
            $pivotRow = [
                'kode_branch' => $item->kode_branch,
                // 'unit' => $item->unit,
                'unit' => $item->branch->unit,
                'imei' => $item->imei,
                'jenis' => $item->jenis,
                'type' => $item->type,
                'keterangan' => $item->keterangan,
                'tanggal_perolehan' => $item->tanggal_perolehan,
                'jumlah' => $item->jumlah,
                'harga_perolehan' => $item->harga_perolehan,
                'umur' => $item->umur,
                'penyusutan_perbulan' => $penyusutanPerBulan,
                'total_penyusutan_awal' => $totalPenyusutanAwal,
                'total_penyusutan_berjalan' => $totalPenyusutanBerjalan,
                'akumulasi_penyusutan' => $akumulasiPenyusutan,
                'nilai_buku_terakhir' => $nilaiBukuTerakhir,
                'penyusutan_ke' => $penyusutanKe,
                'nip' => $item->nip,
                'pengguna' => $item->pengguna,
                'jabatan' => $item->jabatan,
            ];
        
            foreach ($monthlyDepreciation as $month => $depreciationSum) {
                $pivotRow[$month] = $depreciationSum;
            }
        
            $pivotData[] = $pivotRow;
        }
        
    
        if (count($pivotData) == 0) {
            $regions = [];
            $branches = [];
        }
    
        return response()->json([
            'pivotData' => $pivotData,
            'areas' => $areas,
            'branches' => $branches,
        ]);
    }

}
