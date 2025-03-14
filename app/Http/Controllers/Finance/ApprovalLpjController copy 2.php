<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\HistoryApprove;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApprovalLpjController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengajuan LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'lpj-finance')->get();
        // $data['lpj'] = Pengajuan::where('status_approve', 'selesai')->get();
        return view('finance.approval.pengajuan-lpj.index', $data);
    }

    public function detail($id)
    {
        $data['title'] = 'Detail LPJ';
        $data['lpj'] = Pengajuan::findOrFail($id);
        return view('finance.approval.pengajuan-lpj.detail', $data);
    }

    public function detail_cetak($id)
    {
        $data['title'] = 'Detail Cetak LPJ';
        $data['lpj'] = $lpj = Pengajuan::findOrFail($id);

        if ($lpj->approval1) {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }
            
            $data['diajukan'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->user->id.'/'.$nomor_urut;
            $data['kode_approve1'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval1->id.'/'.$nomor_urut;
            $data['kode_approve2'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval2->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval4->id.'/'.$nomor_urut;
        } else {
            $history_approve = HistoryApprove::latest()->first();
            if ($history_approve == null) {
                $nomor_urut = '0001';
            } else {
                $nomor_urut = substr($history_approve->barcode, 24, 4) + 1;
                $nomor_urut = str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
            }

            $data['diajukan'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->user->id.'/'.$nomor_urut;
            $data['kode_approve3'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval3->id.'/'.$nomor_urut;
            $data['kode_approve4'] = $kode_approve4 = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval4->id.'/'.$nomor_urut;
            $data['kode_approve5'] = $lpj->id.'/'.$lpj->updated_at->format('Y-m-d-Hid').'/'.$lpj->approval5->id.'/'.$nomor_urut;

        }

        HistoryApprove::updateOrCreate([
            'pengajuan_id' => $lpj->id,
            'user_id' => $lpj->user_id,
            'barcode' => $kode_approve4,
        ]);

        $lpj->update([
            'status_cetak' => 'sudah-cetak-fpd',
        ]);

        return view('finance.approval.pengajuan-lpj.detail', $data);
    }

    public function approve(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrfail($id);
<<<<<<< HEAD
        $pengajuan->status_approve = 'selesai';
=======
        $pengajuan->status_approve = 'lpj-finance';
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
        $pengajuan->save();
        
        foreach ($pengajuan->pengajuan_details as $key => $cabang) {
            
        }
            // jurnal LPJ normal
            if ($pengajuan->subtotal() == $pengajuan->total_pembelian()) {                
                if ($cabang->kode_branch == NULL) {
                    foreach ($pengajuan->pengajuan_details as $key => $cabang) {
                        $subtotal = 0;
                        $total = $cabang->harga * $cabang->kuantitas;
                        $subtotal = $subtotal + $total;
                    }
                    
                    // Jurnal LPJ Pembelian Peralatan Cabang
                    $branch = Branch::where('code_area', $cabang->kode_area)
                    ->where('status_aktif', 'aktif')
                    ->get();
                    $total = $branch->count();

                    // jurnal LPJ pembelian untuk cabang di KP
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    foreach ($branch as $key => $data) {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010'.'-'.$data->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $cabang->keterangan,
                            'debet' => $cabang->harga_pembelian / $total,
                            'kredit' => 0
                        ]);
                    }
        
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $cabang->keterangan,
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
                    // end jurnal LPJ pembelian untuk cabang di KP
                    
                    // jurnal LPJ   pembelian untuk cabang di AP
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    foreach ($branch as $key => $data) {
                        TransaksiAp::create([
                            'kode_branch' => $data->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $cabang->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $cabang->keterangan,
                            'debet' => $cabang->harga_pembelian / $total,
                            'kredit' => 0
                        ]);

                        TransaksiAp::create([
                            'kode_branch' => $data->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $cabang->keterangan,
                            'debet' => 0,
                            'kredit' => $cabang->harga_pembelian / $total
                        ]);
                    }
                    
                } else {
                    // jurnal di KP
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    foreach ($pengajuan->pengajuan_details as $item) {
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $item->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
        
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
                            'debet' => 0,
                            'kredit' => $item->harga_pembelian,
                        ]);
                    }

                    // Jurnal Pembelian Untuk KP
                    // $kode_transaksi_ap = date('m').random_int(100000, 999999);
                    foreach ($pengajuan->pengajuan_details as $item) {
                        $subtotal = 0;
                        $total = $item->harga * $item->kuantitas;
                        $subtotal = $subtotal + $total;

                        // jurnal di KP
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-'.$item->ap->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
                    }

                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $item->keterangan,
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
                }
                // end jurnal pembelian untuk cabang KP
                // end Jurnal Pembelian Peralatan Cabang
            }
            // end LPJ normal
            
            // jurnal LPJ pembelian lebih besar dari pengajuan
            if ($pengajuan->subtotal() < $pengajuan->total_pembelian()) {
<<<<<<< HEAD
                
                if ($cabang->kode_branch == NULL) {
                    // Jurnal Pembelian Peralatan Cabang
                    foreach ($pengajuan->pengajuan_details as $key => $cabang) {
                        $subtotal = 0;
                        $total = $cabang->harga * $cabang->kuantitas;
                        $subtotal = $subtotal + $total;
                    }
                    
                    $branch = Branch::where('code_area', $cabang->kode_area)
                    ->where('status_aktif', 'aktif')
                    ->get();
                    $total = $branch->count();
                    // jurnal pembelian untuk cabang KP
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    foreach ($branch as $key => $data) {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010'.'-'.$data->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK Aktiva - Unit'.' '.$data->unit,
                            'debet' => $cabang->harga_pembelian / $total,
                            'kredit' => 0
                        ]);
                    }
        
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'UM Pembelian Barang',
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);

                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1312000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'PPBL-Giro Bank BSI Ex BNI Syariah - 8080887729�-�8080887730',
                        'debet' => 0,
                        'kredit' => $cabang->harga_pembelian - $pengajuan->subtotal()
                    ]);
                    // end jurnal pembelian untuk cabang KP
                    
                    // jurnal pembelian untuk cabang AP
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    foreach ($branch as $key => $data) {
                        TransaksiAp::create([
                            'kode_branch' => $data->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $cabang->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $cabang->jenis_peralatan->param_peralatan->nama,
                            'debet' => $cabang->harga_pembelian / $total,
                            'kredit' => 0
                        ]);

                        TransaksiAp::create([
                            'kode_branch' => $data->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK PASIVA - KP',
                            'debet' => 0,
                            'kredit' => $cabang->harga_pembelian / $total
                        ]);
                    }
                    
                } else {
                    foreach ($pengajuan->pengajuan_details as $item) {
                        $subtotal = 0;
                        $total = $item->harga * $item->kuantitas;
                        $subtotal = $subtotal + $total;
                        
                        // jurnal di AP
                        $kode_transaksi = date('m').random_int(100000, 999999);
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $item->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->jenis_peralatan->param_peralatan->nama,
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
        
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK PASIVA - KP',
                            'debet' => 0,
                            'kredit' => $item->harga_pembelian,
                        ]);
                        
                    }
        
                    foreach ($pengajuan->pengajuan_details as $item) {
                        // jurnal di KP
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-'.$item->ap->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'RAK Aktiva - Unit'.' '.ucfirst($item->ap->unit),
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
    
                    }
    
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'UM Pembelian Barang',
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
                    
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1312000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'PPBL-Giro Bank BSI Ex BNI Syariah - 8080887729�-�8080887730',
                        'debet' => 0,
                        'kredit' => $item->harga_pembelian - $pengajuan->subtotal()
                    ]);
                }
            }
            // end jurnal LPJ pengajuan lebih besar dari pembelian

            // jurnal LPJ pembelian lebih kecil dari pengajuan



            if ($pengajuan->subtotal() > $pengajuan->total_pembelian()) {
                foreach ($pengajuan->pengajuan_details as $item) {
                    // Jika keduanya NULL, jalankan ini bro...
                    if (is_null($item->kode_branch) && is_null($item->kode_area)) {
                        $branch = Branch::where('code_area', $item->kode_area)
                            ->where('status_aktif', 'aktif')
                            ->get();
            
                        $totalBranch = $branch->count();
                        $kode_transaksi = date('m') . random_int(100000, 999999);
            
                        foreach ($branch as $data) {
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-' . $data->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => $item->harga_pembelian / $totalBranch,
                                'kredit' => 0
                            ]);
                        }
            
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                            'kode_rekening' => '1010-1311000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
                            'debet' => $pengajuan->nominal_pengembalian,
                            'kredit' => 0
                        ]);
            
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                            'kode_rekening' => '1010-1929000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
=======

                foreach ($pengajuan->pengajuan_details as $row) {

                    $kode_transaksi_kp = date('m').random_int(100000, 999999);

                    $harga_kurang = $row->harga_pembelian - $pengajuan->subtotal();

                    if ($row->kode_branch == '1010') {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-'.$row->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => $row->harga_pembelian,
                            'kredit' => 0
                        ]);
    
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1312000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => 0,
                            'kredit' => $harga_kurang
                        ]);
    
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1929000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => 0,
                            'kredit' => $pengajuan->subtotal()
                        ]);
    
                    } else {
                        $kode_transaksi_kp = date('m').random_int(100000, 999999);
                            
                        $area = Branch::where('code_area', $row->kode_area)
                            ->where('status_aktif', 'aktif')
                            ->get();
                        $total_branch = $area->count();
                        
                        if ($row->kode_branch == null) {
                            foreach ($area as $key => $value) {
                                TransaksiKp::create([
                                    'kode_branch' => 1010,
                                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                                    'kode_rekening' => '1010-'.$value->GL,
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => $row->harga_pembelian / $total_branch,
                                    'kredit' => 0 
                                ]);
                            }
                        } else {
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                                'kode_rekening' => '1010-'.$row->ap->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $row->keterangan,
                                'debet' => $row->harga_pembelian,
                                'kredit' => 0
                            ]);
                        }

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1312000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => 0,
                            'kredit' => $harga_kurang,
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1929000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                            'debet' => 0,
                            'kredit' => $pengajuan->subtotal()
                        ]);

