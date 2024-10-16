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
        <a href="{{ url('akunting/approval-peralatan') }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
        <ul class="p-0" style="list-style: none">
            <li>Nomor : {{ $pengajuan->nomor }}</li>
            <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
            <li>Dari : {{ $pengajuan->user->name }}</li>
            <li>Tanggal : {{ $pengajuan->tanggal_pengajuan->format('d-M-Y') }}</li>
        </ul>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Branch</th>
                        <th scope="col">Keperluan</th>
                        <th scope="col">Qty</th>
                        <th scope="col" width="120px">Jumlah</th>
                        <th scope="col" width="140px">Total</th>
                        @foreach ($pengajuan->pengajuan_details as $item)
                        @endforeach
                        <th scope="col" width="20px">MOA</th>
                        <th scope="col">Keterangan</th>
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
                                    <li> {{ $item->jenis_peralatan->nama_jenis }} </li>
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
                        <td>
                            <ul class="p-0" style="width: 180px; list-style: none">
                                @foreach ($pengajuan->pengajuan_details as $item)
                                <li>
                                    @if ($item->moa == null)
                                    (tidak ada berkas)
                                    @else
                                    <a href="" onclick="window.open('{{ url('files/moa/'.$item->moa) }}')">{{ $item->moa }}</a>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if ($item->rekening_id == NULL)
                            Transfer ke <br>{{ $item->pengajuan->user->nama_bank }} {{ $item->pengajuan->user->no_rek }}
                            A/N {{ $item->pengajuan->user->atas_nama }}
                            @else
                            Transfer ke <br><strong>{{ $item->rekening->no_rek }} {{ $item->rekening->nama_bank }}
                            A/N {{ $item->rekening->atas_nama }}</strong>
                            @endif
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Subtotal</strong></td>
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