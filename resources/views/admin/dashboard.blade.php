@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
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
        <div class="card-body d-flex justify-content-between align-items-center py-4 px-4">
            <div>
                <h4 class="font-weight-bold mb-1 text-white">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="mb-0 text-white-50">
                    Panel Admin &mdash; Kelola pengajuan uang muka peralatan, laporan pertanggungjawaban (LPJ), dan klaim
                    pembelian.
                </p>
            </div>
        </div>
    </div>

    {{-- Small Boxes Summary --}}
    <div class="row">
        {{-- Pengajuan Uang Muka --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $pengajuan_um_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Pengajuan Uang Muka</p>
                    <small>Pengajuan pembelian peralatan / UM</small>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
                <a href="{{ url('admin/pengajuan') }}" class="small-box-footer">
                    Lihat Pengajuan <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Pengajuan LPJ --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $pengajuan_lpj_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Pengajuan LPJ</p>
                    <small>Laporan pertanggungjawaban pembelian</small>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ url('admin/lpj') }}" class="small-box-footer">
                    Lihat LPJ <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Pengajuan Klaim --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $pengajuan_klaim_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Pengajuan Klaim</p>
                    <small>Klaim penggantian dana pembelian</small>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('admin/klaim-pembelian') }}" class="small-box-footer">
                    Lihat Klaim <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