<<<<<<< HEAD
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                            'kode_rekening' => '1010-' . $data->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $item->keterangan,
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
                    } else {
                        // jurnal KP
                        if (!is_null($item->kode_area)) { // Cek apakah kode_area tidak NULL
                            
                            $branch = Branch::where('code_area', $item->kode_area)
                                ->where('status_aktif', 'aktif')
                                ->get();
                
                            $totalBranch = $branch->count();
                            $kode_transaksi = date('m') . random_int(100000, 999999);
                
                            foreach ($branch as $data) {
                                TransaksiKp::create([
                                    'kode_branch' => 1010,
                                    'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                    'kode_rekening' => '1010-' . $data->GL,
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $item->keterangan,
                                    'debet' => $item->harga_pembelian / $totalBranch,
                                    'kredit' => 0
                                ]);
                            }
                
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-1311000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => $pengajuan->nominal_pengembalian,
                                'kredit' => 0
                            ]);
                
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-1929000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => 0,
                                'kredit' => $pengajuan->subtotal()
                            ]);
    
                        } else {
                            // elseif ($item->kode_area == NULL) {
                            // Proses untuk kode_area NULL
                            $kode_transaksi = date('m') . random_int(100000, 999999);
    
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-' . $item->ap->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => $item->harga_pembelian,
                                'kredit' => 0
                            ]);
    
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-1311000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => $pengajuan->nominal_pengembalian,
                                'kredit' => 0
                            ]);
                
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU ' . '/1010/' . $kode_transaksi,
                                'kode_rekening' => '1010-1929000',
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $item->keterangan,
                                'debet' => 0,
                                'kredit' => $pengajuan->subtotal()
                            ]);
                        }
                        // end jurnal KP
                    }
                }
            }
            

                     
