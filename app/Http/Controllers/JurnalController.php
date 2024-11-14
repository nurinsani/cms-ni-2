<?php

namespace App\Http\Controllers;

use App\Models\JurnalAp;
use App\Models\JurnalKp;
use App\Models\TransaksiPembebananAp;
use App\Models\TransaksiPembebananKp;
use App\Models\TransaksiPenyusutanAp;
use App\Models\TransaksiPenyusutanKp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function jurnal_kp(Request $request)
    {
        $title = 'Jurnal KP';

        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_kp = JurnalKp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_kp = JurnalKp::all();
        }

        return view('transaksi.jurnal_kp.index', compact('jurnal_kp', 'title'));
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
        return view('transaksi.jurnal_ap.index', compact('jurnal_ap', 'title'));
    }

    public function jurnal_penyusutan_kp(Request $request)
    {
        $title = 'Jurnal Penyusutan KP';
        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_penyusutan_kp = TransaksiPenyusutanKp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_penyusutan_kp = TransaksiPenyusutanKp::all();
        }
        return view('transaksi.jurnal_penyusutan_kp.index', compact('jurnal_penyusutan_kp', 'title'));
    }

    public function jurnal_penyusutan_ap(Request $request)
    {
        $title = 'Jurnal Penyusutan AP';
        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_penyusutan_ap = TransaksiPenyusutanAp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_penyusutan_ap = TransaksiPenyusutanAp::all();
        }
        return view('transaksi.jurnal_penyusutan_ap.index', compact('jurnal_penyusutan_ap', 'title'));
    }

    public function jurnal_pembebanan_kp(Request $request)
    {
        $title = 'Jurnal Pembebanan KP';
        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_pembebanan_kp = TransaksiPembebananKp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_pembebanan_kp = TransaksiPembebananKp::all();
        }
        return view('transaksi.jurnal_pembebanan_kp.index', compact('jurnal_pembebanan_kp', 'title'));
    }

    public function jurnal_pembebanan_ap(Request $request)
    {
        $title = 'Jurnal Pembebanan AP';
        if (request()->start_date || request()->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $jurnal_pembebanan_ap = TransaksiPembebananAp::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        } else {
            $jurnal_pembebanan_ap = TransaksiPembebananAp::all();
        }
        return view('transaksi.jurnal_pembebanan_ap.index', compact('jurnal_pembebanan_ap', 'title'));
    }
}
