@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">
                {{ $title }}
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('it/klaim') }}">Klaim</a></li>
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
                    display: none !important;
                }

                .card-header {
                    padding-top: 30px;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin: 0.3in;
                }
            }
        </style>
    @endpush

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header mt-4 d-print-flex justify-content-between d-none">
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg" width="150px" alt="Logo">
            <div class="text-center">
                <h4 class="font-weight-bold mb-1">PERMOHONAN KLAIM DANA</h4>
                <h5 class="mb-0">TANGGAL {{ $klaim->tanggal_pengajuan?->format('d-M-Y') ?? $klaim->tanggal_pengajuan }}</h5>
            </div>
            <div></div>
        </div>

        <div class="card-header d-print-none">
            <h3 class="card-title font-weight-bold">
                Nomor Klaim: <span class="text-primary">{{ $klaim->nomor }}</span>
            </h3>
            <div class="card-tools">
                <a href="{{ url('it/klaim') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $klaim->nomor }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Kepada</td>
                            <td>:</td>
                            <td>Ketua dan Pengurus KSPPS Nur Insani</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $klaim->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $klaim->tanggal_pengajuan?->format('d-M-Y') ?? $klaim->tanggal_pengajuan }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Branch / Lokasi</th>
                            <th>COA</th>
                            <th>Keperluan</th>
                            <th class="text-center" style="width: 75px;">Kuantitas</th>
                            <th class="text-right">Harga Satuan</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Bukti Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($klaim->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle font-weight-bold">
                                    Pembelian {{ $item->jenis_peralatan?->nama_jenis ?? '-' }} {{ $item->merk }} {{ $item->type }}
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->struk_pembelian)
                                        <a href="{{ url('files/struk_pembelian/' . $item->struk_pembelian) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-file-invoice mr-1"></i> Lihat Struk
                                        </a>
                                    @else
                                        <span class="text-muted small"><i>(Tidak ada struk)</i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada rincian item klaim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total Klaim Pembelian</strong></td>
                            <td class="text-right text-primary"><strong>Rp {{ number_format($klaim->subtotal(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