=======
                        // jurnal untuk AP
                        foreach ($pengajuan->pengajuan_details as $row) {
                            $kode_transaksi_kp = date('m').random_int(100000, 999999);
                                
                            $area = Branch::where('code_area', $row->kode_area)
                                ->where('status_aktif', 'aktif')
                                ->get();
                            $total_branch = $area->count();
                            
                            if ($row->kode_branch == null) {
                                foreach ($area as $key => $value) {
                                    TransaksiAp::create([
                                        'kode_branch' => $value->kode_branch,
                                        'kode_transaksi' => 'BU'.'/'. $value->kode_branch .'/'.$kode_transaksi_kp,
                                        'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                                        'tanggal_transaksi' => Carbon::now(),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => $row->keterangan,
                                        'debet' => $row->harga_pembelian / $total_branch,
                                        'kredit' => 0 
                                    ]);
        
                                    TransaksiAp::create([
                                        'kode_branch' => $value->kode_branch,
                                        'kode_transaksi' => 'BU'.'/'. $value->kode_branch .'/'.$kode_transaksi_kp,
                                        'kode_rekening' => '2500000',
                                        'tanggal_transaksi' => Carbon::now(),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => $row->keterangan,
                                        'debet' => 0,
                                        'kredit' => $row->harga_pembelian / $total_branch 
                                    ]);
                                }
                            } else {
                                TransaksiAp::create([
                                    'kode_branch' => $row->ap->kode_branch,
                                    'kode_transaksi' => 'BU'.'/'. $row->ap->kode_branch .'/'.$kode_transaksi_kp,
                                    'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => $row->harga_pembelian,
                                    'kredit' => 0
                                ]);
        
                                TransaksiAp::create([
                                    'kode_branch' => $row->ap->kode_branch,
                                    'kode_transaksi' => 'BU'.'/'. $row->ap->kode_branch .'/'.$kode_transaksi_kp,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => 0,
                                    'kredit' => $row->harga_pembelian
                                ]);
                            }
                        }
                    }

                }

            }
            // end jurnal LPJ pengajuan lebih besar dari pembelian


            // jurnal LPJ pembelian lebih kecil dari total pengajuan
            if ($pengajuan->subtotal() > $pengajuan->total_pembelian()) {

                // jurnal untuk KP
                foreach ($pengajuan->pengajuan_details as $row) {

                    $kode_transaksi_kp = date('m').random_int(100000, 999999);

                    if ($row->kode_branch == '1010') {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-'.$row->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => $row->harga_pembelian,
                            'kredit' => 0
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1311000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => $pengajuan->nominal_pengembalian,
                            'kredit' => 0
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1929000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => 0,
                            'kredit' => $pengajuan->subtotal()
                        ]);

                    } else {

                        $kode_transaksi_kp = date('m').random_int(100000, 999999);
                            
                        $area = Branch::where('code_area', $row->kode_area)
                            ->where('status_aktif', 'aktif')
                            ->get();
                        $total_branch = $area->count();
                        
                        if ($row->kode_branch == null) {
                            foreach ($area as $key => $value) {
                                TransaksiKp::create([
                                    'kode_branch' => 1010,
                                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                                    'kode_rekening' => '1010-'.$value->GL,
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => $row->harga_pembelian / $total_branch,
                                    'kredit' => 0 
                                ]);
                            }
                        } else {
                            TransaksiKp::create([
                                'kode_branch' => 1010,
                                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                                'kode_rekening' => '1010-'.$row->ap->GL,
                                'tanggal_transaksi' => Carbon::now(),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => $row->keterangan,
                                'debet' => $row->harga_pembelian,
                                'kredit' => 0
                            ]);
                        }

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1311000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => $pengajuan->nominal_pengembalian,
                            'kredit' => 0
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi_kp,
                            'kode_rekening' => '1010-1929000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => $row->keterangan,
                            'debet' => 0,
                            'kredit' => $pengajuan->subtotal()
                        ]);

                        // jurnal untuk AP
                        foreach ($pengajuan->pengajuan_details as $row) {
                            $kode_transaksi_kp = date('m').random_int(100000, 999999);
                                
                            $area = Branch::where('code_area', $row->kode_area)
                                ->where('status_aktif', 'aktif')
                                ->get();
                            $total_branch = $area->count();
                            
                            if ($row->kode_branch == null) {
                                foreach ($area as $key => $value) {
                                    TransaksiAp::create([
                                        'kode_branch' => $value->kode_branch,
                                        'kode_transaksi' => 'BU'.'/'. $value->kode_branch .'/'.$kode_transaksi_kp,
                                        'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                                        'tanggal_transaksi' => Carbon::now(),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => $row->keterangan,
                                        'debet' => $row->harga_pembelian / $total_branch,
                                        'kredit' => 0 
                                    ]);
        
                                    TransaksiAp::create([
                                        'kode_branch' => $value->kode_branch,
                                        'kode_transaksi' => 'BU'.'/'. $value->kode_branch .'/'.$kode_transaksi_kp,
                                        'kode_rekening' => '2500000',
                                        'tanggal_transaksi' => Carbon::now(),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => $row->keterangan,
                                        'debet' => 0,
                                        'kredit' => $row->harga_pembelian / $total_branch 
                                    ]);
                                }
                            } else {
                                TransaksiAp::create([
                                    'kode_branch' => $row->ap->kode_branch,
                                    'kode_transaksi' => 'BU'.'/'. $row->ap->kode_branch .'/'.$kode_transaksi_kp,
                                    'kode_rekening' => $row->jenis_peralatan->param_peralatan->coa,
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => $row->harga_pembelian,
                                    'kredit' => 0
                                ]);
        
                                TransaksiAp::create([
                                    'kode_branch' => $row->ap->kode_branch,
                                    'kode_transaksi' => 'BU'.'/'. $row->ap->kode_branch .'/'.$kode_transaksi_kp,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => Carbon::now(),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => $row->keterangan,
                                    'debet' => 0,
                                    'kredit' => $row->harga_pembelian
                                ]);
                            }
                        }
                    }
                }

            }
            // jurnal LPJ pembelian lebih kecil dari total pengajuan

