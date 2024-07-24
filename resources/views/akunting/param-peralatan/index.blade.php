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
        <a href="{{ route('akunting.param-peralatan.create') }}" class="btn btn-primary">Tambah</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>COA</th>
          <th>Nama Peralaran</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($param_peralatan as $jenis)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $jenis->coa }}</td>
            <td>{{ $jenis->nama }}</td>
            <td>
              <a href="{{ route('akunting.param-peralatan.edit', $jenis->id) }}" class="btn btn-sm btn-primary">Edit</a>
              <button type="submit" onclick="hapus_data('#delete_form{{ $jenis->id }}')" class="btn btn-sm btn-danger">Hapus</button>
              <form class="d-inline" id="delete_form{{ $jenis->id }}" action="{{ route('akunting.param-peralatan.destroy', $jenis->id) }}" method="post">
                @csrf
                @method('delete')
              </form>
            </td>
          </tr>
          @endforeach
        </tfoot>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection