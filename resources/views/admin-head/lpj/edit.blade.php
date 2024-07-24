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
        <form action="{{ route('admin.lpj.update', $lpj->id) }}" method="post" class="col-md-6" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="" class="form-label">Pengajuan</label>
                </div>
                <div class="col-md-10">
                    <select class="form-control select2bs4 @error('pengajuan_id') is-invalid @enderror" name="pengajuan_id">
                        <option value="" hidden>-- Pilih Pengajuan --</option>
                        @foreach ($pengajuan as $data)
                        <option value="{{ $data->id }}" {{ old('pengajuan_id', $data->id) == $data->id ? 'selected' : '' }}>{{ $data->nomor }}</option>
                        @endforeach
                    </select>
                    @error('pengajuan_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="" class="form-label">Biaya</label>
                </div>
                <div class="col-md-10">
                    <input type="number" class="form-control @error('biaya') is-invalid @enderror" value="{{old('biaya', $lpj->biaya)}}" name="biaya" placeholder="Masukan Biaya">
                    @error('biaya') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="" class="form-label">Struk Pembelian</label>
                </div>
                <div class="col-md-10">
                    <img src="{{ Storage::url($lpj->bukti_pengembalian) }}" class="w-50 mb-3" alt="">
                    <input type="file" class="form-control @error('struk_pembelian') is-invalid @enderror" value="{{old('struk_pembelian', $lpj->struk_pembelian)}}" name="struk_pembelian" placeholder="Masukan struk_pembelian">
                    @error('struk_pembelian') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="" class="form-label">Bukti Pengembalian</label>
                </div>
                <div class="col-md-10">
                    <img src="{{ Storage::url($lpj->struk_pembelian) }}" class="w-50 mb-3" alt="">
                    <input type="file" class="form-control @error('bukti_pengembalian') is-invalid @enderror" value="{{old('bukti_pengembalian', $lpj->bukti_pengembalian)}}" name="bukti_pengembalian" placeholder="Masukan bukti_pengembalian User">
                    @error('bukti_pengembalian') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="" class="form-label">Keterangan</label>
                </div>
                <div class="col-md-10">
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" cols="10" rows="5" placeholder="Masukan Keterangan">{{old('keterangan', $lpj->keterangan)}}</textarea>
                    @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
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
  </div>
@endsection