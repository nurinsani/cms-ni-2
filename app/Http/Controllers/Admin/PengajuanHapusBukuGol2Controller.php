<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\InventarisGol2;
use App\Models\PengajuanHapusBukuGol2;
use App\Models\PengajuanHapusBukuDetailGol2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PengajuanHapusBukuGol2Controller extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Hapus Buku Inventaris Gol 2';
        $user = Auth::user();

        // Ambil pengajuan berdasarkan user yang login
        $data['pengajuan_hapus_buku'] = PengajuanHapusBukuGol2::with(['branch', 'user', 'details.inventaris_gol_2'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('admin.pengajuan-hapus-buku-gol-2.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Pengajuan Hapus Buku Gol 2';
        $user = Auth::user();

        // Pilihan Cabang / AP berdasarkan area user atau semua branch yang aktif
        $data['pilih_ap'] = Branch::where('code_area', $user->code_area)->orderBy('unit', 'asc')->get();
        if ($data['pilih_ap']->isEmpty()) {
            $data['pilih_ap'] = Branch::orderBy('unit', 'asc')->get();
        }

        $data['bulan_list'] = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('admin.pengajuan-hapus-buku-gol-2.create', $data);
    }

    public function getInventaris($kodeBranch)
    {
        $inventaris = InventarisGol2::where('kode_branch', $kodeBranch)
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', '!=', 'hapus_buku');
            })
            ->where('status_branch', '!=', 'AUDIT')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($inventaris);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_branch' => 'required',
            'alasan' => 'required',
            'bulan_hapus_buku' => 'required',
            'inventaris_id' => 'required|array|min:1',
            'inventaris_id.*' => 'required|exists:inventaris_gol_2,id',
            'surat_moa' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_barang' => 'nullable|array',
            'foto_barang.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'kode_branch.required' => 'KP/Cabang/AP harus dipilih',
            'alasan.required' => 'Alasan hapus buku wajib diisi',
            'bulan_hapus_buku.required' => 'Bulan hapus buku wajib dipilih',
            'inventaris_id.required' => 'Pilih minimal 1 barang inventaris yang akan dihapus buku',
            'inventaris_id.min' => 'Pilih minimal 1 barang inventaris yang akan dihapus buku',
            'surat_moa.required' => 'Lampiran dokumen / Surat MOA wajib diunggah',
        ]);

        try {
            DB::beginTransaction();

            $nomorUrut = date('Ymd') . random_int(1000, 9999);
            $kodePengajuan = 'HB2/' . $request->kode_branch . '/' . $nomorUrut;

            $namaMoa = null;
            if ($request->hasFile('surat_moa')) {
                $fileMoa = $request->file('surat_moa');
                $namaMoa = time() . '_moa_gol2_' . uniqid() . '.' . $fileMoa->getClientOriginalExtension();
                $pathMoa = base_path('files/hapus_buku_gol_2/moa');
                if (!File::isDirectory($pathMoa)) {
                    File::makeDirectory($pathMoa, 0755, true, true);
                }
                $fileMoa->move($pathMoa, $namaMoa);
            }

            $pengajuan = PengajuanHapusBukuGol2::create([
                'kode_pengajuan' => $kodePengajuan,
                'user_id' => Auth::user()->id,
                'kode_branch' => $request->kode_branch,
                'alasan' => $request->alasan,
                'surat_moa' => $namaMoa,
                'bulan_hapus_buku' => $request->bulan_hapus_buku,
                'status_approve' => 'admin-head',
            ]);

            $pathKondisi = base_path('files/hapus_buku_gol_2/kondisi');
            if (!File::isDirectory($pathKondisi)) {
                File::makeDirectory($pathKondisi, 0755, true, true);
            }

            foreach ($request->inventaris_id as $invId) {
                $namaFoto = null;
                if ($request->hasFile("foto_barang.$invId")) {
                    $foto = $request->file("foto_barang.$invId");
                    $namaFoto = time() . '_inv2_' . $invId . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $foto->move($pathKondisi, $namaFoto);
                }

                $kondisi = $request->kondisi_barang[$invId] ?? 'Rusak Berat';

                PengajuanHapusBukuDetailGol2::create([
                    'pengajuan_hapus_buku_id' => $pengajuan->id,
                    'inventaris_gol_2_id' => $invId,
                    'foto' => $namaFoto,
                    'kondisi' => $kondisi,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.pengajuan-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan Hapus Buku Gol 2 berhasil dibuat dan diteruskan ke Admin Head.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error store pengajuan hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $title = 'Detail Pengajuan Hapus Buku Gol 2';
        $pengajuan = PengajuanHapusBukuGol2::with(['branch', 'user', 'details.inventaris_gol_2', 'admin_head', 'akunting'])->findOrFail($id);

        $totalHargaPerolehan      = 0;
        $totalAkumulasiPenyusutan = 0;
        $totalNilaiBuku           = 0;

        foreach ($pengajuan->details as $detail) {
            $inv = $detail->inventaris_gol_2;
            if (!$inv) continue;

            $hargaPerolehan = intval($inv->harga_perolehan ?? 0);
            $totalHargaPerolehan += $hargaPerolehan;

            $akumulasi = $inv->penyusutans()->sum('penyusutan_per_bulan');
            $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

            $totalAkumulasiPenyusutan += $akumulasi;
            $totalNilaiBuku           += $nilaiBuku;
        }

        return view('admin.pengajuan-hapus-buku-gol-2.show', compact(
            'pengajuan',
            'title',
            'totalHargaPerolehan',
            'totalAkumulasiPenyusutan',
            'totalNilaiBuku'
        ));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = PengajuanHapusBukuGol2::with('details')->findOrFail($id);

            if ($pengajuan->status_approve == 'selesai') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah disetujui selesai tidak dapat dihapus.');
            }

            // Hapus file dokumen dan foto
            if ($pengajuan->surat_moa) {
                if (File::exists(base_path('files/hapus_buku_gol_2/moa/' . $pengajuan->surat_moa))) {
                    File::delete(base_path('files/hapus_buku_gol_2/moa/' . $pengajuan->surat_moa));
                } elseif (File::exists(base_path('files/hapus_buku_gol_2/' . $pengajuan->surat_moa))) {
                    File::delete(base_path('files/hapus_buku_gol_2/' . $pengajuan->surat_moa));
                } elseif (File::exists(public_path('images/hapus-buku-gol-2/moa/' . $pengajuan->surat_moa))) {
                    File::delete(public_path('images/hapus-buku-gol-2/moa/' . $pengajuan->surat_moa));
                }
            }

            foreach ($pengajuan->details as $detail) {
                if ($detail->foto) {
                    if (File::exists(base_path('files/hapus_buku_gol_2/kondisi/' . $detail->foto))) {
                        File::delete(base_path('files/hapus_buku_gol_2/kondisi/' . $detail->foto));
                    } elseif (File::exists(base_path('files/hapus_buku_gol_2/' . $detail->foto))) {
                        File::delete(base_path('files/hapus_buku_gol_2/' . $detail->foto));
                    } elseif (File::exists(public_path('images/hapus-buku-gol-2/kondisi/' . $detail->foto))) {
                        File::delete(public_path('images/hapus-buku-gol-2/kondisi/' . $detail->foto));
                    }
                }
            }

            $pengajuan->details()->delete();
            $pengajuan->delete();

            DB::commit();

            return redirect()->route('admin.pengajuan-hapus-buku-gol-2.index')
                ->with('success', 'Pengajuan hapus buku Gol 2 berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error delete pengajuan hapus buku gol 2: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pengajuan: ' . $th->getMessage());
        }
    }
}
