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
        <a href="{{ route('admin.pengajuan-mutasi.create') }}" class="btn btn-sm btn-primary">
          <i class="fa fa-plus"></i> Tambah</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Nama Peralatan</th>
          <th>Dari AP</th>
          <th>Ke AP</th>
          <th>Bulan Mutasi</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($penganjuan_mutasi as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->peralatan->jenis }}</td>
                <td>{{ $item->peralatan->ap->unit }}</td>
                <td>{{ $item->branch->unit }}</td>
                <td>{{ $item->bulan_mutasi }}</td>
                <td>
                  <span class="badge bg-success">
                    {{ $item->status_approve }}</td>
                  </span>
                <td>
                  <a href="{{ route('admin.pengajuan-mutasi.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                  @if ($item->status_approve === 'mutasi-admin')
                  <button type="submit" onclick="hapus_data('#delete_form{{ $item->id }}')" class="btn btn-sm btn-danger">Hapus</button>
                  <form class="d-inline" id="delete_form{{ $item->id }}" action="{{ route('admin.pengajuan-mutasi.destroy', $item->id) }}" method="post">
                    @csrf
                    @method('delete')
                  </form>
                      
                  @endif
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection