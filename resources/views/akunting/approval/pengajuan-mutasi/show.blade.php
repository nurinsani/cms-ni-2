@extends('layouts.main')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Pengajuan Mutasi</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Dari Cabang:</th>
                <td>{{ $pengajuan->dari_ap->unit }}</td>
            </tr>
            <tr>
                <th>Ke Cabang:</th>
                <td>{{ $pengajuan->branch->unit }}</td>
            </tr>
            <tr>
                <th>Bulan Mutasi:</th>
                <td>{{ $pengajuan->bulan_mutasi }}</td>
            </tr>
        </table>

        <h4>Inventaris yang Dimutasi:</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Inventaris</th>
                    <th>Jenis</th>
                    <th>Harga Perolehan</th>
                    <th>Akumulasi Penyusutan</th>
                    <th>Nilai Buku Terakhir</th>
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
                        $harga_perolehan = $inventaris->harga_perolehan;
                        $akumulasi_penyusutan = $inventaris->akumulasi_penyusutan_awal() + $inventaris->akumulasi_penyusutan();
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
                    <td colspan="3" class="text-center"><strong>Total</strong></td>
                    <td><strong>{{ number_format($total_harga_perolehan) }}</strong></td>
                    <td><strong>{{ number_format($total_akumulasi_penyusutan) }}</strong></td>
                    <td><strong>{{ number_format($total_nilai_buku) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <a href="{{ url('akunting/approval-mutasi') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
@endsection
