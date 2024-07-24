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
  <div class="card-body">
      <div class="row">
          <div class="col-md-4">
            <form action="{{ url('akunting/jurnal-ap') }}" method="GET">
              <div class="input-group">
                  <input type="date" class="form-control" name="start_date">
                  <input type="date" class="form-control" name="end_date">
                  <button class="btn btn-primary ml-2" type="submit">Filter</button>
              </div>
          </form>
          </div>
      </div>
  </div>
</div>

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
            @foreach ($jurnal_ap as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_rekening }}</td>
                    <td>{{ $item->tanggal_transaksi }}</td>
                    <td>{{ $item->jenis_transaksi }}</td>
                    <td>Rp. {{ number_format($item->debet) }}</td>
                    <td>Rp. {{ number_format($item->kredit) }}</td>
                </tr>
            @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection