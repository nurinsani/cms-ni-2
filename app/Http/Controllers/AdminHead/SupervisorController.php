<?php

namespace App\Http\Controllers\AdminHead;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanBdd;
use App\Models\Admin\Peralatan;
use App\Models\Inventaris;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $data['title'] =  'Admin Head Dashboard';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'admin-head')->where('code_area', auth()->user()->code_area)->get();
        $data['belum_approve'] = Pengajuan::where('status_approve', 'admin-head');
        // $data['belum_approve_bdd'] = PengajuanBdd::where('status_proses', 'supervisor');
        return view('admin-head.dashboard', $data);
    }

    public function peralatan()
    {
        $data['title'] = 'Daftar Peralatan';
        $data['peralatan'] = Peralatan::where('kode_branch', auth()->user()->code_area)->get();
        return view('admin-head.peralatan.index', $data);
    }

    public function inventaris()
    {

        $user = auth()->id();
        $data['title'] = 'Daftar Inventaris';
        $data['inventaris'] = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
            ->select(['inventaris.*', 'branch.area'])
            ->where('branch.code_region', auth()->user()->code_region)->get();
        // $data['inventaris'] = Inventaris::where('kode_branch', auth()->user()->code_area)->get();


        $data['total_harga_perolehan'] = Inventaris::sum('harga_perolehan');
        $data['total_peny'] = Inventaris::sum('harga_perolehan') / 48;
        $data['total_peny_2'] = Inventaris::sum('harga_perolehan') / 48 * 10;

        $branches = DB::table('branch')
            ->where('code_region', auth()->user()->code_region)
            ->orderBy('area', 'asc')
            ->get();

        $area = [];

        foreach ($branches as $branch) {
            if (!in_array($branch->area, $area)) {
                $area[] = $branch->area;
            }
        }

        $data['area']   = $area;

        return view('admin-head.inventaris.index', $data);
    }

    public function inventarisGol2()
    {

        $user = auth()->id();
        $data['title'] = 'Daftar Inventaris';
        $data['inventaris'] = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
            ->select(['inventaris.*', 'branch.area'])
            ->where('branch.code_region', auth()->user()->code_region)->get();
        // $data['inventaris'] = Inventaris::where('kode_branch', auth()->user()->code_area)->get();


        $data['total_harga_perolehan'] = Inventaris::sum('harga_perolehan');
        $data['total_peny'] = Inventaris::sum('harga_perolehan') / 48;
        $data['total_peny_2'] = Inventaris::sum('harga_perolehan') / 48 * 10;

        $branches = DB::table('branch')
            ->where('code_region', auth()->user()->code_region)
            ->orderBy('area', 'asc')
            ->get();

        $area = [];

        foreach ($branches as $branch) {
            if (!in_array($branch->area, $area)) {
                $area[] = $branch->area;
            }
        }

        $data['area']   = $area;

        return view('admin-head.inventaris.index', $data);
    }
}
