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
    <div class="tab-content" id="custom-tabs-four-tabContent">
        <div class="tab-pane fade active show" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
            <form action="{{ route('admin.lpj-bdd.update', $detail->id) }}" method="post" class="col-md-6" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="" class="form-label">Kwitansi</label>
                    </div>
                    <div class="col-md-10">
                        <input type="file" class="form-control @error('kwitansi') is-invalid @enderror" value="{{old('kwitansi')}}" name="kwitansi" placeholder="Masukan Biaya Pembelian">
                        @error('kwitansi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="" class="form-label">Perjanjian</label>
                    </div>
                    <div class="col-md-10">
                        <input type="file" class="form-control @error('perjanjian') is-invalid @enderror" value="{{old('perjanjian')}}" name="perjanjian" placeholder="Masukan Biaya Pembelian">
                        @error('perjanjian') <small class="text-danger">{{ $message }}</small> @enderror
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
    </div>
</div>
@endsection