@extends('layouts.main')

@section('head-content')
    <style>
        #inventaris-table thead th {
            text-transform: uppercase;
        }
        .month-header {
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }
        .month-header:hover {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
        }
        .month-header.active-month {
            background-color: #007bff !important;
            color: #ffffff !important;
        }
    </style>
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card position-relative">
            <!-- Loading Overlay -->
            <div class="overlay" id="loading-overlay" style="display: flex;">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <i class="fas fa-3x fa-sync-alt fa-spin text-primary mb-2"></i>
                    <span class="font-weight-bold text-dark">Memuat data inventaris...</span>
                </div>
            </div>
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Inventaris Gol 2 dan Penyusutan <span id="selected-year-badge" class="badge badge-primary ml-2">Tahun {{ date('Y') }}</span></h3>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (Auth::check() && Auth::user()->hasRole('admin'))
                    @if (Auth::user()->code_area != 1010)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kode_branch" class="form-label">Pilih Cabang (AP):</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control">
                                        <option value="">-- Semua Cabang di Wilayah Anda --</option>
                                        @foreach ($branches as $b)
                                            <option value="{{ $b->kode_branch }}">{{ $b->unit }} ({{ $b->kode_branch }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tahun" class="form-label">Pilih Tahun:</label>
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">-- Tahun Ini ({{ date('Y') }}) --</option>
                                        @php
                                            $tahunSekarang = date('Y');
                                            $tahunAwal = $tahunSekarang - 5;
                                        @endphp
                                        @for ($i = $tahunSekarang; $i >= $tahunAwal; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bulan" class="form-label">Pilih Bulan:</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">-- Semua Bulan --</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Admin Pusat (code_area 1010) --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area" class="form-label">Pilih Area:</label>
                                    <select name="area" id="area" class="form-control">
                                        <option value="">-- Semua Area --</option>
                                        @foreach ($area as $row)
                                            <option value="{{ $row }}">{{ $row }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kode_branch" class="form-label">Pilih Cabang (AP):</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control">
                                        <option value="">-- Semua Cabang --</option>
                                        @foreach ($branches as $b)
                                            <option value="{{ $b->kode_branch }}">{{ $b->unit }} ({{ $b->kode_branch }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tahun" class="form-label">Pilih Tahun:</label>
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">-- Tahun Ini ({{ date('Y') }}) --</option>
                                        @php
                                            $tahunSekarang = date('Y');
                                            $tahunAwal = $tahunSekarang - 5;
                                        @endphp
                                        @for ($i = $tahunSekarang; $i >= $tahunAwal; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bulan" class="form-label">Pilih Bulan:</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">-- Semua Bulan --</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif (Auth::check() && Auth::user()->hasRole('akunting'))
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="form-label">Pilih Area</label>
                            <select name="area" id="area" class="form-control">
                                <option value="">-- Pilih Area --</option>
                                @foreach ($area as $row)
                                    <option value="{{ $row }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="form-label">Pilih Wilayah</label>
                            <select name="kode_branch" id="kode_branch" class="form-control">
                                <option value="">-- Pilih wilayah --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun" class="form-label">Pilih Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                <option value="">-- Pilih Tahun --</option>
                                @php
                                    $tahunSekarang = date('Y');
                                    $tahunAwal = $tahunSekarang - 5; // Mengambil 5 tahun ke belakang
                                @endphp
                                @for ($i = $tahunSekarang; $i >= $tahunAwal; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bulan" class="form-label">Pilih Bulan</label>
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="">-- Semua Bulan --</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                </div>
                @elseif (Auth::check() && Auth::user()->hasRole('admin-head'))
                    <form id="filter-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="region">Pilih Region:</label>
                                <select name="region" id="region" class="form-control">
                                    <option value="">-- Semua Region --</option>
                                    @for ($i = 0; $i < count($regions); $i++)
                                        <option value="{{ $regions[$i] }}">{{ $regions[$i] }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="area">Pilih Area:</label>
                                <select name="area" id="area" class="form-control"></select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="kode_branch">Pilih Branch:</label>
                                <select name="kode_branch" id="kode_branch" class="form-control"></select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="bulan">Pilih Bulan:</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    <option value="">-- Semua Bulan --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                        </div>
                    </form>
                @endif

                <div class="table-responsive">
                    <table id="inventaris-table" class="table table-sm table-bordered table-striped text-nowrap" style="font-size: 14px; width: 100%;">
                        <thead>
                            <tr>
                                <th>KODE BRANCH</th>
                                <th>UNIT</th>
                                <th>IMEI</th>
                                <th>JENIS</th>
                                <th>TYPE</th>
                                <th>KETERANGAN</th>
                                <th>TGL PEROLEHAN</th>
                                <th>JUMLAH</th>
                                <th>HARGA PEROLEHAN</th>
                                <th>UMUR</th>
                                <th>PENYUSUTAN PER BULAN</th>
                                <th>TOTAL PENYUSUTAN AWAL</th>
                                <th class="month-header" data-month="1" title="Klik untuk filter posisi bulan Januari">JANUARI</th>
                                <th class="month-header" data-month="2" title="Klik untuk filter posisi bulan Februari">FEBRUARI</th>
                                <th class="month-header" data-month="3" title="Klik untuk filter posisi bulan Maret">MARET</th>
                                <th class="month-header" data-month="4" title="Klik untuk filter posisi bulan April">APRIL</th>
                                <th class="month-header" data-month="5" title="Klik untuk filter posisi bulan Mei">MEI</th>
                                <th class="month-header" data-month="6" title="Klik untuk filter posisi bulan Juni">JUNI</th>
                                <th class="month-header" data-month="7" title="Klik untuk filter posisi bulan Juli">JULI</th>
                                <th class="month-header" data-month="8" title="Klik untuk filter posisi bulan Agustus">AGUSTUS</th>
                                <th class="month-header" data-month="9" title="Klik untuk filter posisi bulan September">SEPTEMBER</th>
                                <th class="month-header" data-month="10" title="Klik untuk filter posisi bulan Oktober">OKTOBER</th>
                                <th class="month-header" data-month="11" title="Klik untuk filter posisi bulan November">NOVEMBER</th>
                                <th class="month-header" data-month="12" title="Klik untuk filter posisi bulan Desember">DESEMBER</th>
                                <th>TOTAL PENYUSUTAN BERJALAN</th>
                                <th>AKUMULASI PENYUSUTAN</th>
                                <th>NILAI BUKU TERAKHIR</th>
                                <th>PENYUSUTAN KE</th>
                                <th>NIP</th>
                                <th>PENGGUNA</th>
                                <th>JABATAN</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="8">TOTAL</th>
                                <th id="total-harga">0</th>
                                <th id="total-umur">0</th>
                                <th id="total-penyusutan-perbulan">0</th>
                                <th id="total-penyusutan-awal">0</th>
                                <th id="total-januari">0</th>
                                <th id="total-februari">0</th>
                                <th id="total-maret">0</th>
                                <th id="total-april">0</th>
                                <th id="total-mei">0</th>
                                <th id="total-juni">0</th>
                                <th id="total-juli">0</th>
                                <th id="total-agustus">0</th>
                                <th id="total-september">0</th>
                                <th id="total-oktober">0</th>
                                <th id="total-november">0</th>
                                <th id="total-desember">0</th>
                                <th id="total-penyusutan-berjalan">0</th>
                                <th id="total-akumulasi-penyusutan">0</th>
                                <th id="total-nilai-buku-terakhir">0</th>
                                <th id="total-penyusutan-ke">0</th>
                                <th id="total-pengguna">0</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <!-- Data will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div><!-- /.container-fluid -->
@endsection

@push('js')
    @include('inventaris_gol_2.script')
@endpush
