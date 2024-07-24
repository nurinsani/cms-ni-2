<?php

namespace App\Exports;

use App\Models\Inventaris;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class InventarisExport implements FromView
{
    private $area;
    private $kode_branch;
    private $harga_perolehan;
    private $peny;
    private $peny_awal;
    private $peny_jalan;
    private $penyusutan;
    private $total_nilai_buku;

    public function __construct($request)
    {
        $this->area = $request->area;
        $this->kode_branch = $request->kode_branch;
        $this->harga_perolehan = $request->harga_perolehan;
        $this->peny = $request->peny;
        $this->peny_awal = $request->peny_awal;
        $this->peny_jalan = $request->peny_jalan;
        $this->penyusutan = $request->penyusutan;
        $this->total_nilai_buku = $request->total_nilai_buku;
    }

    public function view(): View
    {
        if (Auth::user()->roles[0]->name == 'admin-head') {
            $inventaris = Inventaris::join('branch', 'inventaris.kode_branch', 'inventaris.kode_branch')
                ->select(['inventaris.*', 'branch.area', 'branch.code_area', 'branch.kode_branch'])
                ->where('branch.code_region', auth()->user()->code_region)
                ->get();

            if ($this->area && $this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'inventaris.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.area', 'branch.code_area', 'branch.kode_branch'])
                    ->where('inventaris.kode_branch', $this->kode_branch)
                    ->where('branch.area', $this->area)
                    ->where('branch.code_region', auth()->user()->code_region)
                    ->get();
            } elseif ($this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'inventaris.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.area', 'branch.code_area', 'branch.kode_branch'])
                    ->where('inventaris.kode_branch', $this->kode_branch)
                    ->where('branch.code_region', auth()->user()->code_region)
                    ->get();
            } elseif ($this->area) {
                $inventaris = Inventaris::join('branch', 'inventaris.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.area', 'branch.code_area', 'branch.kode_branch'])
                    ->where('branch.area', $this->area)
                    ->where('branch.code_region', auth()->user()->code_region)
                    ->get();
            }
        } elseif (Auth::user()->roles[0]->name == 'akunting') {
            $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                ->select(['inventaris.*', 'branch.*'])
                ->get();

            if ($this->area && $this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.kode_branch', $this->kode_branch)
                    ->where('branch.area', $this->area)
                    ->get();
            } elseif ($this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.kode_branch', $this->kode_branch)
                    ->get();
            } elseif ($this->area) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.area', $this->area)
                    ->get();
            }
        } else {
            $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                ->select(['inventaris.*', 'branch.*'])
                ->where('branch.code_area', auth()->user()->code_area)
                ->get();

            if ($this->area && $this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.kode_branch', $this->kode_branch)
                    ->where('branch.area', $this->area)
                    ->where('branch.code_area', auth()->user()->code_area)
                    ->get();
            } elseif ($this->kode_branch) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.kode_branch', $this->kode_branch)
                    ->where('branch.code_area', auth()->user()->code_area)
                    ->get();
            } elseif ($this->area) {
                $inventaris = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
                    ->select(['inventaris.*', 'branch.*'])
                    ->where('branch.area', $this->area)
                    ->where('branch.code_area', auth()->user()->code_area)
                    ->get();
            }
        }

        $data = [
            'harga_perolehan'   => $this->harga_perolehan,
            'inventaris'        => $inventaris,
            'peny'              => $this->peny,
            'peny_awal'         => $this->peny_awal,
            'peny_jalan'        => $this->peny_jalan,
            'penyusutan'        => $this->penyusutan,
            'total_nilai_buku'  => $this->total_nilai_buku,
        ];

        if (Auth::user()->roles[0]->name == 'admin-head') {
            return view('exports.inventarisHead', $data);
        } else {
            return view('exports.inventaris', $data);
        }
    }
}
