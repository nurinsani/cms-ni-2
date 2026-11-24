<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Lpj;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanDetail;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Validator;

class LpjController extends Controller
{
    use FileUpload;
    public $validator = [
        'pengajuan_detail_id' => 'required',
        'tanggal_perolehan' => 'required',
        'harga_pembelian' => 'required',
        'keterangan_lpj' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Laporan Pertanggungjawaban (LPJ)';
        $codeArea = auth()->user()->code_area;
        $query = Pengajuan::where('status_approve', 'lpj-admin');
        if (!empty($codeArea)) {
            $query->where('code_area', $codeArea);
        }
        $data['pengajuan'] = $query->latest('id')->get();
        return view('admin.lpj.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Tambah LPJ';
        $data['pengajuan'] = Pengajuan::all();
        return view('admin.lpj.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data['status_lpj'] = 'sudah';
            if ($request->hasFile('struk_pembelian')) {
                $data['struk_pembelian'] = $this->upload_file($request->file('struk_pembelian'), 'lpj/struk-pembelian');
            }

            if ($request->hasFile('bukti_pengembalian')) {
                $data['bukti_pengembalian'] = $this->upload_file($request->file('bukti_pengembalian'), 'lpj/bukti-pengembalian');
            }
            DB::beginTransaction();
            Lpj::create($data);
            
            $pengajuan_detail = PengajuanDetail::find($data['pengajuan_detail_id']);
            $pengajuan_detail->status_lpj = 'sudah';
            $pengajuan_detail->update();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil dibuat');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dibuat');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Lpj  $lpj
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        $data['detail'] = PengajuanDetail::where('pengajuan_id', $id)->first();
        $data['pengajuan_detail'] = PengajuanDetail::where('pengajuan_id', $id)->get();
        return view('admin.lpj.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Lpj  $lpj
     * @return \Illuminate\Http\Response
     */
    public function edit(Lpj $lpj)
    {
        $data['title'] = 'Edit LPJ';
        $data['lpj'] = $lpj;
        $data['pengajuan'] = Pengajuan::all();
        return view('admin.lpj.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Lpj  $lpj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'pengajuan_detail_id' => 'required|exists:pengajuan_details,id',
                'tanggal_perolehan'   => 'required|date',
                'harga_pembelian'     => 'required|numeric|min:0',
                'keterangan_lpj'      => 'nullable|string',
                'struk_pembelian'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
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

    public function upload_bukti_pengembalian(Request $request, $id)
    {
        $lpj = Pengajuan::findOrFail($id);

        $rules = [
            'nominal_pengembalian' => 'required|numeric|min:1',
            'keterangan_lpj'       => 'nullable|string|max:255',
            'bukti_pengembalian'   => $lpj->bukti_pengembalian ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lpj->nominal_pengembalian = $request->nominal_pengembalian;
        $lpj->keterangan_lpj = $request->keterangan_lpj;

        if ($request->hasFile('bukti_pengembalian')) {
            $gambar = $request->file('bukti_pengembalian');
            $namafile = time() . '_' . uniqid() . '.' . $gambar->getClientOriginalExtension();
            $gambar->move('images/lpj/bukti_pengembalian', $namafile);
            $lpj->bukti_pengembalian = $namafile;
        }

        $lpj->update();
        return redirect()->back()->with('success', 'Bukti pengembalian berhasil disimpan');
    }

    public function upload_bukti_pembelian_kurang(Request $request, $id)
    {

        $lpj = Pengajuan::find($id);
        $lpj->nominal_kurang = $request->nominal_kurang;
        $lpj->keterangan_lpj = $request->keterangan_lpj;

        if ($request->hasFile('bukti_kurang')) {
            $gambar = $request->bukti_kurang;
            $namafile = time().'.'.$gambar->getClientOriginalExtension();
            $gambar->move('images/lpj/bukti_kurang', $namafile);

            // $data['struk_pembelian'] = $this->upload_file($request->file('struk_pembelian'), 'lpj/struk-pembelian');
        }

        $lpj->bukti_kurang = $namafile;
        $lpj->update();
        return redirect()->back()->with('success', 'Data berhasil diubah');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Lpj  $lpj
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lpj $lpj)
    {
        try {
            DB::beginTransaction();
            $lpj->delete();
            DB::commit();
            return redirect()->route('admin.lpj.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function sudah_approve(Request $request, $id)
    {
        $sudah_approve = PengajuanDetail::findOrFail($id);
        $sudah_approve->update([
            'status_lpj' => 'sudah'
        ]);
        return redirect()->back()->with('success', 'Data sudah di LPJ');
    }

    public function ajukan_lpj(Request $request, $id)
    {
        $ajukan_lpj = Pengajuan::with('pengajuan_details.jenis_peralatan')->findOrFail($id);

        if ($ajukan_lpj->status_approve != 'lpj-admin') {
            return redirect()->back()->with('error', 'LPJ sudah pernah diajukan atau status pengajuan tidak valid.');
        }

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

        if (auth()->user()->code_area == 1010) {
            $ajukan_lpj->update([
                'status_approve' => 'lpj-akunting-it',
            ]);
        } else {
            $ajukan_lpj->update([
                'status_approve' => 'lpj-admin-head',
            ]);
        }
        return redirect()->route('admin.lpj.index')->with('success', 'Data LPJ berhasil diajukan.');
    }
}
