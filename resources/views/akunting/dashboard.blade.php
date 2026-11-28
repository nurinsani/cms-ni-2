@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    @push('css')
        <style>
            @media (max-width: 767.98px) {
                .small-box,
                .small-box .inner {
                    text-align: left !important;
                }

                .small-box .inner {
                    padding: 15px 18px !important;
                }
            }
        </style>
    @endpush

    {{-- Welcome Banner --}}
    <div class="card bg-gradient-primary shadow-sm mb-4 border-0">
        <div class="card-body d-flex justify-content-between align-items-center py-4 px-4">
            <div>
                <h4 class="font-weight-bold mb-1 text-white">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="mb-0 text-white-50">
                    Panel Akunting &mdash; Monitoring persetujuan (approval) pengajuan cabang, pencatatan jurnal transaksi, dan aset inventaris.
                </p>
            </div>
        </div>
    </div>

    {{-- Approval Section --}}
    <div class="row">
        {{-- Approval Peralatan --}}
        <div class="col-xl-4 col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_peralatan_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Peralatan</p>
                    <small>Pengajuan FPD / UM peralatan</small>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <a href="{{ url('akunting/approval-peralatan') }}" class="small-box-footer">
                    Periksa Pengajuan <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval LPJ --}}
        <div class="col-xl-4 col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_lpj_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval LPJ</p>
                    <small>LPJ pembelian dari cabang</small>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ url('akunting/approval-lpj') }}" class="small-box-footer">
                    Periksa LPJ <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Klaim --}}
        <div class="col-xl-4 col-md-6 col-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_klaim_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Klaim</p>
                    <small>Klaim pembelian dari cabang</small>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('akunting/klaim-pembelian') }}" class="small-box-footer">
                    Periksa Klaim <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Mutasi --}}
        <div class="col-xl-6 col-md-6 col-12">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_mutasi_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Mutasi</p>
                    <small>Mutasi aset Gol 1 & Gol 2</small>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="{{ url('akunting/approval-mutasi') }}" class="small-box-footer">
                    Periksa Mutasi <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Hapus Buku --}}
        <div class="col-xl-6 col-md-6 col-12">
            <div class="small-box bg-secondary shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_hapus_buku_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Hapus Buku</p>
                    <small>Hapus buku aset Gol 1 & Gol 2</small>
                </div>
                <div class="icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <a href="{{ route('akunting.approval-hapus-buku.index') }}" class="small-box-footer">
                    Periksa Hapus Buku <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Ringkasan Nominative & Tabel Pending Approvals --}}
    <div class="row">
        {{-- Ringkasan Aset Inventaris --}}
        <div class="col-lg-4 col-12 mb-4">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-boxes mr-1"></i> Ringkasan Inventaris
                    </h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-around">
                    <div class="info-box bg-light shadow-none border mb-3">
                        <span class="info-box-icon bg-primary"><i class="fas fa-laptop"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted">Inventaris Golongan 1</span>
                            <span class="info-box-number text-dark" style="font-size: 20px;">
                                {{ number_format($total_inventaris_count ?? 0, 0, ',', '.') }}
                                <small class="text-muted font-weight-normal">Barang</small>
                            </span>
                            <a href="{{ url('akunting/inventaris') }}" class="small text-primary">Lihat daftar &rarr;</a>
                        </div>
                    </div>

                    <div class="info-box bg-light shadow-none border mb-0">
                        <span class="info-box-icon bg-teal"><i class="fas fa-mobile-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted">Inventaris Golongan 2</span>
                            <span class="info-box-number text-dark" style="font-size: 20px;">
                                {{ number_format($total_inventaris_gol2_count ?? 0, 0, ',', '.') }}
                                <small class="text-muted font-weight-normal">Barang</small>
                            </span>
                            <a href="{{ url('akunting/inventaris-gol-2') }}" class="small text-teal">Lihat daftar &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Antrean Pengajuan Menunggu Approval --}}
        <div class="col-lg-8 col-12 mb-4">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-clock mr-1"></i> Pengajuan Terbaru Menunggu Approval
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%" class="text-center">NO</th>
                                    <th>NOMOR PENGAJUAN</th>
                                    <th>NAMA AP</th>
                                    <th>JENIS PENGAJUAN</th>
                                    <th>TANGGAL</th>
                                    <th>STATUS</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pending_approvals as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">{{ $item->nomor_pengajuan }}</td>
                                        <td>
                                            @php
                                                $firstDetail = $item->pengajuan_details->first();
                                                $apName = $firstDetail->ap->nama_ap ?? ($item->area->nama_area ?? ($item->nama_ap ?? '-'));
                                            @endphp
                                            {{ $apName }}
                                        </td>
                                        <td>
                                            @if ($item->pengajuan_details->count() > 0)
                                                @foreach ($item->pengajuan_details as $detail)
                                                    <div>- {{ $detail->jenis_peralatan->param_peralatan->nama_barang ?? '-' }}</div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-M-Y') }}</td>
                                        <td>
                                            @if ($item->status_approve === 'akunting-it')
                                                <span class="badge badge-warning">UM Akunting</span>
                                            @elseif ($item->status_approve === 'lpj-akunting-it')
                                                <span class="badge badge-success">LPJ Akunting</span>
                                            @elseif ($item->status_approve === 'klaim-akunting-it')
                                                <span class="badge badge-info">Klaim Akunting</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $item->status_approve }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status_approve === 'akunting-it')
                                                <a href="{{ url('akunting/approval-peralatan/detail', $item->id) }}" class="btn btn-outline-info btn-xs">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                            @elseif ($item->status_approve === 'lpj-akunting-it')
                                                <a href="{{ url('akunting/approval-lpj/detail', $item->id) }}" class="btn btn-outline-info btn-xs">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                            @elseif ($item->status_approve === 'klaim-akunting-it')
                                                <a href="{{ url('akunting/klaim/detail', $item->id) }}" class="btn btn-outline-info btn-xs">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Tidak ada pengajuan yang sedang menunggu persetujuan Akunting.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
