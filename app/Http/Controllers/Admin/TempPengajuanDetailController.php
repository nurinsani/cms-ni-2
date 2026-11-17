<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use Illuminate\Http\Request;
use App\Models\Admin\TempPengajuanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TempPengajuanDetailController extends Controller
{
    public function index()
    {
        
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate(
        [
            'jenis_peralatan_id' => 'required|integer',
            'rekening_id'        => 'nullable|integer',
            'merk'               => 'required|string|max:50',
            'type'               => 'required|string|max:50',
            'kuantitas'          => 'required|integer|min:1',
            'harga'              => 'required|numeric|min:0',
            'no_telp'            => 'nullable|string|max:20',
            'keterangan'         => 'nullable|string',
            'nama_toko'          => 'nullable|string',
            'pengguna'           => 'nullable|string',
            'kode_branch'        => 'nullable|string',
            'kode_area'          => 'nullable|string',
            'kode_user'          => 'nullable|string',
            'tanggal_pembelian'  => 'nullable|date',

            'moa'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'struk_pembelian'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ],
        [
            'required'    => ':attribute wajib diisi.',
            'string'      => ':attribute harus berupa teks.',
            'max.string'  => ':attribute maksimal :max karakter.',
            'max.file'    => ':attribute maksimal berukuran :max KB (2 MB).',
            'mimes'       => ':attribute harus berupa file bertipe: :values.',
            'numeric'     => ':attribute harus berupa angka.',
            'integer'     => ':attribute harus berupa bilangan bulat.',
            'date'        => ':attribute harus berupa tanggal.',
            'min.numeric' => ':attribute minimal :min.',
            'min.integer' => ':attribute minimal :min.',
        ],
        [
            'jenis_peralatan_id' => 'Jenis Peralatan / Inventaris',
            'rekening_id'        => 'Rekening',
            'merk'               => 'Merk',
            'type'               => 'Tipe / Model',
            'kuantitas'          => 'Kuantitas',
            'harga'              => 'Harga Satuan',
            'no_telp'            => 'No. Telepon Toko',
            'nama_toko'          => 'Nama Toko / Vendor',
            'pengguna'           => 'Nama Pengguna',
            'keterangan'         => 'Keterangan',
            'moa'                => 'Berkas MOA',
            'struk_pembelian'    => 'Struk Pembelian',
            'kode_branch'        => 'Unit / AP',
            'kode_area'          => 'Cabang / Area',
        ]);

        DB::beginTransaction();

        try {
            $temp = new TempPengajuanDetail();
            $temp->fill($validated);

            $temp->status_lpj = 'belum';
            $temp->user_id = auth()->id();

            // Upload MOA
            if ($request->hasFile('moa')) {
                $file = $request->file('moa');
                $filename = uniqid().'_moa.'.$file->getClientOriginalExtension();
                $file->move('files/moa', $filename);
                $temp->moa = $filename;
            }

            // Upload Struk
            if ($request->hasFile('struk_pembelian')) {
                $file = $request->file('struk_pembelian');
                $filename = uniqid().'_struk.'.$file->getClientOriginalExtension();
                $file->move('files/struk_pembelian', $filename);
                $temp->struk_pembelian = $filename;
            }

            $temp->save();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item berhasil ditambahkan ke daftar'
                ]);
            }

            return redirect()->back()->with('success', 'Data berhasil dibuat');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan: ' . $th->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Data gagal disimpan');
        }
    }


    public function hapus($id)
    {
        $temp_pengajuan_detail = TempPengajuanDetail::find($id);
        if ($temp_pengajuan_detail) {
            $temp_pengajuan_detail->delete();
        }

        return redirect()->back()->with('success', 'Item berhasil dihapus dari daftar sementara.');
    }
}