>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747

        try {
            DB::beginTransaction();
            

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }

    }

    public function approve_inventaris(Request $request, $id)
    {
        DB::beginTransaction();
        $pengajuan = Pengajuan::findOrfail($id);
<<<<<<< HEAD
        $pengajuan->status_approve = 'selesai';
=======
        $pengajuan->status_approve = 'lpj-finance';
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
        $pengajuan->save();

        foreach ($pengajuan->pengajuan_details as $key => $cabang) {
        
        }

        // jurnal normal
        if ($pengajuan->subtotal() == $pengajuan->total_pembelian()) {
            // jurnal LPJ pembelian inventaris di kp
            $kode_transaksi = date('m').random_int(100000, 999999);
            foreach ($pengajuan->pengajuan_details as $item) {
                if ($item->kode_branch == '1010') {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-'.$item->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $item->keterangan_lpj,
                        'debet' => $item->harga_pembelian,
                        'kredit' => 0
                    ]);
                } else {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-'.$item->ap->GL,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $item->keterangan_lpj,
                        'debet' => $item->harga_pembelian,
                        'kredit' => 0
                    ]);
                }
            }

            TransaksiKp::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                'kode_rekening' => '1010-1929000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => $item->keterangan_lpj,
                'debet' => 0,
                'kredit' => $pengajuan->subtotal()
            ]);
            // end jurnal LPJ pembelian inventaris di kp

            $kode_transaksi = date('m').random_int(100000, 999999);
            if ($item->kode_branch !== '1010') {
                // jurnal LPJ pembelian inventaris di ap
                foreach ($pengajuan->pengajuan_details as $item) {
                    TransaksiAp::create([
                        'kode_branch' => $item->ap->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => $item->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => $item->keterangan,
=======
                        'keterangan' => $item->keterangan_lpj,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => $item->harga_pembelian,
                        'kredit' => 0
                    ]);
    
                    TransaksiAp::create([
                        'kode_branch' => $item->ap->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => '2500000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => $item->keterangan,
=======
                        'keterangan' => $item->keterangan_lpj,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => 0,
                        'kredit' => $item->harga_pembelian,
                    ]);
                }
                // end jurnal LPJ pembelian inventaris di ap
            }

        }
        // end jurnal normal

        // jika pembelian lebih kecil dari pengajuan
        if ($pengajuan->subtotal() > $pengajuan->total_pembelian()) {
<<<<<<< HEAD
            if (!is_null($cabang->kode_area)) {
=======
            if ($cabang->kode_branch == NULL) {
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                // Jurnal Pembelian Peralatan Cabang
                foreach ($pengajuan->pengajuan_details as $key => $cabang) {
                    $subtotal = 0;
                    $total = $cabang->harga * $cabang->kuantitas;
                    $subtotal = $subtotal + $total;
                }

                $branch = Branch::where('code_area', $cabang->kode_area)
                ->where('status_aktif', 'aktif')
                ->get();
                $total = $branch->count();
                // jurnal pembelian untuk cabang KP
                $kode_transaksi = date('m').random_int(100000, 999999);
                foreach ($branch as $key => $data) {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010'.'-'.$data->GL,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
=======
                        'keterangan' => $cabang->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => $cabang->harga_pembelian / $total,
                        'kredit' => 0
                    ]);
                }
                
                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1311000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                    'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
=======
                    'keterangan' => 'LPJ ' . $cabang->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                    'debet' => $pengajuan->nominal_pengembalian,
                    'kredit' => 0
                ]);

                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1929000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                    'keterangan' => 'LPJ' . ' '. $cabang->keterangan,
=======
                    'keterangan' => $cabang->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                    'debet' => 0,
                    'kredit' => $pengajuan->subtotal()
                ]);
                // end jurnal pembelian untuk cabang KP
                
                // jurnal pembelian untuk cabang AP
                $kode_transaksi = date('m').random_int(100000, 999999);
                foreach ($branch as $key => $data) {
                    TransaksiAp::create([
                        'kode_branch' => $data->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => $cabang->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
=======
                        'keterangan' => $cabang->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => $cabang->harga_pembelian / $total,
                        'kredit' => 0
                    ]);

                    TransaksiAp::create([
                        'kode_branch' => $data->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => '2500000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
=======
                        'keterangan' => $cabang->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => 0,
                        'kredit' => $cabang->harga_pembelian / $total
                    ]);
                }
                // end pembelian untuk cabang AP
<<<<<<< HEAD
            } elseif (!is_null($cabang->kode_branch)) {
=======
            } else {
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                // jurnal LPJ pembelian peralatan di KP
                $kode_transaksi = date('m').random_int(100000, 999999);
                foreach ($pengajuan->pengajuan_details as $item) {
                    if ($item->kode_branch == '1010') {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-'.$item->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                            'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                            'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
                    } else {
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                            'kode_rekening' => '1010-'.$item->ap->GL,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                            'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                            'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
                    }
                }
    
                $kode_transaksi = date('m').random_int(100000, 999999);
                if ($item->kode_branch == '1010') {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1311000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                        'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => $pengajuan->nominal_pengembalian,
                        'kredit' => 0
                    ]);
        
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                        'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
                } else {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1311000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                        'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => $pengajuan->nominal_pengembalian,
                        'kredit' => 0
                    ]);
        
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-1929000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                        'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                        'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                        'debet' => 0,
                        'kredit' => $pengajuan->subtotal()
                    ]);
                } // end LPJ pembelian peralatan di KP

                // jurnal LPJ pembelian peralatan di AP
                foreach ($pengajuan->pengajuan_details as $item) {
                    $kode_transaksi = date('m').random_int(100000, 999999);
                    if ($item->kode_branch != '1010') {
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => $item->jenis_peralatan->param_peralatan->coa,
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                            'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                            'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                            'debet' => $item->harga_pembelian,
                            'kredit' => 0
                        ]);
        
                        TransaksiAp::create([
                            'kode_branch' => $item->ap->kode_branch,
                            'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                            'kode_rekening' => '2500000',
                            'tanggal_transaksi' => Carbon::now(),
                            'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                            'keterangan' => 'LPJ' . ' ' . $item->keterangan,
=======
                            'keterangan' => $item->keterangan,
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                            'debet' => 0,
                            'kredit' => $item->harga_pembelian,
                        ]);
                    }
                } // end jurnal LPJ pembelian peralatan di AP
