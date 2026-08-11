@extends('layouts.main')

@section('head-content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.2/css/buttons.dataTables.min.css">  
    <style>
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
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inventaris dan Penyusutan</h3>
            </div>
            <div class="card-body">
                @if (Auth::check() && Auth::user()->hasRole('admin'))
                    @if (Auth::user()->code_area !==1010)
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

                    @if (Auth::user()->code_area == 1010)
                        <div class="row mb-4">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="" class="form-label">Pilih Wilayah</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control">
                                        <option value="">-- Pilih Wilayah --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                    @endif
                @endif


                @if (Auth::check() && Auth::user()->hasRole('akunting'))
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
                                <option value="">-- Pilih Wilayah --</option>
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
                @endif

                @if (Auth::check() && Auth::user()->hasRole('admin-head'))
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
                                <th>Kode Branch</th>
                                <th>Unit</th>
                                <th>Kode Peralatan</th>
                                <th>Jenis</th>
                                <th>Type</th>
                                <th>Keterangan</th>
                                <th>Tanggal Perolehan</th>
                                <th>Jumlah</th>
                                <th>Harga Perolehan</th>
                                <th>Umur</th>
                                <th>Penyusutan per Bulan</th>
                                <th>Total Penyusutan Awal</th>
                                <th class="month-header" data-month="1" title="Klik untuk filter posisi bulan Januari">Januari</th>
                                <th class="month-header" data-month="2" title="Klik untuk filter posisi bulan Februari">Februari</th>
                                <th class="month-header" data-month="3" title="Klik untuk filter posisi bulan Maret">Maret</th>
                                <th class="month-header" data-month="4" title="Klik untuk filter posisi bulan April">April</th>
                                <th class="month-header" data-month="5" title="Klik untuk filter posisi bulan Mei">Mei</th>
                                <th class="month-header" data-month="6" title="Klik me-filter posisi bulan Juni">Juni</th>
                                <th class="month-header" data-month="7" title="Klik me-filter posisi bulan Juli">Juli</th>
                                <th class="month-header" data-month="8" title="Klik me-filter posisi bulan Agustus">Agustus</th>
                                <th class="month-header" data-month="9" title="Klik me-filter posisi bulan September">September</th>
                                <th class="month-header" data-month="10" title="Klik me-filter posisi bulan Oktober">Oktober</th>
                                <th class="month-header" data-month="11" title="Klik me-filter posisi bulan November">November</th>
                                <th class="month-header" data-month="12" title="Klik me-filter posisi bulan Desember">Desember</th>
                                <th>Total Penyusutan Berjalan</th>
                                <th>Akumulasi Penyusutan</th>
                                <th>Nilai Buku Terakhir</th>
                                <th>Penyusutan Ke</th>
                                <th>Pengguna</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="8">Total</th>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <!-- Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <!-- JSZip for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.0/jszip.min.js"></script>

    @include('inventaris.script')
    
@endsection
