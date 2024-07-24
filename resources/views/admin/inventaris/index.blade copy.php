@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <a href="{{ url('admin/export-excel-peralatan') }}" class="btn btn-sm btn-success my-2">Export</a>
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-sm table-responsive">
                <table id="example2" class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Jenis</th>
                            <th>Merk</th>
                            <th>Type</th>
                            <th>Keterangan</th>
                            <th>Tgl Perolehan</th>
                            <th>Qty</th>
                            <th>Harga Perolehan</th>
                            <th>Umur</th>
                            <th>Peny. Bulan</th>
                            <th>Akum. Peny. Awal</th>
                            <th>Akum. Peny. Berjalan</th>
                            <th>Akum. Penyusutan</th>
                            <th>Nilai Buku</th>
                            <th>Peny. Ke</th>
                            <th>Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventaris as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->kode_peralatan }}</td>
                                {{-- <td>{!! DNS2D::getBarcodeHTML("$item->kode_peralatan", 'QRCODE', 2, 2) !!}</td> --}}
                                <td>{{ $item->jenis }}</td>
                                <td>{{ $item->merk }}</td>
                                <td>{{ $item->type }}</td>
                                <td>
                                    @if ($item->keterangan == null)
                                        -
                                    @else
                                        {{ $item->keterangan }}
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_perolehan }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp. {{ number_format($item->harga_perolehan) }}</td>
                                <td>48</td>
                                <td>Rp. {{ number_format($item->harga_perolehan / 48) }}</td>
                                {{-- <td>Rp. {{ number_format($item->akumulasi_penyusutan_awal()) }}</td> --}}
                                <td>Rp. {{ number_format($item->akumulasi_penyusutan()) }}</td>
                                <td>Rp. {{ number_format(($item->harga_perolehan / 48) * $item->susut->count()) }}</td>
                                <td>Rp.
                                    {{ number_format($item->harga_perolehan - ($item->harga_perolehan / 48) * $item->susut->count())}}
                                </td>
                                <td>{{ count($item->susut) - 1 }}</td>
                                <td>
                                    @if ($item->pengguna == NULL)
                                        -
                                    @else
                                        {{ $item->pengguna }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tfoot>
                        <td colspan="8" class="text-center"><strong>TOTAL</strong></td>
                        <td><strong>Rp. {{ number_format($total_harga_perolehan) }}</strong></td>
                        <td></td>
                        <td><strong>Rp. {{ number_format($total_peny) }}<strong /></td>
                        <td><strong>Rp. 0<strong /></td>
                        <td><strong>Rp. {{ number_format($item->total_akumulasi_penyusutan()) }}<strong /></td>
                        <td><strong>Rp. {{ number_format($item->total_akumulasi_penyusutan()) }}<strong /></td>
                        <td><strong>Rp. {{ number_format($item->nilai_buku()) }}<strong /></td>
                        <td colspan="2"></td>
                    </tfoot> --}}
                </table>
            </div>
        </div>
        <!-- /.card-body -->
    </div>

    {{-- <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap myTable">
                    <thead>
                        <tr>
                            @php
                                $th = [
                                    'Januari',
                                    'Februari',
                                    'Maret',
                                    'April',
                                    'Mei',
                                    'Juni',
                                    'Juli',
                                    'Agustus',
                                    'September',
                                    'Oktober',
                                    'November',
                                    'Desember',
                                ];
                            @endphp
                            @foreach ($th as $item)
                                <th>{{ $item . ' ' . date('Y') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($penyusutan as $bulan => $items)
                                @foreach ($items as $item)
                                    <td>{{ $item->penyusutan_per_bulan }}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> 
        <!-- /.card-body -->
    </div> --}}
@endsection
