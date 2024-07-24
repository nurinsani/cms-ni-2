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
        $data['title'] = 'Supervisor Dashboard';
        $data['belum_approve'] = Pengajuan::where('status_approve', 'pengurus');
        // $data['belum_approve_bdd'] = PengajuanBdd::where('status_proses', 'pengurus');
        $data['pengajuan'] = Pengajuan::where('status_approve', 'pengurus')->where('code_area', auth()->user()->code_area)->get();
        return view('pengurus.dashboard', $data);
    }
}
