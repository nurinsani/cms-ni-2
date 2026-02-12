<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Penyusutan;
use App\Models\TransaksiPembebananKp;
use App\Models\TransaksiPembebananAp;

class PembebananService
{
    private function distributeAmount(int $total, int $count): array
    {
        if ($count <= 0) return [];

        $base = intdiv($total, $count);
        $remainder = $total - ($base * $count);

        $result = array_fill(0, $count, $base);
        for ($i = 0; $i < $remainder; $i++) {
            $result[$i]++;
        }

        return $result;
    }

    private function buildTargetBranches($row, string $status_branch): array
    {
        $targets = [];

        $push = function ($branch) use (&$targets) {
            $targets[] = [
                'kode_branch' => $branch->kode_branch,
                'GL' => $branch->GL,
            ];
        };

        $isRegion = Branch::where('code_region', $status_branch)
            ->where('status_aktif', 'aktif')
            ->where(function ($q) {
                $q->whereNull('status_pembebanan')
                  ->orWhere('status_pembebanan', '!=', 'no');
            })->count();

        $isArea = Branch::where('code_area', $status_branch)
            ->where('status_aktif', 'aktif')
            ->count();

        if ($isRegion > 0) {
            if ($status_branch == '9999') {
                $others = Branch::whereIn('code_region', ['3333', '2222'])
                    ->where('status_aktif', 'aktif')->get();
            } else {
                $others = Branch::where('code_region', $status_branch)
                    ->where('kode_branch', '<>', $row->kode_branch)
                    ->where('status_aktif', 'aktif')->get();
            }

            foreach ($others as $b) $push($b);

            $targets[] = ['kode_branch' => $row->kode_branch, 'GL' => $row->gl];

            return $targets;
        }

        if ($isArea != 0) {
            $others = Branch::where('code_area', $status_branch)
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } else {
            $others = collect();
        }

        if ($status_branch == '9999') {
            $others = Branch::whereIn('code_region', ['3333', '2222'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } elseif ($status_branch == '8888') {
            $others = Branch::whereIn('code_area', ['2206', '2207', '2208'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } elseif ($status_branch == '9998') {
            $others = Branch::whereIn('code_region', ['3333', '2222'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } elseif ($status_branch == '3331') {
            $others = Branch::whereIn('code_area', ['2203', '2211', '2204', '2210', '2212'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } elseif ($status_branch == '9901') {
            $others = Branch::whereIn('kode_branch', ['2003', '2002', '2001', '2025'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        } elseif ($status_branch == '9902') {
            $others = Branch::whereIn('kode_branch', ['2007', '2004', '2027', '2006'])
                ->where('kode_branch', '<>', $row->kode_branch)
                ->where('status_aktif', 'aktif')->get();
        }

        foreach ($others as $b) $push($b);

        $targets[] = ['kode_branch' => $row->kode_branch, 'GL' => $row->gl];

        return $targets;
    }

    private function createTransactionsForRow($row)
    {
        $status_branch = $row->status_branch;

        // PENTING: convert ke integer
        $totalCredit = (int) round($row->penyusutan_per_bulan);

        $targets = $this->buildTargetBranches($row, $status_branch);
        $countTargets = count($targets);

        if ($countTargets <= 1) {
            $kode_ap = date('m') . random_int(100000, 999999);
            $kode_kp = date('m') . random_int(100000, 999999);

            // AP entry
            TransaksiPembebananAp::create([
                'kode_branch' => $row->kode_branch,
                'kode_transaksi' => 'BU/'.$row->kode_branch.'/'.$kode_ap,
                'kode_rekening' => '57202',
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => 0,
                'kredit' => $totalCredit,
            ]);

            TransaksiPembebananAp::create([
                'kode_branch' => $row->kode_branch,
                'kode_transaksi' => 'BU/'.$row->kode_branch.'/'.$kode_ap,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => $totalCredit,
                'kredit' => 0,
            ]);

            // KP
            TransaksiPembebananKp::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU/1010/'.$kode_kp,
                'kode_rekening' => '1010-'.$row->gl,
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => 0,
                'kredit' => $totalCredit,
            ]);

            return;
        }

        // DISTRIBUSI
        $distributions = $this->distributeAmount($totalCredit, $countTargets);

        $kode_kp = date('m') . random_int(100000, 999999);

        // Untuk branch penerima (selain origin)
        for ($i = 0; $i < $countTargets - 1; $i++) {
            $t = $targets[$i];
            $value = $distributions[$i];

            $kode_ap = date('m') . random_int(100000, 999999);

            // KP (debet)
            TransaksiPembebananKp::create([
                'kode_branch' => 1010,
                'kode_transaksi' => 'BU/1010/'.$kode_kp,
                'kode_rekening' => '1010-'.$t['GL'],
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => $value,
                'kredit' => 0,
            ]);

            // AP debet
            TransaksiPembebananAp::create([
                'kode_branch' => $t['kode_branch'],
                'kode_transaksi' => 'BU/'.$t['kode_branch'].'/'.$kode_ap,
                'kode_rekening' => '58312',
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => $value,
                'kredit' => 0,
            ]);

            // AP kredit
            TransaksiPembebananAp::create([
                'kode_branch' => $t['kode_branch'],
                'kode_transaksi' => 'BU/'.$t['kode_branch'].'/'.$kode_ap,
                'kode_rekening' => '2500000',
                'tanggal_transaksi' => date('Y-m-d'),
                'jenis_transaksi' => 'Bukti SYSTEM',
                'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
                'debet' => 0,
                'kredit' => $value,
            ]);
        }

        // ORIGIN BRANCH
        $origin = $targets[$countTargets - 1];
        $originDebet = $distributions[$countTargets - 1];

        $kode_ap_origin = date('m') . random_int(100000, 999999);

        $kredit = array_sum($distributions) - $originDebet;

        // AP origin kredit = TOTAL CREDIT
        TransaksiPembebananAp::create([
            'kode_branch' => $origin['kode_branch'],
            'kode_transaksi' => 'BU/'.$origin['kode_branch'].'/'.$kode_ap_origin,
            'kode_rekening' => '57202',
            'tanggal_transaksi' => date('Y-m-d'),
            'jenis_transaksi' => 'Bukti SYSTEM',
            'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
            'debet' => 0,
            'kredit' => $kredit,
        ]);

        // AP origin debet
        TransaksiPembebananAp::create([
            'kode_branch' => $origin['kode_branch'],
            'kode_transaksi' => 'BU/'.$origin['kode_branch'].'/'.$kode_ap_origin,
            'kode_rekening' => '2500000',
            'tanggal_transaksi' => date('Y-m-d'),
            'jenis_transaksi' => 'Bukti SYSTEM',
            'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
            'debet' => $kredit,
            'kredit' => 0,
        ]);

        // KP origin kredit = TOTAL CREDIT
        TransaksiPembebananKp::create([
            'kode_branch' => 1010,
            'kode_transaksi' => 'BU/1010/'.$kode_kp,
            'kode_rekening' => '1010-'.$origin['GL'],
            'tanggal_transaksi' => date('Y-m-d'),
            'jenis_transaksi' => 'Bukti SYSTEM',
            'keterangan' => 'Jurnal Pembebanan Inventaris '.$row->keterangan.' '.date('M Y'),
            'debet' => 0,
            'kredit' => $kredit,
        ]);
    }

    public function processAllCurrentMonth()
    {
        DB::beginTransaction();
        try {
            $pembebanan = Penyusutan::whereMonth('tanggal_penyusutan', date('m'))
                ->whereYear('tanggal_penyusutan', date('Y'))
                ->whereNotIn('status_branch', ['AP', 'ALL'])
                ->whereNotIn('kode_branch', ['1010'])
                ->get();

            foreach ($pembebanan as $row) {
                $this->createTransactionsForRow($row);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
