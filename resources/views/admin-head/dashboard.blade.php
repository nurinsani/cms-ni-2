@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
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
                    Panel Admin Head &mdash; Monitoring pengajuan uang muka, persetujuan (approval) cabang wilayah, dan aset
                    inventaris.
                </p>
            </div>
        </div>
    </div>

    {{-- Approval Section --}}
    <div class="row">
        {{-- Approval Peralatan --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_peralatan_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Peralatan</p>
                    <small>Pengajuan UM peralatan cabang</small>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <a href="{{ url('admin-head/pengajuan-peralatan') }}" class="small-box-footer">
                    Periksa Pengajuan <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval LPJ --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_lpj_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval LPJ</p>
                    <small>LPJ pembelian dari cabang</small>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ url('admin-head/approval-lpj') }}" class="small-box-footer">
                    Periksa LPJ <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Klaim --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_klaim_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Klaim</p>
                    <small>Klaim pembelian dari cabang</small>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('admin-head/klaim-pembelian') }}" class="small-box-footer">
                    Periksa Klaim <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Approval Mutasi --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $approval_mutasi_count ?? 0 }}</h3>
                    <p class="font-weight-bold mb-1">Approval Mutasi</p>
                    <small>Mutasi aset Gol 1 & Gol 2</small>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="{{ url('admin-head/approval-mutasi') }}" class="small-box-footer">
                    Periksa Mutasi <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
