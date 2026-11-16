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
                <li class="breadcrumb-item"><a href="{{ route('it.pengajuan.index') }}">Pengajuan</a></li>
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
        {{-- Header Cetak --}}
        <div class="card-header mt-4 d-print-flex justify-content-between d-none">
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg"
                width="150px" alt="Logo">
            <div class="text-center">
                <h4 class="font-weight-bold mb-1">PERMOHONAN DANA</h4>
                <h5 class="mb-0">TANGGAL {{ $pengajuan->tanggal_pengajuan?->format('d-M-Y') ?? '-' }}</h5>
            </div>
            <div></div>
        </div>

        {{-- Header Layar --}}
        <div class="card-header d-print-none">
            <h3 class="card-title font-weight-bold">
                Nomor Pengajuan: <span class="text-primary">{{ $pengajuan->nomor }}</span>
            </h3>
            <div class="card-tools">
                <a href="{{ url('it/pengajuan') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                @if ($pengajuan->status_approve == 'lpj-admin')
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-danger ml-1">
                        <i class="fas fa-print mr-1"></i> Cetak PDF
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $pengajuan->nomor }}</td>
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
                            <td>{{ $pengajuan->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $pengajuan->tanggal_pengajuan?->format('d-M-Y') ?? '-' }}</td>
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
                            <th>Keperluan / Barang</th>
                            <th class="text-center" style="width: 65px;">Qty</th>
                            <th class="text-right" style="width: 140px;">Harga Satuan</th>
                            <th class="text-right" style="width: 150px;">Total</th>
                            <th>Berkas MOA</th>
                            <th>Rekening Tujuan & Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle font-weight-bold">
                                    {{ $item->jenis_peralatan?->nama_jenis ?? '-' }}
                                    @if ($item->merk || $item->type)
                                        <br><small class="text-muted font-weight-normal">{{ $item->merk }}
                                            {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp
                                    {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="align-middle">
                                    @if (empty($item->moa))
                                        <span class="text-muted small"><i>(Tidak ada berkas)</i></span>
                                    @else
                                        <a href="{{ url('files/moa/' . $item->moa) }}" target="_blank"
                                            class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-file-download mr-1"></i> Lihat MOA
                                        </a>
                                    @endif
                                </td>
                                <td class="align-middle small">
                                    @if ($item->rekening_id == null || !$item->rekening)
                                        Transfer ke:<br>
                                        <strong>{{ $pengajuan->user?->nama_bank }} -
                                            {{ $pengajuan->user?->no_rek }}</strong><br>
                                        a.n. {{ $pengajuan->user?->atas_nama }}
                                    @else
                                        Transfer ke:<br>
                                        <strong>{{ $item->rekening->nama_bank }} -
                                            {{ $item->rekening->no_rek }}</strong><br>
                                        a.n. {{ $item->rekening->atas_nama }}
                                    @endif
                                    @if ($item->keterangan)
                                        <hr class="my-1">
                                        <span class="text-muted">Ket: {{ $item->keterangan }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada rincian item pengajuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <td colspan="5" class="text-right"><strong>Total Uang Muka</strong></td>
                            <td class="text-right text-primary"><strong>Rp
                                    {{ number_format($pengajuan->subtotal(), 0, ',', '.') }}</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
