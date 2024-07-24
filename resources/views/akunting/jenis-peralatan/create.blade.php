@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('akunting.jenis-peralatan.index') }}">Jenis Peralatan</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
        <form action="{{ route('akunting.jenis-peralatan.store') }}" method="post" class="col-md-6">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Nama Peralatan</label>
                </div>
                <div class="col-md-9">
                    <select name="param_peralatan_id" class="form-control @error('peralatan_id') is-invalid @enderror">
                        <option value="" hidden>-- Pilih Nama Peralatan --</option>
                        @foreach ($param_peralatan as $data)
                        <option value="{{ $data->id }}" {{ old('param_peralatan_id') == $data->id ? 'selected' : '' }}>{{ $data->nama }} - {{ $data->coa }}</option>
                        @endforeach
                    </select>
                    @error('peralatan_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Jenis Peralatan</label>
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control @error('nama_jenis') is-invalid @enderror" value="{{old('nama_jenis')}}" name="nama_jenis" placeholder="Masukan Nama Jenis">
                    @error('nama_jenis') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-3">
                  <label for="" class="form-label">Group Approval</label>
              </div>
              <div class="col-md-9">
                <select name="group_approval" class="form-control @error('group_approval') is-invalid @enderror">
                    <option value="" hidden>-- Pilih Group Approval --</option>
                    <option value="umum">Umum</option>
                    <option value="it">IT</option>
                </select>
                @error('group_approval') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
                <label for="" class="form-label">Group BDD</label>
            </div>
            <div class="col-md-9">
              <select name="group_bdd" class="form-control @error('group_bdd') is-invalid @enderror">
                  <option value="" hidden>-- Pilih Group BDD --</option>
                  <option value="sewa">Sewa</option>
                  <option value="asuransi">Asuransi</option>
              </select>
              @error('group_bdd') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-3">
              <label for="" class="form-label">Group Inventris</label>
          </div>
          <div class="col-md-9">
            <select name="group_inventaris" class="form-control @error('group_inventaris') is-invalid @enderror">
                <option value="" hidden>-- Pilih Group Inventaris --</option>
                <option value="ya">YA</option>
                <option value="tidak">TIDAK</option>
            </select>
            @error('group_inventaris') <small class="text-danger">{{ $message }}</small> @enderror
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