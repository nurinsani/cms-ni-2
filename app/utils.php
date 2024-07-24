<?php

use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use App\Models\Branch;
use App\Models\Inventaris;
use App\Models\Penyusutan;
use App\Models\TransaksiAp;
use App\Models\TransaksiKp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function susut()
{
    try {
        $inventaris = Inventaris::all();

        foreach ($inventaris as $value) {
            $tanggal_sekarang = date('d', time());
            $harga_perolehan = intval($value->harga_perolehan);
            $penyusutan = intval($value->umur);
            $kode_branch = $value->kode_branch;
            $kredit = Branch::firstWhere('kode_branch', $kode_branch);
            if (!$kredit) {
                continue; // Skip if no matching Branch found
            }
            $debet = Branch::where('code_area', $kredit->code_area)->get();
            if ($debet->isEmpty()) {
                continue; // Skip if no matching Debet branches found
            }

            $hasil_penyusutan = $harga_perolehan > 0 && $penyusutan > 0 ?
                $harga_perolehan / $penyusutan :
                0;

            $hasil_pembebanan = $hasil_penyusutan / $debet->count();
            if ($tanggal_sekarang == 15) {
                if ($value->penyusutan_ke + count($value->susut) !== 48) {
                    Penyusutan::create([
                        'inventaris_id' => $value->id,
                        'user_id' => $value->user_id,
                        'kode_branch' => $value->kode_branch,
                        'kode_peralatan' => $value->kode_peralatan,
                        'nama_peralatan' => $value->jenis,
                        'tanggal_penyusutan' => date('Y-m-d'),
                        'penyusutan_per_bulan' => intval($value->harga_perolehan) > 0 && intval($value->umur) > 0 ?
                            intval($value->harga_perolehan) / intval($value->umur) :
                            0,
                    ]);


                    $kode_transaksi = date('m') . random_int(100000, 999999);
                    if ($value->status_branch == "AP") {
                        // jurnal Amortisasi di KP
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->umur,
                            'kredit' => 0
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->umur,
                        ]);

                        // jurnal Amortisasi di AP
                        TransaksiAp::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->umur,
                            'kredit' => 0
                        ]);

                        TransaksiAp::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->umur,
                        ]);
                    } else if ($value->status_branch == "ALL") {
                        // jurnal Amortisasi di KP
                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->umur,
                            'kredit' => 0
                        ]);

                        TransaksiKp::create([
                            'kode_branch' => 1010,
                            'kode_transaksi' => 'BU' . '/' . '1010' . '/' . $kode_transaksi,
                            'kode_rekening' => '1010-1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->umur,
                        ]);

                        // jurnal Amortisasi di AP
                        TransaksiAp::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '52915',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'Biaya Penyusutan-ATI-Gol I-Inventaris',
                            'debet' => $value->harga_perolehan / $value->umur,
                            'kredit' => 0
                        ]);

                        TransaksiAp::create([
                            'kode_branch' => $value->kode_branch,
                            'kode_transaksi' => 'BU' . '/' . $value->kode_branch . '/' . $kode_transaksi,
                            'kode_rekening' => '1672000',
                            'tanggal_transaksi' => date('Y-m-d'),
                            'jenis_transaksi' => 'Bukti SYSTEM',
                            'keterangan' => 'ATI-API-Gol I-Inventaris -/-',
                            'debet' => 0,
                            'kredit' => $value->harga_perolehan / $value->umur,
                        ]);
                    } else if ($value->status_branch == "AUDIT") {
                        foreach ($debet as $key => $data) {
                            if ($data->kode_branch == $kode_branch) {
                                $nilai_debet = 0;
                                $nilai_kredit = $hasil_pembebanan * ($debet->count() - 1);
                            } else {
                                $nilai_debet = $hasil_pembebanan;
                                $nilai_kredit = 0;
                            }

                            TransaksiKp::create([
                                'kode_branch' => $data->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                'kode_rekening' => '1010' . '-' . $data->GL,
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK Aktiva - Unit' . ' ' . $data->unit,
                                'debet' => $nilai_debet,
                                'kredit' => $nilai_kredit
                            ]);
                        }

                        // jurnal amortisasi pembebanan di KP
                        foreach ($debet as $key => $data) {
                            if ($data->kode_branch == $kode_branch) {
                                $nilai_debet = 0;
                                $nilai_kredit = $hasil_pembebanan * ($debet->count() - 1);
                            } else {
                                $nilai_debet = $hasil_pembebanan;
                                $nilai_kredit = 0;
                            }

                            TransaksiKp::create([
                                'kode_branch' => $data->kode_branch,
                                'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                'kode_rekening' => '1010' . '-' . $data->GL,
                                'tanggal_transaksi' => date('Y-m-d'),
                                'jenis_transaksi' => 'Bukti SYSTEM',
                                'keterangan' => 'RAK Aktiva - Unit' . ' ' . $data->unit,
                                'debet' => $nilai_debet,
                                'kredit' => $nilai_kredit
                            ]);
                        }
                        // end jurnal amortisasi pembebanan di KP

                        // jurnal amortisasi pembebanan di AP
                        foreach ($debet as $key => $data) {
                            if ($data->kode_branch != $kode_branch) {
                                // $nilai_debet = 0;
                                // $nilai_kredit = $hasil_pembebanan * ($debet->count() - 1);
                                TransaksiAp::create([
                                    'kode_branch' => $data->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '58312',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                                    'debet' => $hasil_pembebanan,
                                    'kredit' => 0
                                ]);

                                TransaksiAp::create([
                                    'kode_branch' => $data->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'RAK PASIVA - KP',
                                    'debet' => 0,
                                    'kredit' => $hasil_pembebanan
                                ]);
                            } else {
                                TransaksiAp::create([
                                    'kode_branch' => $data->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '2500000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'RAK PASIVA - KP',
                                    'debet' => $hasil_pembebanan * ($debet->count() - 1),
                                    'kredit' => 0
                                ]);

                                TransaksiAp::create([
                                    'kode_branch' => $data->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $data->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '57202',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'BNO-Bg Hsl/Bns/Margin Antar Kantor-RAK AP',
                                    'debet' => 0,
                                    'kredit' => $hasil_pembebanan * ($debet->count() - 1)
                                ]);
                            }
                        }
                    }
                }
            }
        }

        die();
        Log::info('schadule inventaris dijalankan');
    } catch (\Throwable $th) {
        Log::error($th->getMessage());
    }
}
