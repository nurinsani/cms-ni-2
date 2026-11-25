<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventarisGol2;
use App\Exports\InventarisGol2TemplateExport;
use App\Imports\InventarisGol2Import;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventarisGol2Controller extends Controller
{
    public function index(Request $request)
    {
        $title = 'Inventaris Gol 2';
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

        return view('inventaris_gol_2.index', compact('regions', 'area', 'title', 'branches'));
    }

    public function fetchData(Request $request)
    {
        $currentYear = $request->input('tahun') ? (int)$request->input('tahun') : (int)date('Y');
        $selectedMonth = $request->input('bulan');
        $selectedBranch = $request->input('kode_branch');
        $area = $request->input('area');
        $region = $request->input('region');
    
        $query = InventarisGol2::with('penyusutans', 'branch')
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 'hapus_buku');
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
    
        $inventaris_gol_2 = $query->get();
    
        foreach ($inventaris_gol_2 as $item) {
            $totalPenyusutanTahunSebelumnya = 0;
            $penyusutanBerjalan = 0;
            $countPenyusutanPosisi = 0;
            $umur = $item->umur > 0 ? (int)$item->umur : 24;
            $penyusutanPerBulan = $item->harga_perolehan > 0 ? (int) round($item->harga_perolehan / $umur) : 0;
        
            // Array untuk menyimpan penyusutan per bulan berdasarkan tanggal
            $monthlyDepreciation = [];
        
            foreach ($item->penyusutans as $penyusutan) {
                $penyusutanDate = Carbon::parse($penyusutan->tanggal_penyusutan);
                $penyusutanYear = $penyusutanDate->year;
                $penyusutanMonth = $penyusutanDate->month;
                $month = $penyusutanDate->format('Y-m');
                $nominalPenyusutan = (int) round($penyusutan->penyusutan_per_bulan);
            
                // Logika penyusutan berdasarkan posisi bulan & tahun
                if ($penyusutanYear < $currentYear) {
                    $totalPenyusutanTahunSebelumnya += $nominalPenyusutan;
                    $countPenyusutanPosisi++;
                } elseif ($penyusutanYear == $currentYear) {
                    if (!$selectedMonth || $penyusutanMonth <= (int)$selectedMonth) {
                        $penyusutanBerjalan += $nominalPenyusutan;
                        $countPenyusutanPosisi++;

                        if (!isset($monthlyDepreciation[$month])) {
                            $monthlyDepreciation[$month] = 0;
                        }
                        $monthlyDepreciation[$month] += $nominalPenyusutan;
                    }
                }
            }
            
            $penyusutanKe = $countPenyusutanPosisi + $item->penyusutan_ke;
            $totalPenyusutanAwal = $totalPenyusutanTahunSebelumnya + (int) round($item->total_penyusutan_awal);
            $totalPenyusutanBerjalan = $penyusutanBerjalan;
            $akumulasiPenyusutan = $totalPenyusutanAwal + $totalPenyusutanBerjalan;
        
            // Tambahkan akumulasi penyusutan dari item yang status_branch-nya AUDIT
            if ($item->status_branch === 'AUDIT') {
                $auditAkumulasi = floatval($item->getRawOriginal('akumulasi_penyusutan') ?? 0);
                $akumulasiPenyusutan += (int) round($auditAkumulasi);
            }
        
            $nilaiBukuTerakhir = max(0, $item->harga_perolehan - $akumulasiPenyusutan);
        
            $pivotRow = [
                'kode_branch' => $item->kode_branch,
                'unit' => optional($item->branch)->unit ?? '',
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
                'jabatan'  => $item->jabatan,
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

    public function updateInline(Request $request)
    {
        $request->validate([
            'id'    => 'required|integer',
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $inventaris = InventarisGol2::findOrFail($request->id);

        $field = $request->field;
        $value = $request->value;

        // Field yang boleh diubah via inline edit
        $allowedFields = ['pengguna', 'jabatan', 'nip'];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false]);
        }

        // Jika nilai sama, tidak usah update
        if ($inventaris->$field === $value) {
            return response()->json(['success' => true]);
        }

        // SIMPAN NILAI LAMA
        if ($field === 'nip') {
            $inventaris->old_nip = $inventaris->nip;
        }

        if ($field === 'pengguna') {
            $inventaris->old_pengguna = $inventaris->pengguna;
        }

        if ($field === 'jabatan') {
            $inventaris->old_jabatan = $inventaris->jabatan;
        }

        // UPDATE NILAI BARU
        $inventaris->$field = $value;
        $inventaris->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'Format file harus berupa Excel (.xlsx atau .xls).',
            'file.max'      => 'Ukuran file maksimal adalah 10MB.',
        ]);

        try {
            $import = new InventarisGol2Import();
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();

            return redirect()->back()->with('success', "Berhasil mengimpor {$count} data Inventaris Gol 2.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new InventarisGol2TemplateExport, 'Template_Import_Inventaris_Gol_2.xlsx');
    }
}
