<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembebananAp;
use App\Models\TransaksiPembebananKp;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function koreksi_jurnal_ap()
    {
        $title = 'Koreksi Jurnal AP';

        $koreksi_jurnal_ap = TransaksiPembebananAp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();

        return view('transaksi.koreksi_jurnal_ap.index', compact('koreksi_jurnal_ap', 'title'));
    }

    public function update_jurnal_ap(Request $request, $id)
    {
        $data = TransaksiPembebananAp::findOrFail($id);

        $data->keterangan = $request->keterangan;
        $data->debet = $request->debet;
        $data->kredit = $request->kredit;

        $data->save();

        return response()->json(['status' => 'success']);
    }

    public function koreksi_jurnal_kp()
    {
        $title = 'Koreksi Jurnal KP';

        $koreksi_jurnal_kp = TransaksiPembebananKp::whereMonth('tanggal_transaksi', date('m'))
            ->whereYear('tanggal_transaksi', date('Y'))
            ->get();

        return view('transaksi.koreksi_jurnal_kp.index', compact('koreksi_jurnal_kp', 'title'));
    }

    public function update_jurnal_kp(Request $request, $id)
    {
        $data = TransaksiPembebananKp::findOrFail($id);

        $data->keterangan = $request->keterangan;
        $data->debet = $request->debet;
        $data->kredit = $request->kredit;

        $data->save();

        return response()->json(['status' => 'success']);
    }



}
