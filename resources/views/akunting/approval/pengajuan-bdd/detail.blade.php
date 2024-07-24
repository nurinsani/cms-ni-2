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
                        <h4>PENGAJUAN BDD</h4>
                        {{-- <h4>WILAYAH JATUT</h4>
                        <h4>PERIODE AGUSTUS 2023</h4>
                        <h4>TANGGAL 25-8-2023</h4> --}}
                    </div>
                    <div class=""></div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary mb-3">
                        <i class="fas fa-arrow-left"></i> 
                        Kembali
                    </a>
                    <ul class="p-0" style="list-style: none">
                        <li>Nomor : {{ $detail->nomor }}</li>
                        <li>Kepada : Pengurus</li>
                        <li>Tanggal : {{ $detail->tanggal }}</li>
                        <li>User : {{ $detail->user->name }}</li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Keperluan</th>
                                    <th scope="col">Harga Sewa</th>
                                    <th scope="col">Pajak</th>
                                    <th scope="col">Harga Sewa Net</th>
                                    <th scope="col">Tenor</th>
                                    <th scope="col">Biaya Perbulan</th>
                                    <th scope="col">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    @if ($detail->jenis_bdd->group_bdd == 'sewa')
                                        <td>Sewa {{ $detail->jenis_sewa }} - {{ $detail->lokasi }}</td>
                                    @else
                                        <td>Asuransi {{ $detail->jenis_asuransi }}</td>
                                    @endif
                                    <td>Rp. {{ number_format($detail->harga_pertanggungan ?? $detail->harga_sewa) }}</td>
                                    <td>
                                        @if ($detail->jenis_bdd->group_bdd == 'sewa')
                                        Rp. {{ number_format($detail->harga_sewa / 9) }}
                                        @else
                                        Rp. {{ number_format($detail->harga_pertanggungan) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($detail->jenis_bdd->group_bdd == 'sewa')
                                        Rp. {{ number_format(($detail->harga_sewa / 9) + $detail->harga_sewa) }}
                                        @else
                                        Rp. {{ number_format($detail->harga_pertanggungan) }}
                                        @endif
                                    </td>
                                    <td>{{ $detail->jangka_waktu_amortisasi }}</td>
                                    @if ($detail->jenis_bdd->group_bdd == 'sewa')
                                        <td>Rp.
                                            {{ number_format((($detail->harga_sewa / 90) + $detail->harga_sewa) / $detail->jangka_waktu_amortisasi) }}
                                        </td>
                                    @else
                                        <td>Rp.
                                            {{ number_format($detail->harga_pertanggungan / $detail->jangka_waktu_amortisasi) }}
                                        </td>
                                    @endif
                                    <td>
                                        <ul class="p-0" style="list-style: none">
                                            <li>Nomor Rekening Pemilik : {{ $detail->no_rek_bank_pemilik }}</li>
                                            <li>Nama Rekening Bank : {{ $detail->atas_nama }}</li>
                                            {{-- <li>User : {{ $detail->user->name }}</li> --}}
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($detail->status_proses == 'selesai')
                        <button type="button" onclick="window.print()"
                            class="btn btn-primary mt-5 d-print-none">Print</button>
                    @endif

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
                <img src="{{ Storage::url($detail->struk_pembelian) }}" onclick="window.open('{{ Storage::url($detail->struk_pembelian) }}')" class="w-50 mr-2" alt="">
                <img src="{{ Storage::url($detail->bukti_pengembalian) }}" onclick="window.open('{{ Storage::url($detail->bukti_pengembalian) }}')" class="w-50 mr-2" alt="">
            </div>
        </div>
    </div> --}}
    </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_id) {
            let modal = $('#modal_lpj').modal('show')
            modal.find('#pengajuan_id').val(pengajuan_id)
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
        }
    </script>
@endpush
