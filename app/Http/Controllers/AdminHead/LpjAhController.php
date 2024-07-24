<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use Illuminate\Http\Request;

class LpjAhController extends Controller
{
    public function index()
    {
        $data['title'] = 'LPJ';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'lpj-admin-head')->get();
        return view('admin-head.lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        $data['detail'] = PengajuanDetail::where('pengajuan_id', $id)->first();
        $data['pengajuan_detail'] = PengajuanDetail::where('pengajuan_id', $id)->get();
        return view('admin-head.lpj.detail', $data);
    }

    public function ajukan_lpj(Request $request, $id)
    {
        $ajukan_lpj = Pengajuan::findOrFail($id);
        // if ($ajukan_lpj->tanggal_pembelian == NULL) {
        //     return redirect()->back()->with('error', 'Anda belum buat LPJ.');
        // }
        
        if (auth()->user()->code_area == 1010) {
            $ajukan_lpj->update([
                'status_approve' => 'lpj-atasan-akunting',
            ]);
        } else {
            $ajukan_lpj->update([
                'status_approve' => 'lpj-akunting-it',
                // 'nominal_kurang' => $ajukan_lpj->subtotal() - $ajukan_lpj->total_pembelian()
            ]);

        }
        return redirect('admin-head/lpj')->with('success', 'Data sudah di Ajukan');
    }
}
