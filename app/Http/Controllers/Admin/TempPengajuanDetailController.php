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
            'rekening_id'        => 'required|integer',
            'merk'               => 'required|string|max:50',
            'type'               => 'required|string|max:50',
            'kuantitas'          => 'required|integer|min:1',
            'harga'              => 'required|numeric|min:0',
            'no_telp'            => 'nullable|string|max:20',
            'keterangan'         => 'nullable|string',
            'nama_toko'         => 'nullable|string',
            'pengguna'         => 'nullable|string',
            'kode_branch'        => 'required|string',
            'kode_area'          => 'required|string',
            'kode_user'          => 'required|string',


            'moa'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'struk_pembelian'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ],
        [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'max.string' => ':attribute maksimal :max karakter.',
            'numeric'  => ':attribute harus berupa angka.',
            'integer'  => ':attribute harus berupa bilangan bulat.',
            'date'     => ':attribute harus berupa tanggal.',
            'min.numeric' => ':attribute minimal :min.',
        ],
        [
            'jenis_peralatan_id' => 'Jenis Peralatan',
            'rekening_id' => 'Rekening',
            'merk' => 'Merk',
            'type' => 'Tipe',
            'kuantitas' => 'Kuantitas',
            'harga' => 'Harga',
            'no_telp' => 'No Telp',
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
                $file->move(public_path('files/moa'), $filename);
                $temp->moa = $filename;
            }

            // Upload Struk
            if ($request->hasFile('struk_pembelian')) {
                $file = $request->file('struk_pembelian');
                $filename = uniqid().'_struk.'.$file->getClientOriginalExtension();
                $file->move(public_path('files/struk_pembelian'), $filename);
                $temp->struk_pembelian = $filename;
            }

            $temp->save();

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil dibuat');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('error', 'Data gagal disimpan');
        }
    }


    public function hapus($id)
    {
        $temp_pengajuan_detail = TempPengajuanDetail::find($id);
        $temp_pengajuan_detail->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
