@extends('layouts.main')

@section('content')

{{-- <a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}" class="btn btn-primary mb-3">
    <i class="fas fa-arrow-left"></i> Kembali
</a> --}}

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">
            Informasi Mutasi
        </h5>

        <div class="card border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">AP Asal</label>
                            <div class="text-bold">{{ $pengajuan->dari_ap->unit }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">AP Tujuan</label>
                            <div class="text-bold text-success">{{ $pengajuan->branch->unit }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">Bulan Mutasi</label>
                            <div class="text-bold">{{ strtoupper($pengajuan->bulan_mutasi) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-sm table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KODE INV</th>
                    <th>JENIS</th>
                    <th>HRG PEROLEHAN</th>
                    <th>AKM PENYUSUTAN</th>
                    <th>NILAI BUKU</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
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
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $inventaris->kode_peralatan }}</td>
                        <td>{{ $inventaris->jenis }}</td>
                        <td>{{ number_format($harga_perolehan) }}</td>
                        <td>{{ number_format($akumulasi_penyusutan) }}</td>
                        <td>{{ number_format($nilai_buku) }}</td>
                    </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                    <td><strong>{{ number_format($total_harga_perolehan) }}</strong></td>
                    <td><strong>{{ number_format($total_akumulasi_penyusutan) }}</strong></td>
                    <td><strong>{{ number_format($total_nilai_buku) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">Jurnal Berkurang</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
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
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1674000</td>
                        <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>2500000</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1654000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <h5 class="mb-3">Jurnal Bertambah</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
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
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1654000</td>
                        <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1674000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>2500000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <h5 class="mb-3">Jurnal KP</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 48;
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
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1010-{{ $pengajuan->branch->GL }}</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 1</td>
                        <td>1010-{{ $pengajuan->dari_ap->GL }}</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
