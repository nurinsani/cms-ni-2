<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Akunting\TransaksiBdd;

class JurnalBddController extends Controller
{
    public function index()
    {
        $data['title'] = 'Jurnal BDD';
        $data['transaksi_bdd'] = TransaksiBdd::all();
        return view('admin.jurnal-bdd.index', $data);
    }
}
