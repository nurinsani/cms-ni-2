<?php

namespace App\Http\Controllers\Akunting;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\PengajuanMutasi;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalMutasiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Mutasi';
        $data['mutasi'] = PengajuanMutasi::where('status_approve', 'akunting-it')->get();
        return view('akunting.approval.pengajuan-mutasi.index', $data);
    }

    public function show($id)
    {
        $title = 'Detail Mutasi';
        $pengajuan = PengajuanMutasi::with('inventaris')->findOrFail($id);
        return view('akunting.approval.pengajuan-mutasi.show', compact('pengajuan', 'title'));
    }

    public function approve(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $mutasi = PengajuanMutasi::findOrfail($id);
<<<<<<< HEAD
            $mutasi->status_approve = 'mutasi-selesai';
            $mutasi->atasan_2 = auth()->user()->id;
            $mutasi->save();

            $inventaris = Inventaris::findOrfail($mutasi->peralatan_id);
            $inventaris->kode_branch = $mutasi->ke_cabang;
            $inventaris->update();

            
            $akumulasiPenyusutan = $inventaris->akumulasi_penyusutan_awal() + $inventaris->akumulasi_penyusutan();
            $nilaiBuku = $inventaris->harga_perolehan - $akumulasiPenyusutan;
            
            // dd($nilaiBuku);
            // dd($akumulasiPenyusutan);
            
            // jurnal berkurang
            $kode_transaksi = date('m').random_int(100000, 999999);
            TransaksiAp::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                'kode_rekening' => '1652000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => 0,
                'kredit' => $inventaris->harga_perolehan
            ]);

            TransaksiAp::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                'kode_rekening' => '1672000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => $akumulasiPenyusutan,
                'kredit' => 0
            ]);

            TransaksiAp::create([
                'kode_branch' => $mutasi->dari_cabang,
                'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => $nilaiBuku,
                'kredit' => 0
            ]);

            // jurnal bertambah
            $kode_transaksi = date('m').random_int(100000, 999999);
            TransaksiAp::create([
                'kode_branch' => $inventaris->kode_branch,
                'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                'kode_rekening' => '1652000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => $inventaris->harga_perolehan,
                'kredit' => 0
            ]);

            TransaksiAp::create([
                'kode_branch' => $inventaris->kode_branch,
                'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                'kode_rekening' => '1672000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => 0,
                'kredit' => $akumulasiPenyusutan
            ]);

            TransaksiAp::create([
                'kode_branch' => $inventaris->kode_branch,
                'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                'debet' => 0,
                'kredit' => $nilaiBuku
            ]);
=======
            $mutasi->status_approve = 'selesai';
            $mutasi->atasan_2 = auth()->user()->id;
            $mutasi->save();

            // Update kode_branch untuk setiap inventaris yang dimutasi
            foreach ($mutasi->inventaris as $inventaris) {
                $inventaris->kode_branch = $mutasi->ke_cabang;
                $inventaris->save();

                $akumulasiPenyusutan = $inventaris->akumulasi_penyusutan_awal() + $inventaris->akumulasi_penyusutan();
                $nilaiBuku = $inventaris->harga_perolehan - $akumulasiPenyusutan;
                
                // dd($nilaiBuku);
                // dd($akumulasiPenyusutan);

                // jurnal KP
                $kode_transaksi = date('m').random_int(100000, 999999);
                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'. '/' .'1010' . '/' . $kode_transaksi,
                    'kode_rekening' => '1010-1652000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => $inventaris->harga_perolehan,
                    'kredit' => 0
                ]);
    
                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'. '/' . '1010' . '/' . $kode_transaksi,
                    'kode_rekening' => '1010-1672000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => 0,
                    'kredit' => $inventaris->harga_perolehan,
                ]);

                
                // jurnal berkurang di AP
                $kode_transaksi = date('m').random_int(100000, 999999);
                TransaksiAp::create([
                    'kode_branch' => $mutasi->dari_cabang,
                    'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                    'kode_rekening' => '1652000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => 0,
                    'kredit' => $inventaris->harga_perolehan
                ]);
    
                TransaksiAp::create([
                    'kode_branch' => $mutasi->dari_cabang,
                    'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                    'kode_rekening' => '1672000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => $akumulasiPenyusutan,
                    'kredit' => 0
                ]);
    
                TransaksiAp::create([
                    'kode_branch' => $mutasi->dari_cabang,
                    'kode_transaksi' => 'BU'. '/' . $mutasi->dari_cabang . '/' . $kode_transaksi,
                    'kode_rekening' => '2500000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => $nilaiBuku,
                    'kredit' => 0
                ]);
    
                // jurnal bertambah di AP
                $kode_transaksi = date('m').random_int(100000, 999999);
                TransaksiAp::create([
                    'kode_branch' => $inventaris->kode_branch,
                    'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                    'kode_rekening' => '1652000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => $inventaris->harga_perolehan,
                    'kredit' => 0
                ]);
    
                TransaksiAp::create([
                    'kode_branch' => $inventaris->kode_branch,
                    'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                    'kode_rekening' => '1672000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => 0,
                    'kredit' => $akumulasiPenyusutan
                ]);
    
                TransaksiAp::create([
                    'kode_branch' => $inventaris->kode_branch,
                    'kode_transaksi' => 'BU'. '/' .$inventaris->kode_branch . '/' . $kode_transaksi,
                    'kode_rekening' => '2500000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'Mutasi' . ' ' . $inventaris->keterangan ?? '-',
                    'debet' => 0,
                    'kredit' => $nilaiBuku
                ]);
            }
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }
    }
}
