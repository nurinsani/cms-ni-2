@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">Detail Approval Mutasi Inventaris Gol 2</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('akunting/approval-mutasi-gol-2') }}">Approval Mutasi Gol 2</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ url('akunting/approval-mutasi-gol-2') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    @if ($pengajuan->status_approve === 'akunting-it')
        <div>
            <form action="{{ url('akunting/approval-mutasi-gol-2/tolak', $pengajuan->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">
                    <i class="fas fa-times mr-1"></i> Tolak
                </button>
            </form>
            <form action="{{ url('akunting/approval-mutasi-gol-2/approve', $pengajuan->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success ml-1" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
            </form>
        </div>
    @endif
</div>

<div class="card card-outline card-primary shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">Informasi Mutasi</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="text-muted small mb-1">KODE MUTASI</label>
                <div class="font-weight-bold">{{ $pengajuan->kode_mutasi ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <label class="text-muted small mb-1">AP ASAL</label>
                <div class="font-weight-bold">{{ $pengajuan->dari_ap->unit ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <label class="text-muted small mb-1">AP TUJUAN</label>
                <div class="font-weight-bold text-success">{{ $pengajuan->branch->unit ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <label class="text-muted small mb-1">BULAN MUTASI</label>
                <div class="font-weight-bold">{{ strtoupper($pengajuan->bulan_mutasi ?? '-') }}</div>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">NO</th>
                        <th>KODE INV</th>
                        <th>JENIS</th>
                        <th>IMEI</th>
                        <th class="text-right">HRG PEROLEHAN</th>
                        <th class="text-right">AKM PENYUSUTAN</th>
                        <th class="text-right">NILAI BUKU</th>
                        <th>PENGGUNA</th>
                        <th>PENGGUNA LAMA</th>
                        <th>JABATAN</th>
                        <th>JABATAN LAMA</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_harga_perolehan = 0;
                        $total_akumulasi_penyusutan = 0;
                        $total_nilai_buku = 0;
                    @endphp
                    @forelse ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                        @php
                            $jumlahPenyusutan = $inventaris->penyusutan_ke;
                            $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                            $jumlahBulan = $inventaris->penyusutans()->count();
                            
                            $harga_perolehan = $inventaris->harga_perolehan;
                            $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                            $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;

                            // Akumulasi total
                            $total_harga_perolehan += $harga_perolehan;
                            $total_akumulasi_penyusutan += $akumulasi_penyusutan;
                            $total_nilai_buku += $nilai_buku;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $inventaris->kode_peralatan }}</td>
                            <td>{{ $inventaris->jenis }}</td>
                            <td>{{ $inventaris->imei }}</td>
                            <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                            <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                            <td class="text-right">{{ number_format($nilai_buku) }}</td>
                            <td>{{ $inventaris->pengguna ?? '-' }}</td>
                            <td>{{ $inventaris->old_pengguna ?? '-' }}</td>
                            <td>{{ $inventaris->jabatan ?? '-' }}</td>
                            <td>{{ $inventaris->old_jabatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Tidak ada data inventaris</td>
                        </tr>
                    @endforelse
                    <tr class="font-weight-bold bg-light">
                        <td colspan="4" class="text-center">TOTAL</td>
                        <td class="text-right">{{ number_format($total_harga_perolehan) }}</td>
                        <td class="text-right">{{ number_format($total_akumulasi_penyusutan) }}</td>
                        <td class="text-right">{{ number_format($total_nilai_buku) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">Jurnal Berkurang</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>JENIS</th>
                        <th>KETERANGAN</th>
                        <th>KODE REKENING</th>
                        <th class="text-right">DEBET</th>
                        <th class="text-right">KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                        @php
                            $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                            $jumlahBulan = $inventaris->penyusutans()->count();
                            $harga_perolehan = $inventaris->harga_perolehan;
                            $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                            $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;
                        @endphp
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1674000</td>
                            <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>2500000</td>
                            <td class="text-right">{{ number_format($nilai_buku) }}</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1654000</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">Jurnal Bertambah</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>JENIS</th>
                        <th>KETERANGAN</th>
                        <th>KODE REKENING</th>
                        <th class="text-right">DEBET</th>
                        <th class="text-right">KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                        @php
                            $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                            $jumlahBulan = $inventaris->penyusutans()->count();
                            $harga_perolehan = $inventaris->harga_perolehan;
                            $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                            $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;
                        @endphp
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1654000</td>
                            <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1674000</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                        </tr>
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>2500000</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">Jurnal KP</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>JENIS</th>
                        <th>KETERANGAN</th>
                        <th>KODE REKENING</th>
                        <th class="text-right">DEBET</th>
                        <th class="text-right">KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                        @php
                            $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                            $jumlahBulan = $inventaris->penyusutans()->count();
                            $harga_perolehan = $inventaris->harga_perolehan;
                            $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                            $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;
                        @endphp
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1010-{{ $pengajuan->branch->GL ?? '-' }}</td>
                            <td class="text-right">{{ number_format($nilai_buku) }}</td>
                            <td class="text-right">0</td>
                        </tr>
                        <tr>
                            <td>Bukti SYSTEM</td>
                            <td>MUTASI INVENTARIS GOL 2</td>
                            <td>1010-{{ $pengajuan->dari_ap->GL ?? '-' }}</td>
                            <td class="text-right">0</td>
                            <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
