@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('admin-head/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

@push('css')
    <style>
        @media print {
            .d-print-none {
                display: none;
            }

        .main-footer {
            display: none !important;
        }

        @page {
            margin-left: 0.5in;
            margin-right: 0.5in;
            margin-top: 0;
            margin-bottom: 0;
            }
        }
    </style>
@endpush

<div class="card">
    <div class="card-body">
        <a href="{{ url('pengurus/klaim-pembelian') }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
        <ul class="p-0" style="list-style: none">
            <li>Nomor : {{ $pengajuan->nomor }}</li>
            <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
            <li>Dari : {{ $pengajuan->user->name }}</li>
            <li>Tanggal : {{ $pengajuan->tanggal_pengajuan->format('d-M-Y'); }}</li>
        </ul>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Nama AP</th>
                        <th scope="col">COA</th>
                        <th scope="col">Keperluan</th>
                        <th scope="col">Kuantitas</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Total</th>
                        @foreach ($pengajuan->pengajuan_details as $item)
                        @if ($item->jenis_peralatan->group_inventaris == 'ya')
                        <th scope="col">MOA</th>
                        @endif
                        @endforeach
                        <th scope="col">Bukti Pembelian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                <li>{{ $item->ap->unit ?? $item->area->area }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                    <li>{{ $item->jenis_peralatan->param_peralatan->coa }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                    <li> {{ $item->jenis_peralatan->nama_jenis }} {{ $item->merk }} {{ $item->type }} </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                    <li>{{ $item->kuantitas }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                    <li>Rp. {{ number_format($item->harga) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                    <li>Rp. {{ number_format($item->harga * $item->kuantitas) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        @if ($item->jenis_peralatan->group_inventaris == 'ya')
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                <a href="" onclick="window.open('{{ url('files/moa/'.$item->moa) }}')">{{ $item->moa }}</a>
                                @endforeach
                            </ul>
                        </td>
                        @endif
                        <td>
                            <a href="" onclick="window.open('{{ url('files/struk_pembelian/'.$item->struk_pembelian) }}')">{{ $item->struk_pembelian }}</a>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><strong>Subtotal</strong></td>
                        <td colspan="3"><strong>Rp. {{ number_format($pengajuan->subtotal())}}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($pengajuan->status_approve == 'selesai')
        <button type="button" onclick="window.print()" class="btn btn-primary mt-5 d-print-none">Print</button>
        @endif
    </div>
</div>
@endsection