<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Lpj;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Akunting\Transaksi;

class JurnalPembelianController extends Controller
{
    public function index()
    {
        $data['title'] = 'Jurnal Pembelian';
        $data['transaksi'] = Transaksi::all();
        return view('admin.jurnal-pembelian.index', $data);
    }
}