<<<<<<< HEAD
            } elseif (!is_null($cabang->kode_user)) {
                // Jurnal Pembelian Peralatan Cabang
                foreach ($pengajuan->pengajuan_details as $key => $cabang) {
                    $subtotal = 0;
                    $total = $cabang->harga * $cabang->kuantitas;
                    $subtotal = $subtotal + $total;
                }

                $branch = Branch::where('code_region', $cabang->kode_user)
                ->where('status_aktif', 'aktif')
                ->get();
                $total = $branch->count();
                // jurnal pembelian untuk cabang KP
                $kode_transaksi = date('m').random_int(100000, 999999);
                foreach ($branch as $key => $data) {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010'.'-'.$data->GL,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
                        'debet' => $cabang->harga_pembelian / $total,
                        'kredit' => 0
                    ]);
                }
                
                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1311000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
                    'debet' => $pengajuan->nominal_pengembalian,
                    'kredit' => 0
                ]);

                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => '1010-1929000',
                    'tanggal_transaksi' => Carbon::now(),
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
                    'debet' => 0,
                    'kredit' => $pengajuan->subtotal()
                ]);
                // end jurnal pembelian untuk cabang KP
                
                // jurnal pembelian untuk cabang AP
                $kode_transaksi = date('m').random_int(100000, 999999);
                foreach ($branch as $key => $data) {
                    TransaksiAp::create([
                        'kode_branch' => $data->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => $cabang->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
                        'debet' => $cabang->harga_pembelian / $total,
                        'kredit' => 0
                    ]);

                    TransaksiAp::create([
                        'kode_branch' => $data->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$data->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => '2500000',
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'LPJ' . ' ' . $cabang->keterangan,
                        'debet' => 0,
                        'kredit' => $cabang->harga_pembelian / $total
                    ]);
                }
                // end pembelian untuk cabang AP
