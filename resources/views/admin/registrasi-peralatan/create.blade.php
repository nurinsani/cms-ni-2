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
      <form action="{{ route('admin.registrasi-peralatan.store') }}" method="post" class="col-md-6" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="" class="form-label">Peralatan</label>
            </div>
            <div class="col-md-10">
                <select class="form-control select2bs4 @error('pengajuan_id') is-invalid @enderror" name="pengajuan_id">
                    <option value="" hidden>-- Pilih Peralatan --</option>
                    @foreach ($peralatan as $data)
                    <option value="{{ $data->id }}" {{ old('pengajuan_id') == $data->id ? 'selected' : '' }}>{{ $data->merk }}</option>
                    @endforeach
                </select>
                @error('pengajuan_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

        <div class="row mb-2">
            <div class="col-md-2">  
            </div>
            <div class="col-md-10">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
    </div>
    <!-- /.card-body -->
  </div>
@endsection