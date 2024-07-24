<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Admin\Pengajuan;
use App\Models\Admin\PengajuanDetail;
use App\Models\Akunting\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Inventaris extends Model
{
    use HasFactory;
    protected $table = 'inventaris';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function susut()
    {
        return $this->hasMany(Penyusutan::class);
    }

    public function ap()
    {
        return $this->belongsTo(Branch::class, 'kode_branch', 'kode_branch');
    }

    public function akumulasi_penyusutan_awal()
    {
        // Log::info($this->susut()->whereYear('tanggal_penyusutan', 2023)->get());
        return ($this->harga_perolehan / 48) * $this->susut()->whereYear('tanggal_penyusutan', Carbon::now()->subYears(1)->format('Y'))->count();
    }

    public function akumulasi_penyusutan()
    {
        return (intval($this->harga_perolehan) / 48) * $this->susut()->whereYear('tanggal_penyusutan', date('Y'))->count();
    }


    public function total_akumulasi_penyusutan()
    {
        $total = 0;
        $inventaris = Inventaris::all();

        foreach ($inventaris as $i) {
            $total += $i->akumulasi_penyusutan();
        }

        return $total;
    }

    public function nilai_buku()
    {
        $total = 0;
        $inventaris = Inventaris::all();

        foreach ($inventaris as $i) {
            $total += intval($i->harga_perolehan) - ($i->akumulasi_penyusutan());
        }

        return $total;
    }

    public static function penyusutan()
    {
        try {
            $i = 0;

            $inventaris = Inventaris::all();

            $penyusutan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->first();

            if (!$penyusutan) {
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

                    if ($tanggal_sekarang == 19) {
                        if ($value->penyusutan_ke + count($value->susut) !== 48) {
                            Penyusutan::create([
                                'inventaris_id' => $value->id,
                                'user_id' => $value->user_id,
                                'kode_branch' => $value->kode_branch,
                                'kode_peralatan' => $value->kode_peralatan,
                                'nama_peralatan' => $value->jenis,
                                'tanggal_penyusutan' => '2024-07-15',
                                // 'tanggal_penyusutan' => date('Y-m-d'),
                                'penyusutan_per_bulan' => intval($value->harga_perolehan) > 0 && intval($value->umur) > 0 ?
                                    intval($value->harga_perolehan) / intval($value->umur) :
                                    0,
                                'status_branch' => $value->status_branch
                            ]);
                        }

                        $i++;
                    }
                }


                $branches = Branch::all();

                foreach ($branches as $branch) {
                    $penyusutan = Penyusutan::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_penyusutan', date('m'))
                        ->whereYear('tanggal_penyusutan', date('Y'))
                        ->whereIn('status_branch', ['AP', 'ALL'])
                        ->get();

                    if (count($penyusutan) > 0) {
                        $penyusutan_per_bulan = Penyusutan::where('kode_branch', $branch->kode_branch)
                            ->whereMonth('tanggal_penyusutan', date('m'))
                            ->whereYear('tanggal_penyusutan', date('Y'))
                            ->sum('penyusutan_per_bulan');

                        if ($branch->kode_branch == '1010') {
                            $transaksiKpDebet = TransaksiKp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('debet', '>', 0)
                                ->first();

                            $transaksiKpKredit = TransaksiKp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('kredit', '>', 0)
                                ->first();

                            $kode_transaksi = date('m') . random_int(100000, 999999);
                            if ($transaksiKpDebet && $transaksiKpKredit) {
                                TransaksiKp::where('id', $transaksiKpDebet->id)->update([
                                    'debet' => $penyusutan_per_bulan,
                                ]);

                                TransaksiKp::where('id', $transaksiKpKredit->id)->update([
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            } else {
                                TransaksiKp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1010-52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => $penyusutan_per_bulan,
                                    'kredit' => 0,
                                ]);

                                TransaksiKp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1010-1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            }
                        } else {
                            $transaksiApKredit = TransaksiAp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('kredit', '>', 0)
                                ->first();

                            $transaksiApDebet = TransaksiAp::where('kode_branch', $branch->kode_branch)
                                ->whereMonth('tanggal_transaksi', date('m'))
                                ->whereYear('tanggal_transaksi', date('Y'))
                                ->where('debet', '>', 0)
                                ->first();

                            $kode_transaksi = date('m') . random_int(100000, 999999);
                            if ($transaksiApKredit && $transaksiApDebet) {
                                TransaksiAp::where('id', $transaksiApDebet->id)->update([
                                    'debet' => $penyusutan_per_bulan,
                                ]);
                                TransaksiAp::where('id', $transaksiApKredit->id)->update([
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            } else {
                                TransaksiAp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => $penyusutan_per_bulan,
                                    'kredit' => 0,
                                ]);

                                TransaksiAp::create([
                                    'kode_branch' => $branch->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $branch->kode_branch . '/' . $kode_transaksi,
                                    'kode_rekening' => '1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $penyusutan_per_bulan,
                                ]);
                            }
                        }
                    } else {
                        continue;
                    }

                    $penyusutan = Penyusutan::where('kode_branch', $branch->kode_branch)
                        ->whereMonth('tanggal_penyusutan', date('m'))
                        ->whereYear('tanggal_penyusutan', date('Y'))
                        ->whereNotIn('status_branch', ['AP', 'ALL'])
                        ->get();

                    if (count($penyusutan) > 0) {
                        foreach ($penyusutan as $row) {
                            $status_branch = $row->status_branch;

                            $isRegion   = Branch::where('code_region', $status_branch)
                                // ->where('status_aktif', 'aktif')
                                ->count();

                            $isArea     = Branch::where('code_area', $status_branch)
                                // ->where('status_aktif', 'aktif')
                                ->count();

                            $credit = floatval($row->penyusutan_per_bulan);

                            $credit = round($credit, 2);

                            $ids = [];

                            if ($isRegion > 0) {
                                $debet = round(($credit / ($isRegion - 1)), 2);

                                $otherBranches = Branch::where('code_region', $status_branch)
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->get();

                                foreach ($otherBranches as $other) {
                                    $ids[] = $other->kode_branch;
                                }

                                for ($j = 0; $j < count($ids); $j++) {
                                    TransaksiAp::create([
                                        'kode_branch' => $ids[$j],
                                        'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/',
                                        'kode_rekening' => '1010-52915',
                                        'tanggal_transaksi' => date('Y-m-d'),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                        'debet' => $debet,
                                        'kredit' => 0,
                                    ]);

                                    TransaksiKp::create([
                                        'kode_branch' => $ids[$j],
                                        'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/',
                                        'kode_rekening' => '1010-1672000',
                                        'tanggal_transaksi' => date('Y-m-d'),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                        'debet' => $debet,
                                        'kredit' => 0,
                                    ]);
                                }

                                TransaksiAp::create([
                                    'kode_branch' => $row->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/',
                                    'kode_rekening' => '1010-52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $credit,
                                ]);

                                TransaksiKp::create([
                                    'kode_branch' => $row->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/',
                                    'kode_rekening' => '1010-1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $credit,
                                ]);
                            } else {
                                $debet = round(($credit / ($isArea - 1)), 2);

                                $otherBranches = Branch::where('code_area', $status_branch)
                                    ->where('kode_branch', '<>', $row->kode_branch)
                                    ->get();

                                foreach ($otherBranches as $other) {
                                    $ids[] = $other->kode_branch;
                                }

                                for ($j = 0; $j < count($ids); $j++) {
                                    TransaksiAp::create([
                                        'kode_branch' => $ids[$j],
                                        'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/',
                                        'kode_rekening' => '1010-52915',
                                        'tanggal_transaksi' => date('Y-m-d'),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                        'debet' => $debet,
                                        'kredit' => 0,
                                    ]);

                                    TransaksiKp::create([
                                        'kode_branch' => $ids[$j],
                                        'kode_transaksi' => 'BU' . '/' . $ids[$j] . '/',
                                        'kode_rekening' => '1010-1672000',
                                        'tanggal_transaksi' => date('Y-m-d'),
                                        'jenis_transaksi' => 'Bukti SYSTEM',
                                        'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                        'debet' => $debet,
                                        'kredit' => 0,
                                    ]);
                                }

                                TransaksiAp::create([
                                    'kode_branch' => $row->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/',
                                    'kode_rekening' => '1010-52915',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $credit,
                                ]);

                                TransaksiKp::create([
                                    'kode_branch' => $row->kode_branch,
                                    'kode_transaksi' => 'BU' . '/' . $row->kode_branch . '/',
                                    'kode_rekening' => '1010-1672000',
                                    'tanggal_transaksi' => date('Y-m-d'),
                                    'jenis_transaksi' => 'Bukti SYSTEM',
                                    'keterangan' => 'Jurnal Penyusutan Inventaris' . ' ' . date('M Y'),
                                    'debet' => 0,
                                    'kredit' => $credit,
                                ]);
                            }
                        }
                    } else {
                        continue;
                    }
                }

                Log::info('schadule inventaris dijalankan ' . $i . ' data ditambahkan');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