=======
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
            }

        }

        // jika pembelian lebih besar dari pengajuan
        if ($pengajuan->subtotal() < $pengajuan->total_pembelian()) {
            foreach ($pengajuan->pengajuan_details as $item) {
                // jurnal LPJ pembelian inventaris di kp
                $kode_transaksi = date('m').random_int(100000, 999999);
                if ($item->kode_branch == '1010') {
                    TransaksiKp::create([
                        'kode_branch' => 1010,
                        'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                        'kode_rekening' => '1010-'.$item->jenis_peralatan->param_peralatan->coa,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => $item->jenis_peralatan->param_peralatan->nama,
                        'debet' => $item->harga_pembelian,
                        'kredit' => 0
                    ]);
                } else {
                    TransaksiKp::create([
                        'kode_branch' => $item->ap->kode_branch,
                        'kode_transaksi' => 'BU'.'/'.$item->ap->kode_branch.'/'.$kode_transaksi,
                        'kode_rekening' => $item->ap->GL,
                        'tanggal_transaksi' => Carbon::now(),
                        'jenis_transaksi' => 'Bukti SYSTEM',
                        'keterangan' => 'RAK Aktiva - Unit'.' '.ucfirst($item->ap->unit),
                        'debet' => $item->harga_pembelian,
                        'kredit' => 0
                    ]);
                } 
            }

            foreach ($pengajuan->pengajuan_details as $item) {
                // jurnal di KP
                TransaksiKp::create([
                    'kode_branch' => 1010,
                    'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                    'kode_rekening' => $item->ap->GL,
                    'tanggal_transaksi' => $item->tanggal_pembelian,
                    'jenis_transaksi' => 'Bukti SYSTEM',
                    'keterangan' => 'RAK Aktiva - Unit'.' '.ucfirst($item->ap->unit),
                    'debet' => $item->harga_pembelian,
                    'kredit' => 0
                ]);
            }

            TransaksiKp::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                'kode_rekening' => '1010-1312000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
<<<<<<< HEAD
                'keterangan' => 'PPBL-Giro Bank BSI Ex BNI Syariah - 8080887729�-�8080887730',
=======
                'keterangan' => 'PPBL-Giro Bank BSI Ex BNI Syariah - 8080887729 - 8080887730',
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
                'debet' => 0,
                'kredit' => $item->harga_pembelian - $pengajuan->subtotal()
            ]); 

            TransaksiKp::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU'.'/'.'1010'.'/'.$kode_transaksi,
                'kode_rekening' => '1010-1929000',
                'tanggal_transaksi' => Carbon::now(),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'UM Lainnya',
                'debet' => 0,
                'kredit' => $pengajuan->subtotal()
            ]); 
        }
        try {

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil  Approve');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal diapprove');
        }

    }

    public function tolak(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $pengajuan = Pengajuan::findOrfail($id);
<<<<<<< HEAD
            $pengajuan->status_approve = 'lpj-admin';
=======
            $pengajuan->status_approve = 'lpj-akunting-it';
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
            $pengajuan->save();
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil Tolak');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th->getMessage());
            return redirect()->back()->with('error', 'Data gagal ditolak');
        }
    }

    public function approve_lpj()
    {
        $data['title'] = 'Pengajuan LPJ';
        $data['lpj'] = Pengajuan::where('status_approve', 'selesai')->get();
        return view('finance.approval.pengajuan-lpj.index', $data);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 81782da63a18fa6bcaf96177de4d2c4e8fcce747
