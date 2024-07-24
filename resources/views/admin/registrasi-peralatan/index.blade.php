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
    <div class="card-header">
        <a href="{{ route('admin.registrasi-peralatan.create') }}" class="btn btn-primary">Tambah</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Nama Cabang</th>
          <th>Nama Peralatan</th>
          <th>QR</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($registrasi_peralatan as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->pengajuan->nama_cabang }}</td>
                <td>{{ $item->pengajuan->merk }}</td>
                <td>
                  <a href="{{ route('admin.generate',$item->id) }}" class="btn btn-primary">Generate</a>
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection