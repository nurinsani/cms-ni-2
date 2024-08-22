<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\Peralatan;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\RencanaPenyusutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class klaimController extends Controller
{
    public function index()
    {
        $data['title'] = 'Klaim Pembelian';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'klaim-akunting-it')
        ->get();
        return view('akunting.approval.klaim.index', $data);
    }

    public function detail($id)
    {
        $title = 'Detail';
        $pengajuan = Pengajuan::findOrFail($id);
        $branch = Branch::where('code_area', 'kode_area')->get();
        return view('akunting.approval.klaim.detail', compact('title', 'pengajuan'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'klaim-pengurus';
            $pengajuan->approval_2 = auth()->user()->id;

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

    public function tolak(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
            $pengajuan->status_approve = 'admin';
            $pengajuan->approval_1 = auth()->user()->id;
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }
}
