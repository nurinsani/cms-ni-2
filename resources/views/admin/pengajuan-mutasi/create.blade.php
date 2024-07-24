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
      <form action="{{ route('admin.pengajuan-mutasi.store') }}" method="post" class="col-md-6" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="" class="form-label">Peralatan</label>
            </div>
            <div class="col-md-9">
                <select class="form-control select2bs4 @error('peralatan_id') is-invalid @enderror" name="peralatan_id">
                    <option value="" hidden>-- Pilih Peralatan --</option>
                    @foreach ($peralatan as $data)
                    <option value="{{ $data->id }}" {{ old('peralatan_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_peralatan }}</option>
                    @endforeach
                </select>
                @error('peralatan_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
                <label for="" class="form-label">Ke KP/Cabang/AP</label>
            </div>
            <div class="col-md-9">
              <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="ke_cabang">
                  <option value="" hidden>-- Pilih Nama Ap --</option>
                  @foreach ($branch as $data)
                  <option value="{{ $data->kode_branch }}">{{ $data->unit }}</option>
                  @endforeach
              </select>
              @error('ke_cabang') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
                <label for="" class="form-label">Bulan Mutasi</label>
            </div>
            <div class="col-md-9">
                <select class="form-control select2bs4 @error('bulan') is-invalid @enderror" name="bulan_mutasi">
                    <option value="" hidden>-- Pilih Bulan --</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                    <option value="Maret">Maret</option>
                    <option value="April">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Juni">Juni</option>
                    <option value="Juli">Juli</option>
                    <option value="Agustus">Agustus</option>
                    <option value="September">September</option>
                    <option value="Oktober">Oktober</option>
                    <option value="November">November</option>
                    <option value="Desember">Desember</option>
                </select>
                @error('bulan') <small class="text-danger">{{ $message }}</small> @enderror
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