@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.data-user.index') }}">Data User</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
        <form action="{{ route('akunting.param-peralatan.update', $peralatan->id) }}" method="post" class="col-md-6">
            @method('PUT')
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">COA</label>
                </div>
                <div class="col-md-9">
                    <input type="number" class="form-control @error('coa') is-invalid @enderror" value="{{old('coa', $peralatan->coa)}}" name="coa" placeholder="Masukan COA">
                    @error('coa') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Nama Jenis</label>
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" value="{{old('nama', $peralatan->nama)}}" name="nama" placeholder="Masukan Nama Jenis">
                    @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
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
  </div>
@endsection