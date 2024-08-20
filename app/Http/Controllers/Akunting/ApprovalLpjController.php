<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\Peralatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\RencanaPenyusutan;
use Carbon\Carbon;

class ApprovalLpjController extends Controller
{
    public function index()
    {
        $data['title'] = 'Approval LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'lpj-akunting-it')->get();
        return view('akunting.approval.pengajuan-lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        return view('akunting.approval.pengajuan-lpj.detail', $data);
    }   

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'lpj-pengurus';

            foreach ($pengajuan->pengajuan_details as $item) {
                if ($item->harga_pembelian > 500000) {
                    if ($item->tanggal_perolehan >= 16) {
                        if ($item->kode_branch == NULL) {
                            RencanaPenyusutan::create([
                                'user_id' => auth()->user()->id,
                                'kode_branch' => $item->kode_area,
                                'status_branch' => 'AP',
                                'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                                'jenis' => $item->jenis_peralatan->nama_jenis,
                                'merk' => $item->merk,
                                'type' => $item->type,
                                'tanggal_perolehan' => $item->tanggal_perolehan,
                                'jumlah' => $item->kuantitas,
                                'harga_perolehan' => $item->harga_pembelian,
                                'umur' => 48,
                                'keterangan' => $pengajuan->keterangan
                            ]);
                        } else {
                            RencanaPenyusutan::create([
                                'user_id' => auth()->user()->id,
                                'kode_branch' => $item->kode_branch,
                                'status_branch' => 'AP',
                                'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                                'jenis' => $item->jenis_peralatan->nama_jenis,
                                'merk' => $item->merk,
                                'type' => $item->type,
                                'tanggal_perolehan' => $item->tanggal_perolehan,
                                'jumlah' => $item->kuantitas,
                                'harga_perolehan' => $item->harga_pembelian,
                                'umur' => 48,
                                'keterangan' => $pengajuan->keterangan
                            ]);
                        }
                    } else {
                        if ($item->kode_branch == NULL) {
                            Inventaris::create([
                                'user_id' => auth()->user()->id,
                                'kode_branch' => $item->kode_area,
                                'status_branch' => 'AP',
                                'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                                'jenis' => $item->jenis_peralatan->nama_jenis,
                                'merk' => $item->merk,
                                'type' => $item->type,
                                'tanggal_perolehan' => $item->tanggal_perolehan,
                                'jumlah' => $item->kuantitas,
                                'harga_perolehan' => $item->harga_pembelian,
                                'umur' => 48,
                                'keterangan' => $pengajuan->keterangan
                            ]);
                        } else {
                            Inventaris::create([
                                'user_id' => auth()->user()->id,
                                'kode_branch' => $item->kode_branch,
                                'status_branch' => 'AP',
                                'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                                'jenis' => $item->jenis_peralatan->nama_jenis,
                                'merk' => $item->merk,
                                'type' => $item->type,
                                'tanggal_perolehan' => $item->tanggal_perolehan,
                                'jumlah' => $item->kuantitas,
                                'harga_perolehan' => $item->harga_pembelian,
                                'umur' => 48,
                                'keterangan' => $pengajuan->keterangan
                            ]);
                        }
                    }
                } else {
                    if ($item->kode_branch == NULL) {
                        Peralatan::create([
                            'user_id' => auth()->user()->id,
                            'kode_branch' => $item->kode_area,
                            'status_branch' => 'AP',
                            'kode_peralatan' => $pengajuan->nomor,
                            'jenis' => $item->jenis_peralatan->nama_jenis,
                            'merk' => $item->merk,
                            'type' => $item->type,
                            'tanggal_perolehan' => $item->tanggal_perolehan,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'keterangan' => $pengajuan->keterangan
                        ]);
                    } else {
                        Peralatan::create([
                            'user_id' => auth()->user()->id,
                            'kode_branch' => $item->kode_branch,
                            'status_branch' => 'AP',
                            'kode_peralatan' => $pengajuan->nomor,
                            'jenis' => $item->jenis_peralatan->nama_jenis,
                            'merk' => $item->merk,
                            'type' => $item->type,
                            'tanggal_perolehan' => $item->tanggal_perolehan,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'keterangan' => $pengajuan->keterangan
                        ]);
                    }
                }
            }

            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }
    }
}
