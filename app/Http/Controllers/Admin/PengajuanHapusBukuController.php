<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\PengajuanHapusBuku;
use App\Models\PengajuanHapusBukuDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PengajuanHapusBukuController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan Hapus Buku Inventaris Gol 1';
        $user = Auth::user();

        // Ambil pengajuan berdasarkan area atau user
        $data['pengajuan_hapus_buku'] = PengajuanHapusBuku::with(['branch', 'user', 'inventaris'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('admin.pengajuan-hapus-buku.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Pengajuan Hapus Buku';
        $user = Auth::user();

        // Pilihan Cabang / AP berdasarkan area user atau semua branch yang aktif
        $data['pilih_ap'] = Branch::where('code_area', $user->code_area)->orderBy('unit', 'asc')->get();
        if ($data['pilih_ap']->isEmpty()) {
            $data['pilih_ap'] = Branch::orderBy('unit', 'asc')->get();
        }

        $data['jenis_aktiva_list'] = [
            'Peralatan Kantor',
            'Peralatan Dapur',
            'Peralatan Rumah Tangga',
            'Peralatan Kecil'
        ];

        $data['bulan_list'] = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('admin.pengajuan-hapus-buku.create', $data);
    }

    public function getInventaris($kodeBranch)
    {
        $inventaris = Inventaris::where('kode_branch', $kodeBranch)
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', '!=', 'hapus_buku');
            })
            ->where('status_branch', '!=', 'AUDIT')
            ->orderBy('jenis', 'asc')
            ->get();

        foreach ($inventaris as $item) {
            $text = strtolower(($item->jenis ?? '') . ' ' . ($item->keterangan ?? '') . ' ' . ($item->merk ?? ''));
            if (preg_match('/dapur|dispenser|piring|gelas|sendok|garpu|pisau|rak piring|kompor|gas|rice cooker|blender|kulkas|wajan|dandang|teko/i', $text)) {
                $item->kategori_aktiva = 'Peralatan Dapur';
            } elseif (preg_match('/kasur|bantal|guling|sprei|karpet|sofa|jemuran|tirai|gordyn|lemari pakaian|bed cover/i', $text)) {
                $item->kategori_aktiva = 'Peralatan Rumah Tangga';
            } elseif (preg_match('/alat pembersih|tong sampah|rak helm|gembok|stempel|keran air|jas hujan|bendera|kabel|gunting|tangga|sunblas|pompa air/i', $text)) {
                $item->kategori_aktiva = 'Peralatan Kecil';
            } else {
                $item->kategori_aktiva = 'Peralatan Kantor';
            }
        }

        return response()->json($inventaris);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_branch' => 'required',
            'jenis_aktiva' => 'required',
            'alasan' => 'required',
            'bulan_hapus_buku' => 'required',
            'inventaris_id' => 'required|array|min:1',
            'inventaris_id.*' => 'required|exists:inventaris,id',
            'surat_moa' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_barang' => 'nullable|array',
            'foto_barang.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'kode_branch.required' => 'KP/Cabang/AP harus dipilih',
            'jenis_aktiva.required' => 'Jenis aktiva tetap harus dipilih',
            'alasan.required' => 'Alasan hapus buku wajib diisi',
            'bulan_hapus_buku.required' => 'Bulan hapus buku wajib dipilih',
            'inventaris_id.required' => 'Pilih minimal 1 barang inventaris yang akan dihapus buku',
            'inventaris_id.min' => 'Pilih minimal 1 barang inventaris yang akan dihapus buku',
            'surat_moa.required' => 'Lampiran dokumen / Surat MOA wajib diunggah',
        ]);

        try {
            DB::beginTransaction();

            $nomorUrut = date('Ymd') . random_int(1000, 9999);
            $kodePengajuan = 'HB/' . $request->kode_branch . '/' . $nomorUrut;

            $namaMoa = null;
            if ($request->hasFile('surat_moa')) {
                $fileMoa = $request->file('surat_moa');
                $namaMoa = time() . '_moa_' . uniqid() . '.' . $fileMoa->getClientOriginalExtension();
                $pathMoa = base_path('files/hapus_buku/moa');
                if (!File::isDirectory($pathMoa)) {
                    File::makeDirectory($pathMoa, 0755, true, true);
                }
                $fileMoa->move($pathMoa, $namaMoa);
            }

            $pengajuan = PengajuanHapusBuku::create([
                'kode_pengajuan' => $kodePengajuan,
                'user_id' => Auth::user()->id,
                'kode_branch' => $request->kode_branch,
                'jenis_aktiva' => $request->jenis_aktiva,
                'alasan' => $request->alasan,
                'surat_moa' => $namaMoa,
                'bulan_hapus_buku' => $request->bulan_hapus_buku,
                'status_approve' => 'admin-head',
            ]);

            $pathKondisi = base_path('files/hapus_buku/kondisi');
            if (!File::isDirectory($pathKondisi)) {
                File::makeDirectory($pathKondisi, 0755, true, true);
            }

            foreach ($request->inventaris_id as $invId) {
                $namaFoto = null;
                if ($request->hasFile("foto_barang.$invId")) {
                    $foto = $request->file("foto_barang.$invId");
                    $namaFoto = time() . '_inv_' . $invId . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $foto->move($pathKondisi, $namaFoto);
                }

                $kondisi = $request->kondisi_barang[$invId] ?? 'Rusak Berat';

                PengajuanHapusBukuDetail::create([
                    'pengajuan_hapus_buku_id' => $pengajuan->id,
                    'inventaris_id' => $invId,
                    'foto' => $namaFoto,
                    'kondisi' => $kondisi,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.pengajuan-hapus-buku.index')
                ->with('success', 'Pengajuan Hapus Buku berhasil dibuat dan diteruskan ke Admin Head.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error store pengajuan hapus buku: ' . $th->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $title = 'Detail Pengajuan Hapus Buku';
        $pengajuan = PengajuanHapusBuku::with(['branch', 'user', 'details.inventaris', 'admin_head', 'akunting'])->findOrFail($id);

        // Hitung ringkasan nilai buku dan penyusutan (mengikuti logika InventarisController)
        $totalHargaPerolehan      = 0;
        $totalAkumulasiPenyusutan = 0;
        $totalNilaiBuku           = 0;

        foreach ($pengajuan->details as $detail) {
            $inv = $detail->inventaris;
            if (!$inv) continue;

            $hargaPerolehan = intval($inv->harga_perolehan);
            $totalHargaPerolehan += $hargaPerolehan;

            $akumulasiDariPenyusutan = $inv->penyusutans()->sum('penyusutan_per_bulan');
            $totalPenyusutanAwal     = floatval($inv->total_penyusutan_awal ?? 0);
            $auditAkumulasi         = ($inv->status_branch === 'AUDIT')
                ? floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0)
                : 0;

            $akumulasi = $totalPenyusutanAwal + $akumulasiDariPenyusutan + $auditAkumulasi;
            $nilaiBuku = max(0, $hargaPerolehan - $akumulasi);

            $totalAkumulasiPenyusutan += $akumulasi;
            $totalNilaiBuku           += $nilaiBuku;
        }

        return view('admin.pengajuan-hapus-buku.show', compact(
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
            $pengajuan = PengajuanHapusBuku::with('details')->findOrFail($id);

            if ($pengajuan->status_approve == 'selesai') {
                return redirect()->back()->with('error', 'Pengajuan yang sudah disetujui selesai tidak dapat dihapus.');
            }

            // Hapus file foto & dokumen
            if ($pengajuan->surat_moa) {
                if (File::exists(base_path('files/hapus_buku/moa/' . $pengajuan->surat_moa))) {
                    File::delete(base_path('files/hapus_buku/moa/' . $pengajuan->surat_moa));
                } elseif (File::exists(public_path('images/hapus-buku/moa/' . $pengajuan->surat_moa))) {
                    File::delete(public_path('images/hapus-buku/moa/' . $pengajuan->surat_moa));
                }
            }

            foreach ($pengajuan->details as $detail) {
                if ($detail->foto) {
                    if (File::exists(base_path('files/hapus_buku/kondisi/' . $detail->foto))) {
                        File::delete(base_path('files/hapus_buku/kondisi/' . $detail->foto));
                    } elseif (File::exists(public_path('images/hapus-buku/kondisi/' . $detail->foto))) {
                        File::delete(public_path('images/hapus-buku/kondisi/' . $detail->foto));
                    }
                }
            }

            $pengajuan->details()->delete();
            $pengajuan->delete();

            DB::commit();

            return redirect()->route('admin.pengajuan-hapus-buku.index')
                ->with('success', 'Pengajuan hapus buku berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error delete pengajuan hapus buku: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pengajuan: ' . $th->getMessage());
        }
    }
}
