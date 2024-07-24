<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\Peralatan;
use App\Models\Inventaris;

class AkuntingController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Akunting Dashboard';
        $data['belum_approve'] = Pengajuan::where('status_approve', 'akunting-it')->get();
        // $data['belum_approve_bdd'] = PengajuanBdd::where('status_proses', 'akunting-it')->whereHas('jenis_bdd', function($query){
        //     $query->where('group_approval', 'umum');
        //     })->get();
        return view('akunting.dashboard', $data);
    }

    public function peralatan()
    {
        $user = auth()->id();
        $data['title'] = 'Daftar Peralatan';

        $data['peralatan'] = Peralatan::all();

        return view('admin-head.peralatan.index', $data);
    }

    public function inventaris()
    {
        $user = auth()->id();
        $data['title'] = 'Daftar Inventaris';

        $data['inventaris'] = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
            ->select(['inventaris.*', 'branch.area'])
            ->get();

        // dd($data);
        $branches = [];

        foreach ($data['inventaris'] as $row) {
            $branches[] = $row->kode_branch;
        }

        $data['branches'] = DB::table('branch')
            ->whereIn('kode_branch', $branches)
            ->orderBy('unit', 'asc')
            ->get();

        $area = [];

        foreach ($data['branches'] as $branch) {
            if (!in_array($branch->area, $area)) {
                $area[] = $branch->area;
            }
        }

        sort($area);

        $data['area'] = $area;

        $data['total_harga_perolehan'] = Inventaris::sum('harga_perolehan');
        $data['total_peny'] = Inventaris::sum('harga_perolehan') / 48;
        $data['total_peny_2'] = Inventaris::sum('harga_perolehan') / 48 * 10;

        return view('akunting.inventaris.index', $data);
    }
}
