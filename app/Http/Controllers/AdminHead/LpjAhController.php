<?php

namespace App\Http\Controllers\AdminHead;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LpjAhController extends Controller
{
    use FileUpload;
    public $validator = [
        'pengajuan_detail_id' => 'required',
        'tanggal_perolehan' => 'required',
        'harga_pembelian' => 'required',
        'keterangan' => 'required',
    ];

    public function index()
    {
        $data['title'] = 'LPJ';
        $data['pengajuan'] = Pengajuan::where('status_approve', 'lpj-admin-head')->get();
        return view('admin-head.lpj.index', $data);
    }

    public function show($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        $data['detail'] = PengajuanDetail::where('pengajuan_id', $id)->first();
        $data['pengajuan_detail'] = PengajuanDetail::where('pengajuan_id', $id)->get();
        return view('admin-head.lpj.detail', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'pengajuan_detail_id' => 'required|exists:pengajuan_details,id',
                'tanggal_perolehan'   => 'required|date',
                'harga_pembelian'     => 'required|numeric|min:0',
                'keterangan'          => 'nullable|string',
                'struk_pembelian'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'lampiran'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];

            $lpj = PengajuanDetail::findOrFail($request->pengajuan_detail_id);

            if (empty($lpj->struk_pembelian)) {
                $rules['struk_pembelian'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();
            $data['status_lpj'] = 'sudah';

            if ($request->hasFile('struk_pembelian')) {
                $gambar = $request->file('struk_pembelian');
                $namafile = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
                $gambar->move('images/lpj/struk_pembelian', $namafile);
                $data['struk_pembelian'] = $namafile;
            }

            if ($request->hasFile('lampiran')) {
                $gambar = $request->file('lampiran');
                $namafileLampiran = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
                $gambar->move('images/lpj/lampiran', $namafileLampiran);
                $data['lampiran'] = $namafileLampiran;
            }

            DB::beginTransaction();
            $lpj->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Data LPJ berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }   

    public function ajukan_lpj(Request $request, $id)
    {
        $ajukan_lpj = Pengajuan::with('pengajuan_details.jenis_peralatan')->findOrFail($id);

        if ($ajukan_lpj->pengajuan_details->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal mengajukan LPJ: Tidak ada item dalam pengajuan ini.');
        }

        // Cek apakah seluruh item sudah dibuatkan LPJ (status_lpj == 'sudah')
        $itemBelum = $ajukan_lpj->pengajuan_details->where('status_lpj', '!=', 'sudah')->count();
        if ($itemBelum > 0) {
            return redirect()->back()->with('error', "Gagal mengajukan LPJ: Masih ada {$itemBelum} item yang belum diinput realisasinya. Silakan selesaikan tombol 'Buat LPJ' pada setiap item.");
        }

        // Cek kelengkapan data per item
        foreach ($ajukan_lpj->pengajuan_details as $item) {
            if (empty($item->tanggal_perolehan) || empty($item->harga_pembelian) || empty($item->struk_pembelian)) {
                $namaItem = $item->jenis_peralatan?->nama_jenis ?? ('Item #' . $item->id);
                return redirect()->back()->with('error', "Gagal mengajukan LPJ: Data tanggal, harga pembelian, atau struk untuk item '{$namaItem}' belum lengkap.");
            }
        }

        // Cek sisa uang muka (pengembalian dana)
        $selisih = $ajukan_lpj->subtotal() - $ajukan_lpj->total_pembelian();
        if ($selisih > 0) {
            if (empty($ajukan_lpj->bukti_pengembalian) || empty($ajukan_lpj->nominal_pengembalian)) {
                return redirect()->back()->with('error', 'Gagal mengajukan LPJ: Terdapat sisa uang muka sebesar Rp ' . number_format($selisih, 0, ',', '.') . '. Harap upload bukti pengembalian dan isi nominal pengembalian terlebih dahulu.');
            }
        }

        $ajukan_lpj->update([
            'status_approve' => 'lpj-akunting-it',
        ]);
        return redirect('admin-head/lpj')->with('success', 'Data LPJ berhasil diajukan.');
    }

    public function upload_bukti_pengembalian(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nominal_pengembalian' => 'required|numeric|min:1',
            'bukti_pengembalian'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan_lpj'       => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lpj = Pengajuan::findOrFail($id);
        $lpj->nominal_pengembalian = $request->nominal_pengembalian;
        $lpj->keterangan_lpj = $request->keterangan_lpj;

        if ($request->hasFile('bukti_pengembalian')) {
            $gambar = $request->file('bukti_pengembalian');
            $namafile = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
            $gambar->move('images/lpj/bukti_pengembalian', $namafile);
            $lpj->bukti_pengembalian = $namafile;
        }

        $lpj->update();
        return redirect()->back()->with('success', 'Bukti pengembalian berhasil diunggah');
    }
}
