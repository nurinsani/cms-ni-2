<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Admin\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\JenisPeralatan;
use App\Models\Akunting\ParamPeralatan;
use Illuminate\Support\Facades\Validator;
Use Alert;
use App\Models\Admin\TempPengajuanDetail;
use App\Models\Rekening;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade\Pdf;
class PengajuanController extends Controller
{
    public $validator = [
        // 'keterangan' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Pengajuan Pembelian';
        $data['pengajuan'] = Pengajuan::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-peralatan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pengajuan_peralatan()
    {
        $data['title'] = 'Pengajuan Peralatan';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('jenis_bdd', function($query) {
            $query->where('group_inventaris', 'tidak');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-peralatan.tambah-peralatan', $data);
    }

    public function pengajuan_inventaris()
    {
        $data['title'] = 'Pengajuan Inventaris';
        $data['temp_pengajuan'] = TempPengajuanDetail::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['rekening'] = Rekening::all();
        $data['param_peralatan'] = ParamPeralatan::whereHas('jenis_bdd', function($query) {
            $query->where('group_inventaris', 'ya');
        })->get();
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        return view('admin.pengajuan-peralatan.tambah-inventaris', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pengajuan = Pengajuan::latest()->first();
        $kode_branch = auth()->user()->code_area;
        $kode_tahun = date('Y');
        $kode_bulan = date('m');
        $kode_tanggal = date('d');
        // dd($kode_tanggal);

        if ($pengajuan == null) {
            $nomor_urut = '0001';
        } else {
            $nomor_urut = substr($pengajuan->nomor, 16, 4) + 1;
            // $nomor_urut = strval(substr($pengajuan->nomor, 18, 4) + 1);
            // $nomor_urut = substr($pengajuan->nomor, 18, 4) + 1;
            $nomor_urut = "000". $nomor_urut;
            // $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
        }

        $data['nomor'] = $kode_branch.'/'.$kode_tanggal.$kode_bulan.$kode_tahun.'/'.$nomor_urut;
        $data['tanggal_pengajuan'] = Carbon::now(); 
        $data['user_id'] = auth()->user()->id;
        $data['code_area'] = auth()->user()->code_area;
        $data['rekening_id'] = $request->rekening_id;
        if (auth()->user()->code_area == 1010) {
            $data['status_approve'] = 'akunting-it';
        } else {
            $data['status_approve'] = 'admin-head';
        }
        
        $pengajuan = Pengajuan::create($data);
        $temp_pengajuan = TempPengajuanDetail::all();
        
        foreach ($temp_pengajuan as $item) {
            $pengajuan_detail = New PengajuanDetail;
            $pengajuan_detail->pengajuan_id = $pengajuan->id;
            $pengajuan_detail->rekening_id = $item->rekening_id;
            $pengajuan_detail->kode_branch = $item->kode_branch;
            $pengajuan_detail->kode_area = $item->kode_area;
            $pengajuan_detail->kode_user = $item->kode_user;
            $pengajuan_detail->jenis_peralatan_id = $item->jenis_peralatan_id;
            $pengajuan_detail->merk = $item->merk;
            $pengajuan_detail->type = $item->type;
            $pengajuan_detail->kuantitas = $item->kuantitas;
            $pengajuan_detail->harga = $item->harga;
            $pengajuan_detail->nama_toko = $item->nama_toko;
            $pengajuan_detail->no_telp = $item->no_telp;
            $pengajuan_detail->pengguna = $item->pengguna;
            $pengajuan_detail->status_lpj = $item->status_lpj;
            $pengajuan_detail->moa = $item->moa;
            $pengajuan_detail->group_approval = $item->group_approval;
            $pengajuan_detail->keterangan = $item->keterangan;
            $pengajuan_detail->umur = $item->umur;
            $pengajuan_detail->save();
        }
        
        if ($temp_pengajuan == null) {
            return redirect()->back()->with('success', 'Hmmm SAlah!');
        }
        TempPengajuanDetail::query()->delete();

        // $this->sendWhatsAppNotification($pengajuan);

        return redirect()->route('admin.pengajuan.index')->with('success', 'Data berhasil dibuat');
    }

    // private function sendWhatsAppNotification($pengajuan)
    // {
    //     // Format pesan notifikasi
    //     $message = "DARI CMS NURINSANI:\n";
    //     $message .= "Nomor Pengajuan: " . $pengajuan->nomor . "\n";
    //     $message .= "Tanggal Pengajuan: " . $pengajuan->tanggal_pengajuan . "\n";
    //     $message .= "Diajukan oleh: " . auth()->user()->name . "\n";
    //     $message .= "Status: " . $pengajuan->status_approve . "\n";
    //     $message .= "Segera proses pengajuan ini.";

    //     $data = [
    //         'id' => '120363399892113718@g.us', // ID grup WhatsApp
    //         'name' => 'Approve CMS',
    //         'message' => $message,
    //     ];

    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => "192.168.88.151:3000/send-group-message",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30000,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "POST",
    //         CURLOPT_POSTFIELDS => json_encode($data),
    //         CURLOPT_HTTPHEADER => array(
    //             "accept: /",
    //             "accept-language: en-US,en;q=0.8",
    //             "content-type: application/json",
    //         ),
    //     ));

    //     $response = curl_exec($curl);
    //     $err = curl_error($curl);

    //     curl_close($curl);

    //     if ($err) {
    //         Log::error('Gagal mengirim notifikasi WhatsApp: ' . $err);
    //     } else {
    //         Log::info('Notifikasi WhatsApp berhasil dikirim: ' . $response);
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function show(Pengajuan $pengajuan)
    {
        $data['title'] = 'Detail Pengajuan';
        $data['pengajuan'] = $pengajuan;
        return view('admin.pengajuan-peralatan.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pengajuan';
        $data['branch'] = Branch::where('code_area', auth()->user()->code_area)->get();
        $data['param_peralatan'] = ParamPeralatan::all();
        $data['jenis_peralatan'] = JenisPeralatan::all();
        $data['pengajuan'] = Pengajuan::findOrFail($id);
        return view('admin.pengajuan-peralatan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), $this->validator);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $validator->validated();

            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrFail($id);
            $pengajuan->update($data);
            $pengajuan->jenis_peralatans()->delete();
            foreach ($request->nama_jenis as $index => $value) {
                if ($value) {
                    $jenis_peralatan = JenisPeralatan::firstWhere('nama_jenis', $value);
                    $coa = $jenis_peralatan->peralatan->coa;
                    $kuantitas = $request->kuantitas[$index];
                    $harga = $request->harga[$index];
                    $nama_toko = $request->nama_toko[$index];
                    $no_telp = $request->no_telp[$index];
                    $pengguna = $request->pengguna[$index];
                    PengajuanDetail::create([
                        'pengajuan_id' => $pengajuan->id,
                        'nama_jenis' => $value,
                        'coa' => $coa,
                        'kuantitas' => $kuantitas,
                        'harga' => $harga,
                        'nama_toko' => $nama_toko,
                        'no_telp' => $no_telp,
                        'pengguna' => $pengguna
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Pengajuan  $pengajuan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrFail($id);
            $pengajuan->delete();
            DB::commit();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }

    public function daftar_peralatan()
    {
        $data['title'] = 'Daftar Peralatan';
        $data['daftar_peralatan'] = Pengajuan::where('status_proses', 'selesai')->get();
        return view('admin.peralatan.index', $data);
    }

    public function pdf($id)
    {
        $data['title'] = 'Cetak PDF';
        $data['pengajuan'] = Pengajuan::find($id);

        $data = PDF::loadview('admin.pengajuan-peralatan.export-pdf', $data);

    }
}
