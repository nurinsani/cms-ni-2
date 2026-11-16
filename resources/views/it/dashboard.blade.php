@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">Dashboard IT</h1>
            <small class="text-muted">Ringkasan aktivitas pengajuan, approval, dan manajemen pengguna</small>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card bg-gradient-primary shadow-sm mb-4 border-0">
        <div class="card-body d-flex justify-content-between align-items-center py-4 px-4">
            <div>
                <h4 class="font-weight-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="mb-0 text-white-50">
                    Kelola pengajuan peralatan IT, verifikasi approval, dan atur hak akses pengguna melalui panel ini.
                </p>
            </div>
            <div class="d-none d-md-block">
                <a href="{{ route('it.data-user.index') }}" class="btn btn-light text-primary font-weight-bold shadow-sm mr-2">
                    <i class="fas fa-users-cog mr-1"></i> Kelola User
                </a>
                <a href="{{ url('it/pengajuan') }}" class="btn btn-outline-light font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Buat Pengajuan
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $belum_approve->count() }}</h3>
                    <p>Approval Peralatan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-laptop-medical"></i>
                </div>
                <a href="{{ route('it.pengajuan_peralatan') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>0</h3>
                    <p>Approval BDD</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-signature"></i>
                </div>
                <a href="{{ route('it.pengajuan_bdd') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $total_pengajuan_it ?? 0 }}</h3>
                    <p>Pengajuan Uang Muka IT</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ url('it/pengajuan') }}" class="small-box-footer">
                    Lihat Pengajuan <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary shadow-sm">
                <div class="inner">
                    <h3>{{ $total_user ?? 0 }}</h3>
                    <p>Total User Terdaftar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('it.data-user.index') }}" class="small-box-footer">
                    Kelola User <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
@endsection