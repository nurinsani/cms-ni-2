@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('atasan-it.dashboard') }}">Home</a></li>
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

    <div class="card bg-gradient-primary shadow-sm mb-4 border-0">
        <div class="card-body py-4 px-4">
            <h4 class="font-weight-bold mb-1 text-white">Selamat Datang, {{ Auth::user()->name }}!</h4>
            <p class="mb-0 text-white-50">
                Panel Approval Atasan IT &mdash; Kelola dan tinjau persetujuan pengajuan peralatan, laporan
                pertanggungjawaban (LPJ), dan klaim pembelian.
            </p>
        </div>
    </div>

    {{-- Small Boxes Summary --}}
    <div class="row">
        {{-- Approval Peralatan --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_peralatan->count() }}</h3>
                    <p class="font-weight-bold mb-1">Approval Peralatan</p>
                    <small>Menunggu persetujuan peralatan / UM</small>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
                <a href="{{ url('atasan-it/approval-peralatan') }}" class="small-box-footer">
                    Lihat Antrean <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval LPJ --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_lpj->count() }}</h3>
                    <p class="font-weight-bold mb-1">Approval LPJ</p>
                    <small>Menunggu verifikasi laporan LPJ</small>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ url('atasan-it/approval-lpj') }}" class="small-box-footer">
                    Lihat Antrean <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Klaim --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_klaim->count() }}</h3>
                    <p class="font-weight-bold mb-1">Approval Klaim</p>
                    <small>Menunggu persetujuan klaim pembelian</small>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('atasan-it/klaim-pembelian') }}" class="small-box-footer">
                    Lihat Antrean <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
