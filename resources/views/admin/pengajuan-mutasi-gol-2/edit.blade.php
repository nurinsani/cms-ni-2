@extends('layouts.main')

@section('head-content')
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
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <form action="{{ route('admin.pengajuan-mutasi.update', $pengajuan_mutasi->id) }}" method="post"
                class="col-md-6" enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Peralatan</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2bs4 @error('peralatan_id') is-invalid @enderror"
                            name="peralatan_id">
                            <option value="" hidden>-- Pilih Peralatan --</option>
                            @foreach ($peralatan as $data)
                                <option value="{{ $data->id }}"
                                    {{ old('peralatan_id') == $data->id ? 'selected' : '' }}
                                    @if ($pengajuan_mutasi->peralatan_id == $data->id) selected @endif>{{ $data->nama_peralatan }}</option>
                            @endforeach
                        </select>
                        @error('pengajuan_detail_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Ke AP</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="ke_cabang">
                            <option value="" hidden>-- Pilih Nama Ap --</option>
                            @foreach ($branch as $data)
                                <option value="{{ $data->kode_branch }}" {{ old('ke_cabang') }}
                                    @if ($pengajuan_mutasi->ke_cabang == $data->kode_branch) selected @endif>{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('ke_cabang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Bulan Mutasi</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2bs4 @error('bulan_mutasi') is-invalid @enderror"
                            name="bulan_mutasi">
                            <option value="" hidden>-- Pilih Bulan --</option>
                            <option value="Januari" @if ($pengajuan_mutasi->bulan_mutasi == 'Januari') selected @endif>Januari</option>
                            <option value="Februari" @if ($pengajuan_mutasi->bulan_mutasi == 'Februari') selected @endif>Februari</option>
                            <option value="Maret" @if ($pengajuan_mutasi->bulan_mutasi == 'Maret') selected @endif>Maret</option>
                            <option value="April" @if ($pengajuan_mutasi->bulan_mutasi == 'April') selected @endif>April</option>
                            <option value="Mei" @if ($pengajuan_mutasi->bulan_mutasi == 'Mei') selected @endif>Mei</option>
                            <option value="Juni" @if ($pengajuan_mutasi->bulan_mutasi == 'Juni') selected @endif>Juni</option>
                            <option value="Juli" @if ($pengajuan_mutasi->bulan_mutasi == 'Juli') selected @endif>Juli</option>
                            <option value="Agustus" @if ($pengajuan_mutasi->bulan_mutasi == 'Agustus') selected @endif>Agustus</option>
                            <option value="September" @if ($pengajuan_mutasi->bulan_mutasi == 'September') selected @endif>September</option>
                            <option value="Oktober" @if ($pengajuan_mutasi->bulan_mutasi == 'Oktober') selected @endif>Oktober</option>
                            <option value="November" @if ($pengajuan_mutasi->bulan_mutasi == 'November') selected @endif>November</option>
                            <option value="Desember" @if ($pengajuan_mutasi->bulan_mutasi == 'Desember') selected @endif>Desember</option>
                        </select>
                        @error('bulan_mutasi')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-9">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
@endsection
