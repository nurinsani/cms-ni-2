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
                <div class="card-body">
                    <a href="{{ url('akunting/approval-lpj') }}" class="btn btn-sm btn-primary mb-3">
                        <i class="fas fa-arrow-left"></i> Kembali</a>
                    <ul class="p-0" style="list-style: none">
                        <li>Nomor : {{ $lpj->nomor }}</li>
                        <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
                        <li>Dari : {{ $lpj->user->name }}</li>
                        <li>Tanggal : {{ $lpj->tanggal_pengajuan->format('d-M-Y'); }}</li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">AP</th>
                                    <th scope="col">COA</th>
                                    <th scope="col">Nama jenis Peralatan</th>
                                    <th scope="col">Merk/Type</th>
                                    <th scope="col">Pengajuan</th>
                                    <th scope="col">Harga Pembelian</th>
                                    <th>Struk pembelian</th>
                                    <th>Bukti Pengembalian</th>
                                    {{-- <th scope="col">Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lpj->pengajuan_details as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->ap->unit ?? $item->area->area ?? $item->region->region }}</td>

                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                        <td>{{ $item->merk }} {{ $item->type }}</td>
                                        <td>Rp. {{ number_format($item->harga * $item->kuantitas) }}</td>
                                        <td>Rp. {{ number_format($item->harga_pembelian) }}</td>
                                        <td><a href="" onclick="window.open('{{ url('images/lpj/struk_pembelian/'.$item->struk_pembelian) }}')">{{ $item->struk_pembelian }}</a></td>
                                        <td><a href="" onclick="window.open('{{ url('images/lpj/bukti_pengembalian/'.$lpj->bukti_pengembalian) }}')">{{ $lpj->bukti_pengembalian }}</a></td>

                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"><strong>Total Uang Muka</strong></td>
                                    <td colspan="3"><strong>Rp. {{ number_format($lpj->subtotal()) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6"><strong>Total Pembelian</strong></td>
                                    <td colspan="2"><strong>Rp. {{ number_format($lpj->total_pembelian()) }}</strong></td> 
                                </tr>
                                @if ($lpj->total_pembelian() > $lpj->subtotal())
                                    <tr>
                                        <td colspan="6"><strong>Kekurangan</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->total_pembelian() - $lpj->subtotal())}}</strong></td> 
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="6"><strong>Sisa/Pengembalian</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->subtotal() - $lpj->total_pembelian())}}</strong></td> 
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_detail_id) {
            let modal = $('#modal_lpj').modal('show')
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
        }
    </script>
@endpush
