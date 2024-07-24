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
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Kode Rekening</th>
          <th>Tanggal Transaksi</th>
          <th>Jenis Transaksi</th>
          <th>Debet</th>
          <th>Kredit</th>
          {{-- <th>Aksi</th> --}}
        </tr>
        </thead>
        <tbody>
            @foreach ($transaksi_bdd as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_rekening }}</td>
                    <td>{{ $item->tanggal_transaksi }}</td>
                    <td>{{ $item->jenis_transaksi }}</td>
                    <td>Rp. {{ number_format($item->debet) }}</td>
                    <td>Rp. {{ number_format($item->kredit) }}</td>
                    {{-- <td>
                        <a href="{{ route('admin.lpj.show', $item->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection