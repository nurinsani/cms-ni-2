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

    public function detail($id)
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
            $validator = Validator::make($request->all(), $this->validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();
            $data['status_lpj'] = 'sudah';

            if ($request->hasFile('struk_pembelian')) {
                $gambar = $request->struk_pembelian;
                $namafile = time().'.'.$gambar->getClientOriginalExtension();
                $gambar->move('images/lpj/struk_pembelian', $namafile);

            }

            $data['struk_pembelian'] = $namafile;

            if ($request->hasFile('lampiran')) {
                $gambar = $request->lampiran;
                $namafile = time().'.'.$gambar->getClientOriginalExtension();
                $gambar->move('images/lpj/lampiran', $namafile);

            }

            $data['lampiran'] = $namafile;


            DB::beginTransaction();
            $lpj = PengajuanDetail::find($data['pengajuan_detail_id']);
            $lpj->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }   

    public function ajukan_lpj(Request $request, $id)
    {
        $ajukan_lpj = Pengajuan::findOrFail($id);
        // if ($ajukan_lpj->tanggal_pembelian == NULL) {
        //     return redirect()->back()->with('error', 'Anda belum buat LPJ.');
        // }
        
        $ajukan_lpj->update([
            'status_approve' => 'lpj-akunting-it',
            // 'nominal_kurang' => $ajukan_lpj->subtotal() - $ajukan_lpj->total_pembelian()
        ]);
        // if (auth()->user()->code_area == 1010) {
        //     $ajukan_lpj->update([
        //         'status_approve' => 'lpj-atasan-akunting',
        //     ]);
        // } else {

        // }
        return redirect('admin-head/lpj')->with('success', 'Data sudah di Ajukan');
    }

    public function upload_bukti_pengembalian(Request $request, $id)
    {

        $lpj = Pengajuan::find($id);
        $lpj->nominal_pengembalian = $request->nominal_pengembalian;
        $lpj->keterangan_lpj = $request->keterangan_lpj;

        if ($request->hasFile('bukti_pengembalian')) {
            $gambar = $request->bukti_pengembalian;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/lpj/bukti_pengembalian', $namafile);

            // $data['struk_pembelian'] = $this->upload_file($request->file('struk_pembelian'), 'lpj/struk-pembelian');
        }

        $lpj->bukti_pengembalian = $namafile;
        $lpj->update();
        return redirect()->back()->with('success', 'Data berhasil diubah');

    }
}
