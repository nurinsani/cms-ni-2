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
    <!-- /.card-header -->
    <div class="card-body">
        <ul class="p-0" style="list-style: none">
            <li>Nomor : {{ $pengajuan->nomor }}</li>
            <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
            <li>Dari : {{ $pengajuan->user->name }}</li>
            {{-- <li>Bagian : -</li> --}}
            <li>Tanggal : {{ $pengajuan->tanggal_pengajuan }}</li>
        </ul>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Keperluan</th>
                        <th scope="col">Kuantitas</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Total</th>
                        <th scope="col">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td>1</td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->jenis_peralatans as $item)
                                    <li>{{ $item->nama_jenis }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->jenis_peralatans as $item)
                                    <li>{{ $item->kuantitas }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->jenis_peralatans as $item)
                                    <li>Rp. {{ number_format($item->harga) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul class="p-0" style="list-style: none">
                                @foreach ($pengajuan->jenis_peralatans as $item)
                                    <li>Rp. {{ number_format($item->harga * $item->kuantitas) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            {{ $pengajuan->keterangan }}<br>
                            {{ $pengajuan->branch->unit }}, {{ $pengajuan->user->name }}, {{ $pengajuan->user->no_rek }}, {{ $pengajuan->user->nama_bank }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Subtotal</strong></td>
                        <td colspan="3"><strong>Rp. {{ number_format($pengajuan->subtotal())}}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($pengajuan->status_uji == 'selesai')
        <button type="button" onclick="window.print()" class="btn btn-primary mt-5 d-print-none">Print</button>
        @endif
        
    </div>
    <!-- /.card-body -->
  </div>
@endsection