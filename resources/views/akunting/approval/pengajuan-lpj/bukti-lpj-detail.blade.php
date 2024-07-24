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

        .d-print-flex {
            display: flex;
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

<div class="row row-cols-1">
    <div class="col">
        <div class="card">
            <div class="card-header mt-5 d-print-flex justify-content-between d-none">
                <img src="https://picsum.photos/200" width="150px" alt="">
                <div class="text-center">
                    <h4>LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                    <h4>WILAYAH JATUT</h4>
                    <h4>PERIODE AGUSTUS 2023</h4>
                    <h4>TANGGAL 25-8-2023</h4>
                </div>
                <div class=""></div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <a href="{{ url('akunting/pengajuan-lpj') }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td style="width: 240px">COA</td>
                            <td style="width: 20px">:</td>
                            <td>{{ $bukti_lpj_detail->coa }}</td>
                        </tr>
                        <tr>
                            <td>Nama jenis Peralatan</td>
                            <td>:</td>
                            <td>{{ $bukti_lpj_detail->nama_jenis }}</td>
                        </tr>
                        <tr>
                            <td>Merk/type</td>
                            <td>:</td>
                            <td>{{ $bukti_lpj_detail->merk }}</td>
                        </tr>
                        <tr>
                            <td>Kuantitas</td>
                            <td>:</td>
                            <td>{{ $bukti_lpj_detail->kuantitas }}</td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>:</td>
                            <td>{{ number_format($bukti_lpj_detail->harga) }}</td>
                        </tr>
                        <tr>
                            <td>Pengajuan</td>
                            <td>:</td>
                            <td>{{ number_format($bukti_lpj_detail->harga * $bukti_lpj_detail->kuantitas) }}</td>
                        </tr>
                        <tr>
                            <td>Total Pembelian</td>
                            <td>:</td>
                            <td>{{ number_format($bukti_lpj_detail->harga_pembelian) }}</td>
                        </tr>
                        <tr>
                            <td>Selisih</td>
                            <td>:</td>
                            @php
                                $total_beli = $bukti_lpj_detail->harga * $bukti_lpj_detail->kuantitas;
                            @endphp
                            <td>{{ number_format($total_beli - $bukti_lpj_detail->harga_pembelian) }}</td>
                        </tr>
                        <tr>
                            <td>Struk Pembelian</td>
                            <td>:</td>
                            <td>
                                <img src="{{ Storage::url($bukti_lpj_detail->struk_pembelian ?? '') }}" width="50px" onclick="window.open('{{ Storage::url($bukti_lpj_detail->struk_pembelian ?? '') }}')">
                            </td>
                        </tr>
                    </table>
                </div>
                                
            </div>
            <!-- /.card-body -->
          </div>
    </div>
    {{-- <div class="col d-print-none">
        <div class="card">
            <div class="card-header">
                Bukti Transaksi
            </div>
            <div class="card-body d-flex">
                <img src="{{ Storage::url($lpj->struk_pembelian) }}" onclick="window.open('{{ Storage::url($lpj->struk_pembelian) }}')" class="w-50 mr-2" alt="">
                <img src="{{ Storage::url($lpj->bukti_pengembalian) }}" onclick="window.open('{{ Storage::url($lpj->bukti_pengembalian) }}')" class="w-50 mr-2" alt="">
            </div>
        </div>
    </div> --}}
</div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_detail_id){
            let modal = $('#modal_lpj').modal('show') 
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
        }
    </script>
@endpush