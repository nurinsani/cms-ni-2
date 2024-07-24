<?php

namespace App\Http\Controllers\AtasanIt;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Http\Request;

class AtasanItController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'IT Dashboard';
        $data['belum_approve'] = Pengajuan::where('status_approve', 'atasan-it');
        $data['belum_approve_bdd'] = PengajuanBdd::where('status_proses', 'atasan-it');
        return view('atasan-it.dashboard', $data);
    }
}
