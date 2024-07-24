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

                .card-header {
                    padding-top: 50px;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin-left: 0.2in;
                    margin-right: 0.2in;
                    margin-top: 0;
                    margin-bottom: 0;
                }
            }
        </style>
    @endpush

    <div class="card">
        <div class="card-header mt-5 d-print-flex justify-content-between d-none">
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg" width="150px" alt="">
            <div class="text-center">
                <h4>PERMOHONAN DANA</h4>
                {{-- <h4>WILAYAH {{ $pengajuan->area->area }}</h4> --}}
                {{-- <h4>PERIODE AGUSTUS 2023</h4> --}}
                <h4>TANGGAL {{ $pengajuan->tanggal_pengajuan }}</h4>
            </div>
            <div class=""></div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary mb-3 d-print-none">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
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
                            <th scope="col">No</th>
                            <th scope="col">Nama AP</th>
                            <th scope="col">COA</th>
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
                                        <li> Pembelian {{ $item->jenis_peralatan->nama_jenis }} {{ $item->merk }} {{ $item->type }} </li>
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
                            <td colspan="6"><strong>Subtotal</strong></td>
                            <td colspan="2"><strong>Rp. {{ number_format($pengajuan->subtotal()) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($pengajuan->status_approve == 'lpj-admin')
                <div class="d-flex justify-content-between mt-4">
                    <div class="col text-center">
                        <p>Diajukan Oleh,</p>
                        <div class="py-4"></div>
                        <span class="border-bottom pb-1">{{ $pengajuan->user->name ?? '' }}</span>
                        <br>
                        <b class="mt-1 d-block">Admin</b>
                    </div>
                    <div class="col text-center">
                        <p>Diketahui Oleh,</p>
                        <div class="py-4"></div>
                        <span class="border-bottom pb-1">{{ $pengajuan->atasan1->name ?? '' }}</span>
                        <br>
                        <b class="mt-1 d-block">Admin Head</b>
                    </div>
                    <div class="col text-center">
                        <p>Diperiksa Oleh,</p>
                        <div class="py-4"></div>
                        <span class="border-bottom pb-1">{{ $pengajuan->atasan2->name ?? '' }}</span>
                        <br>
                        @if ($pengajuan->atasan2->username == 'it')
                            <b class="mt-1 d-block">Kabag IT</b>
                        @else
                            <b class="mt-1 d-block">Akunting</b>
                        @endif
                    </div>
                    <div class="col text-center">
                        <p>Disetujui Oleh,</p>
                        <div class="py-4"></div>
                        <span class="border-bottom pb-1">{{ $pengajuan->atasan3->name ?? '' }}</span>
                        <br>
                        <b class="mt-1 d-block">Pengurus</b>
                    </div>
                </div>
                <div class="col-4 mt-5">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">DIPROSES,</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="w-50 p-5 text-center">
                                    {{ $pengajuan->atasan4->name ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    Finance
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($pengajuan->status_approve == 'lpj-admin')
                {{-- <a href="{{ url('admin/export-pdf-peralatan', $pengajuan->id) }}" class="btn btn-sm btn-danger">Cetak PDF</a> --}}
                <button type="button" onclick="window.print()" class="btn btn-sm btn-danger mt-5 d-print-none">Cetak PDF</button>
            @endif

        </div>
        <!-- /.card-body -->
    </div>
@endsection
