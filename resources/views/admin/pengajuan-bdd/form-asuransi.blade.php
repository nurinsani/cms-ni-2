@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      {{-- <h1 class="m-0">{{ $title }}</h1> --}}
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
    <div class="card-header">
        {{ $title }}
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pengajuan-bdd.store-asuransi') }}" method="post" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>-- Pilih Nama AP --</label>
                        <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="kode_branch">
                            <option value="" hidden>-- Pilih Nama Ap --</option>
                            @foreach ($branch as $data)
                            <option value="{{ $data->kode_branch }}" {{ old('kode_branch') == $data->kode_branch ? 'selected' : '' }}>{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('kode_branch') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Pengajuan</label>
                        <select class="form-control select2bs4 @error('jenis_bdd_id') is-invalid @enderror" name="jenis_bdd_id">
                            <option value="" hidden>-- Pilih Jenis BDD --</option>
                            @foreach ($jenis as $data)
                            <option value="{{ $data->id }}" {{ old('jenis_bdd_id') == $data->id ? 'selected' : '' }}>{{ $data->nama_jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_bdd_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
        
                    <div class="form-group">
                        <label>Jenis Asuransi</label>
                        <input type="text" class="form-control @error('jenis_asuransi') is-invalid @enderror" value="{{old('jenis_asuransi')}}" name="jenis_asuransi" placeholder="Masukkan Jenis Asuransi">
                        @error('jenis_asuransi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="" class="form-label">Produk Asuransi</label>
                        <input type="text" class="form-control @error('produk_asuransi') is-invalid @enderror" value="{{old('produk_asuransi')}}" name="produk_asuransi" placeholder="Masukkan Produk Asuransi">
                        @error('produk_asuransi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
        
                    <div class="form-group">
                        <label for="" class="form-label">No. Polis</label>
                        <input type="file" class="form-control @error('no_polis') is-invalid @enderror" value="{{old('no_polis')}}" name="no_polis" placeholder="Masukkan No Polis">
                        @error('no_polis') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
        
                    <div class="form-group">
                        <label for="" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" value="{{old('nama_perusahaan')}}" name="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">
                        @error('nama_perusahaan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
        
                <div class="col-md-6">
        
                    <div class="form-group">
                        <label for="" class="form-label">Penjaminan</label>
                        <input type="text" class="form-control @error('penjaminan') is-invalid @enderror" value="{{old('penjaminan')}}" name="penjaminan" placeholder="Masukkan Penjaminan">
                        @error('penjaminan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="" class="form-label">Harga pertanggungan</label>
                        <input type="text" class="form-control @error('harga_pertanggungan') is-invalid @enderror" value="{{old('harga_pertanggungan')}}" name="harga_pertanggungan" placeholder="Masukkan Harga Pertanggungan">
                        @error('harga_pertanggungan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="" class="form-label">No Telp</label>
                        <input type="number" class="form-control @error('no_pic_ar') is-invalid @enderror" value="{{old('no_pic_ar')}}" name="no_pic_ar" placeholder="Masukkan No Telp">
                        @error('no_pic_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
        
                    <div class="form-group">
                        <label for="" class="form-label">Jangka Waktu</label>
                        <input type="number" class="form-control @error('jangka_waktu_amortisasi') is-invalid @enderror" value="{{old('jangka_waktu_amortisasi')}}" name="jangka_waktu_amortisasi" placeholder="Masukkan Jangka Waktu">
                        @error('jangka_waktu_amortisasi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
              
                </div>
            </div>
        
            <button type="submit" class="btn btn-primary">Simpan</button>
        
        </form>
    </div>
</div>
@endsection