<?php

namespace App\Imports;

use App\Models\InventarisGol2;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InventarisGol2Import implements ToCollection, WithHeadingRow
{
    private $importedCount = 0;

    public function collection(Collection $rows)
    {
        $userId = Auth::id() ?? 1;

        foreach ($rows as $row) {
            // Trim values
            $kodeBranch = isset($row['kode_branch']) ? trim((string)$row['kode_branch']) : null;
            $jenis      = isset($row['jenis']) ? trim((string)$row['jenis']) : null;
            $type       = isset($row['type']) ? trim((string)$row['type']) : null;

            // Pastikan data minimal terpenuhi (kode_branch dan jenis)
            if (empty($kodeBranch) || empty($jenis)) {
                continue;
            }

            // Sanitasi unit jika kosong, ambil unit dari tabel branch
            $unit = isset($row['unit']) && strlen(trim((string)$row['unit'])) > 0 
                ? trim((string)$row['unit']) 
                : null;

            if (!$unit) {
                $branchObj = Branch::where('kode_branch', $kodeBranch)->first();
                if ($branchObj) {
                    $unit = $branchObj->unit;
                }
            }

            // Parsing Tanggal Perolehan
            $tanggalPerolehan = $this->transformDate($row['tanggal_perolehan'] ?? null);

            // Handling IMEI jika format float (ilmiah) dari excel
            $imei = isset($row['imei']) && !empty($row['imei']) ? (string)$row['imei'] : null;
            if ($imei) {
                // Hilangkan karakter non-digit
                $imei = preg_replace('/[^0-9]/', '', $imei);
            }

            // Status Branch
            $statusBranch = isset($row['status_branch']) && strlen(trim((string)$row['status_branch'])) > 0
                ? trim((string)$row['status_branch'])
                : 'ALL';

            // Clean data angka
            $jumlah = isset($row['jumlah']) && is_numeric($row['jumlah']) ? (int)$row['jumlah'] : 1;
            $hargaPerolehan = isset($row['harga_perolehan']) ? trim((string)$row['harga_perolehan']) : '0';
            $umur = isset($row['umur']) ? trim((string)$row['umur']) : '48';
            $penyusutanKe = isset($row['penyusutan_ke']) ? trim((string)$row['penyusutan_ke']) : '0';

            InventarisGol2::create([
                'user_id'           => $userId,
                'status_branch'     => $statusBranch,
                'kode_branch'       => $kodeBranch,
                'unit'              => $unit,
                'kode_peralatan'    => isset($row['kode_peralatan']) ? trim((string)$row['kode_peralatan']) : null,
                'jenis'             => $jenis,
                'merk'              => isset($row['merk']) ? trim((string)$row['merk']) : null,
                'type'              => !empty($type) ? $type : '-',
                'imei'              => $imei,
                'keterangan'        => isset($row['keterangan']) ? trim((string)$row['keterangan']) : null,
                'tanggal_perolehan' => $tanggalPerolehan,
                'jumlah'            => $jumlah,
                'harga_perolehan'   => $hargaPerolehan,
                'umur'              => $umur,
                'nip'               => isset($row['nip']) ? trim((string)$row['nip']) : null,
                'pengguna'          => isset($row['pengguna']) ? trim((string)$row['pengguna']) : null,
                'jabatan'           => isset($row['jabatan']) ? trim((string)$row['jabatan']) : null,
                'penyusutan_ke'     => $penyusutanKe,
                'old_pengguna'      => null,
                'old_jabatan'       => null,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
