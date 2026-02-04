<?php

namespace App\Http\Controllers\Akunting;

use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\Peralatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Inventaris;

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
                if (($item->harga_pembelian / $item->kuantitas) >= 500000) {
                    if ($item->kode_branch == NULL) {
                        Inventaris::create([
                            'user_id' => auth()->user()->id,
                            'kode_branch' => $item->kode_area,
                            'status_branch' => $item->kode_user ?? 'AP',
                            'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                            'jenis' => strtoupper($item->jenis_peralatan->nama_jenis),
                            'merk' => strtoupper($item->merk),
                            'type' => strtoupper($item->type),
                            'tanggal_perolehan' => $item->updated_at,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'umur' => 48,
                            'keterangan' => strtoupper($item->jenis_peralatan->nama_jenis . ' ' . $item->type . ' ' . $item->pengguna),
                            'pengguna' => strtoupper($item->pengguna)
                        ]);
                    } else {
                        Inventaris::create([
                            'user_id' => auth()->user()->id,
                            'kode_branch' => $item->kode_branch,
                            'status_branch' => $item->kode_user ?? 'AP',
                            'kode_peralatan' => 'INV-NI-'.$pengajuan->nomor,
                            'jenis' => strtoupper($item->jenis_peralatan->nama_jenis),
                            'merk' => strtoupper($item->merk),
                            'type' => strtoupper($item->type),
                            'tanggal_perolehan' => $item->updated_at,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'umur' => 48,
                            'keterangan' => strtoupper($item->jenis_peralatan->nama_jenis . ' ' . $item->type . ' ' . $item->pengguna),
                            'pengguna' => strtoupper($item->pengguna)
                        ]);
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
                            'tanggal_perolehan' => $item->updated_at,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'keterangan' => $item->jenis_peralatan->nama_jenis . ' ' . $item->type
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
                            'tanggal_perolehan' => $item->updated_at,
                            'jumlah' => $item->kuantitas,
                            'harga_perolehan' => $item->harga_pembelian,
                            'keterangan' => $item->jenis_peralatan->nama_jenis . ' ' . $item->type
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
            $pengajuan->status_approve = 'lpj-admin';
            $pengajuan->approval_2 = auth()->user()->id;
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil ditolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }
}
