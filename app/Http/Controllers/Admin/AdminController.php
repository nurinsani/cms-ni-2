<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Peralatan;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\Penyusutan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Admin Dashboard';
        $data['nominative'] = Peralatan::all();
        $data['user1'] = User::where('code_area', 1101)->get();

        return view('admin.dashboard', $data);
    }

    public function inventaris()
    {
        $user = auth()->id();
        $data['title'] = 'Daftar Inventaris';
        // $data['inventaris'] = Inventaris::where('kode_branch', 1)->get();
        $data['inventaris'] = Inventaris::join('branch', 'branch.kode_branch', 'inventaris.kode_branch')
            ->select(['inventaris.*', 'branch.*'])
            ->where('branch.code_area', auth()->user()->code_area)
            ->get();

        $data['total_harga_perolehan'] = Inventaris::sum('harga_perolehan');
        $data['total_peny'] = Inventaris::sum('harga_perolehan') / 48;
        $data['total_peny_2'] = Inventaris::sum('harga_perolehan') / 48 * 10;

        $branches = [];

        foreach ($data['inventaris'] as $row) {
            $branches[] = $row->kode_branch;
        }

        $data['branches'] = DB::table('branch')
            ->whereIn('kode_branch', $branches)
            ->orderBy('unit', 'asc')
            ->get();

        return view('admin.inventaris.index', $data);
    }
}
