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
        <a href="{{ route('it.data-user.create') }}" class="btn btn-primary">Tambah</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>Username</th>
          <th>Email</th>
          <th>Nomor Rekening</th>
          <th>Bank</th>
          <th>Cabang</th>
          <th>Level</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->nik }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->no_rek }}</td>
            <td>{{ $user->nama_bank }}</td>
            <td>{{ $user->branch->area ?? $user->area->region}}</td>
            <td class="text-capitalize">{{ $user->getRoleNames()[0] ?? '' }}</td>
            <td>
              <a href="{{ route('it.data-user.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
              <button type="submit" onclick="hapus_data('#delete_form{{ $user->id }}')" class="btn btn-sm btn-danger">Hapus</button>
              <form class="d-inline" id="delete_form{{ $user->id }}" action="{{ route('it.data-user.destroy', $user->id) }}" method="post">
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