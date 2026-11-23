<?php

namespace App\Http\Controllers\AtasanIt;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;

class AtasanItController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Dashboard Atasan IT';
        $data['approval_peralatan'] = Pengajuan::where('status_approve', 'atasan-it')->get();
        $data['approval_lpj'] = Pengajuan::where('status_approve', 'lpj-atasan-it')->get();
        $data['approval_klaim'] = Pengajuan::where('status_approve', 'klaim-atasan-it')->get();
        
        // Alias backward compatibility
        $data['belum_approve'] = $data['approval_peralatan'];

        return view('atasan-it.dashboard', $data);
    }
}
