@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('akunting.param-peralatan.index') }}">Peralatan</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
        <form action="{{ route('akunting.param-peralatan.store') }}" method="post" class="col-md-6">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">COA</label>
                </div>
                <div class="col-md-9">
                    <input type="number" class="form-control @error('coa') is-invalid @enderror" value="{{old('coa')}}" name="coa" placeholder="Masukan COA">
                    @error('coa') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Nama Peralatan</label>
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" value="{{old('nama')}}" name="nama" placeholder="Masukan Nama Peralatan">
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