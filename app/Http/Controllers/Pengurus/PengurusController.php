<?php

namespace App\Http\Controllers\Pengurus;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanBdd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PengurusController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Pengurus Dashboard';
        $data['approval_um'] = Pengajuan::where('status_approve', 'pengurus');
        $data['approval_lpj'] = Pengajuan::where('status_approve', 'lpj-pengurus');
        return view('pengurus.dashboard', $data);
    }
}
