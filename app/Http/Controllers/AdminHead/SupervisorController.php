<?php

namespace App\Http\Controllers\AdminHead;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\PengajuanMutasi;
use App\Models\PengajuanMutasiGol2;
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
        $data['title'] = 'Dashboard Admin Head';
        $codeRegion = auth()->user()->code_region;

        // 1. Approval Tasks (Pengajuan dari cabang di wilayah region Admin Head yang menunggu persetujuan)
        $data['approval_peralatan_count'] = Pengajuan::join('branch', 'branch.code_area', '=', 'pengajuans.code_area')
            ->where('pengajuans.status_approve', 'admin-head')
            ->where('branch.code_region', $codeRegion)
            ->distinct('pengajuans.id')
            ->count('pengajuans.id');

        $data['approval_lpj_count'] = Pengajuan::where('status_approve', 'lpj-admin-head')->count();

        $data['approval_klaim_count'] = Pengajuan::join('branch', 'branch.code_area', '=', 'pengajuans.code_area')
            ->where('pengajuans.status_approve', 'klaim-admin-head')
            ->where('branch.code_region', $codeRegion)
            ->distinct('pengajuans.id')
            ->count('pengajuans.id');

        $data['approval_mutasi_count'] = PengajuanMutasi::where('status_approve', 'admin-head')->count()
            + PengajuanMutasiGol2::where('status_approve', 'admin-head')->count();

        // 2. Pengajuan Milik Admin Head Sendiri
        $data['pengajuan_um_count'] = Pengajuan::where('user_id', auth()->id())->count();

        // 3. Ringkasan Aset di Wilayah (Region)
        $data['total_inventaris_count'] = Inventaris::join('branch', 'branch.kode_branch', '=', 'inventaris.kode_branch')
            ->where('branch.code_region', $codeRegion)
            ->count();

        // 4. Daftar Pengajuan Peralatan Menunggu Approval (Maks. 5 pengajuan terbaru)
        $data['pending_approvals'] = Pengajuan::with(['area', 'pengajuan_details.jenis_peralatan', 'user'])
            ->join('branch', 'branch.code_area', '=', 'pengajuans.code_area')
            ->select(['pengajuans.*'])
            ->where('pengajuans.status_approve', 'admin-head')
            ->where('branch.code_region', $codeRegion)
            ->distinct()
            ->orderBy('pengajuans.tanggal_pengajuan', 'desc')
            ->orderBy('pengajuans.id', 'desc')
            ->take(5)
            ->get();

        // Legacy compatibility
        $data['belum_approve'] = Pengajuan::where('status_approve', 'admin-head');
        $data['pengajuan'] = $data['pending_approvals'];

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
