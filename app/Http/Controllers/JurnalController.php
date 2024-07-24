<?php

namespace App\Http\Controllers;

use App\Models\JurnalAp;
use App\Models\JurnalKp;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function jurnal_kp()
    {
        $title = 'Jurnal KP';

        $jurnal_kp = JurnalKp::all();

        return view('admin.jurnal-kp.index', compact('jurnal_kp', 'title'));
    }

    public function jurnal_ap(Request $request)
    {
        $title = 'Jurnal AP';
        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_ap = JurnalAp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_ap = JurnalAp::all();
        }
        return view('admin.jurnal-ap.index', compact('jurnal_ap', 'title'));
    }
}
