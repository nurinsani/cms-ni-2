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
                <li class="breadcrumb-item"><a href="{{ route('it.pengajuan_peralatan') }}">Approval Peralatan</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-print-none">
            <h3 class="card-title font-weight-bold">
                Nomor Pengajuan: <span class="text-primary">{{ $pengajuan->nomor }}</span>
            </h3>
            <div class="card-tools">
                <a href="{{ route('it.pengajuan_peralatan') }}" class="btn btn-sm btn-secondary">
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
                            <th>Lokasi (AP / Cabang)</th>
                            <th>Keperluan</th>
                            <th class="text-center" style="width: 75px;">Kuantitas</th>
                            <th class="text-right">Harga Satuan</th>
                            <th class="text-right">Total</th>
                            <th>Keterangan & Rekening</th>
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
                                        <br><small class="text-muted font-weight-normal">{{ $item->merk }} {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="align-middle small">
                                    @if ($item->keterangan)
                                        <div>{{ $item->keterangan }}</div>
                                    @endif
                                    <span class="text-muted">
                                        {{ $pengajuan->user?->name }}, {{ $pengajuan->user?->no_rek }} ({{ $pengajuan->user?->nama_bank }})
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Tidak ada rincian item pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right"><strong>Subtotal</strong></td>
                            <td class="text-right text-primary"><strong>Rp {{ number_format($pengajuan->subtotal(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
