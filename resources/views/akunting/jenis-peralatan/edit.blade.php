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
        <form action="{{ route('akunting.jenis-peralatan.update', $jenis_peralatan->id) }}" method="post" class="col-md-6">
            @method('PUT')
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="" class="form-label">Nama Peralatan</label>
                </div>
                <div class="col-md-9">
                    <select name="param_peralatan_id" class="form-control @error('peralatan_id') is-invalid @enderror">
                        <option value="" hidden>-- Pilih Nama Peralatan --</option>
                        @foreach ($param_peralatan as $data)
                        <option value="{{ $data->id }}" {{ old('param_peralatan_id', $jenis_peralatan->param_peralatan_id) == $data->id ? 'selected' : '' }}>{{ $data->nama }}</option>
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
                    <input type="text" class="form-control @error('nama_jenis') is-invalid @enderror" value="{{old('nama_jenis', $jenis_peralatan->nama_jenis)}}" name="nama_jenis" placeholder="Masukan Nama Jenis">
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
                    <option value="umum"
                      @if ($jenis_peralatan->group_approval == 'umum')
                          selected
                      @endif
                    >Umum</option>
                    <option value="it"
                    @if ($jenis_peralatan->group_approval == 'it')
                        selected
                    @endif
                    >IT</option>
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
                  <option value="sewa"
                    @if ($jenis_peralatan->group_bdd == 'sewa')
                      selected
                    @endif
                  >Sewa</option>
                  <option value="asuransi"
                  @if ($jenis_peralatan->group_bdd == 'asuransi')
                    selected
                  @endif
                  >Asuransi</option>
              </select>
              @error('group_bdd') <small class="text-danger">{{ $message }}</small> @enderror
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