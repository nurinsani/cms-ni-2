<?php

namespace App\Http\Controllers\AtasanAkunting;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;

class AtasanAkuntingController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Finance Dashboard';
        $data['belum_approve'] = Pengajuan::where('status_approve', 'finance');
        $data['belum_approve_bdd'] = PengajuanBdd::where('status_proses', 'finance');
        return view('atasan-akunting.dashboard', $data);
    }
}
