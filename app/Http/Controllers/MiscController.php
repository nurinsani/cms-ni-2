<?php

namespace App\Http\Controllers;

use App\Exports\InventarisExport;
use App\Exports\jurnalKpExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MiscController extends Controller
{
    public function areaByBranch($id)
    {
        $branches = DB::table('branch')
            ->where('kode_branch', $id)
            ->orderBy('unit', 'asc')
            ->get();

        $area = [];

        foreach ($branches as $branch) {
            if (!in_array($branch->area, $area)) {
                $area[] = $branch->area;
            }
        }

        sort($area);

        return response()->json($area);
    }

    public function inventarisExcel(Request $request)
    {
        return Excel::download(new InventarisExport($request), 'inventaris.xlsx');
    }

    public function jurnalKPExcel(Request $request)
    {
        return Excel::download(new jurnalKpExport($request), 'jurnal_kp.xlsx');
    }
}
