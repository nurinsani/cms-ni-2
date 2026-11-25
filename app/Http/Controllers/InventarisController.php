<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Inventaris';
        $user = auth()->user();

        $branchQuery = Branch::where('status_aktif', 'aktif');

        // Jika user adalah admin cabang (bukan kantor pusat 1010)
        if ($user && $user->hasRole('admin') && !empty($user->code_area) && $user->code_area != 1010) {
            $branchQuery->where('code_area', $user->code_area);
        } elseif ($user && $user->hasRole('admin-head') && !empty($user->code_region)) {
            $branchQuery->where('code_region', $user->code_region);
        }

        $branches = $branchQuery->get();

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

        return view('inventaris.index', compact('regions', 'area', 'title', 'branches'));
    }

    public function fetchData(Request $request)
    {
        $currentYear = $request->input('tahun') ? (int)$request->input('tahun') : (int)date('Y');
        $selectedMonth = $request->input('bulan');
        $selectedBranch = $request->input('kode_branch');
        $area = $request->input('area');
        $region = $request->input('region');
    
        $query = Inventaris::with('penyusutans', 'branch')
            ->where(function ($q) {
                if (Schema::hasColumn('inventaris', 'status')) {
                    $q->whereNull('status')
                      ->orWhere('status', 'aktif')
                      ->orWhere('status', '!=', 'hapus_buku');
                }
            });

        // Filter otomatis sesuai wilayah kewenangan role pengguna
        $user = auth()->user();
        if ($user && $user->hasRole('admin') && !empty($user->code_area) && $user->code_area != 1010) {
            $adminBranchCodes = Branch::where('code_area', $user->code_area)->pluck('kode_branch');
            $query->whereIn('kode_branch', $adminBranchCodes);
        } elseif ($user && $user->hasRole('admin-head') && !empty($user->code_region)) {
            $ahBranchCodes = Branch::where('code_region', $user->code_region)->pluck('kode_branch');
            $query->whereIn('kode_branch', $ahBranchCodes);
        }

        if ($selectedMonth) {
            $cutoffDate = Carbon::createFromDate($currentYear, (int)$selectedMonth, 1)->endOfMonth()->toDateString();
            $query->where(function($q) use ($cutoffDate) {
                $q->whereDate('tanggal_perolehan', '<=', $cutoffDate)
                  ->orWhereNull('tanggal_perolehan');
            });
        } elseif ($request->filled('tahun')) {
            $cutoffDate = Carbon::createFromDate($currentYear, 12, 31)->toDateString();
            $query->where(function($q) use ($cutoffDate) {
                $q->whereDate('tanggal_perolehan', '<=', $cutoffDate)
                  ->orWhereNull('tanggal_perolehan');
            });
        }
    
        $pivotData = [];
        $areas = [];
        $branches = [];
    
        if ($selectedBranch) {
            $query->where('kode_branch', $selectedBranch);
        } elseif ($area) {
            $branchCodes = Branch::where('area', $area)->pluck('kode_branch');
            $query->whereIn('kode_branch', $branchCodes);
    
            $branchesList = Branch::where('area', $area)->get();
            foreach ($branchesList as $row) {
                if (!in_array($row->kode_branch, $branches) && strlen($row->kode_branch) > 0) {
                    $branches[] = $row->kode_branch;
                }
            }
            sort($branches);
        } elseif ($region) {
            $branchCodes = Branch::where('region', $region)->pluck('kode_branch');
            $query->whereIn('kode_branch', $branchCodes);
    
            $branchesList = Branch::where('region', $region)->get();
            foreach ($branchesList as $row) {
                if (!in_array($row->area, $areas) && strlen($row->area) > 0) {
                    $areas[] = $row->area;
                }
            }
            sort($areas);
        }
    
        $inventaris = $query->get();

        /** @var \App\Models\Inventaris $item */
        foreach ($inventaris as $item) {
            $totalPenyusutanTahunSebelumnya = 0;
            $penyusutanBerjalan = 0;
            $countPenyusutanPosisi = 0;
            $penyusutanPerBulan = $item->harga_perolehan > 0 ? $item->harga_perolehan / 48 : 0;
        
            // Array untuk menyimpan penyusutan per bulan berdasarkan tanggal
            $monthlyDepreciation = [];
        
            foreach ($item->penyusutans as $penyusutan) {
                $penyusutanDate = Carbon::parse($penyusutan->tanggal_penyusutan);
                $penyusutanYear = $penyusutanDate->year;
                $penyusutanMonth = $penyusutanDate->month;
                $month = $penyusutanDate->format('Y-m');
            
                // Logika penyusutan berdasarkan posisi bulan & tahun
                if ($penyusutanYear < $currentYear) {
                    $totalPenyusutanTahunSebelumnya += $penyusutan->penyusutan_per_bulan;
                    $countPenyusutanPosisi++;
                } elseif ($penyusutanYear == $currentYear) {
                    if (!$selectedMonth || $penyusutanMonth <= (int)$selectedMonth) {
                        $penyusutanBerjalan += $penyusutan->penyusutan_per_bulan;
                        $countPenyusutanPosisi++;

                        if (!isset($monthlyDepreciation[$month])) {
                            $monthlyDepreciation[$month] = 0;
                        }
                        $monthlyDepreciation[$month] += $penyusutan->penyusutan_per_bulan;
                    }
                }
            }
            
            $penyusutanKe = $countPenyusutanPosisi + $item->penyusutan_ke;
            $totalPenyusutanAwal = $totalPenyusutanTahunSebelumnya + $item->total_penyusutan_awal;
            $totalPenyusutanBerjalan = $penyusutanBerjalan;
            $akumulasiPenyusutan = $totalPenyusutanAwal + $totalPenyusutanBerjalan;
        
            // Tambahkan akumulasi penyusutan dari item yang status_branch-nya AUDIT
            if ($item->status_branch === 'AUDIT') {
                $auditAkumulasi = floatval($item->getRawOriginal('akumulasi_penyusutan') ?? 0);
                $akumulasiPenyusutan += $auditAkumulasi;
            }
        
            $nilaiBukuTerakhir = max(0, $item->harga_perolehan - $akumulasiPenyusutan);
        
            $pivotRow = [
                'kode_branch' => $item->kode_branch,
                'unit' => optional($item->branch)->unit ?? '',
                'kode_peralatan' => $item->kode_peralatan,
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
                'pengguna' => $item->pengguna,
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
            'selectedYear' => $currentYear,
        ]);
    }

}
