@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ url("#") }}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
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
          <th>Nomor</th>
          <th>Nama Ap</th>
          <th>Nominal</th>
          <th>Tanggal Pengajuan</th>
          <th>Keterangan</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($pengajuan as $data)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->nomor }}</td>
            <td>{{ $data->branch->unit }}</td>
            <td>Rp. {{ number_format($data->subtotal()) }}</td>
            <td>{{ $data->tanggal_pengajuan }}</td>
            <td>{{ $data->keterangan }}</td>
            <td>
              <a href="{{ route('supervisor.pengajuan-detail', $data->id) }}" class="btn btn-sm btn-primary">Detail</a>
              <button type="submit" onclick="approve('#approve{{ $data->id }}')" class="btn btn-sm btn-success">Approve</button>
              <form class="d-inline" id="approve{{ $data->id }}" action="{{ route('supervisor.approve', $data->id) }}" method="post">
                @csrf
              </form>

              <button type="submit" onclick="tolak('#tolak{{ $data->id }}')" class="btn btn-sm btn-danger">Tolak</button>
              <form class="d-inline" id="tolak{{ $data->id }}" action="{{ route('supervisor.tolak', $data->id) }}" method="post">
                @csrf
              </form>
            </td>
          </tr>
          @endforeach
        </tfoot>
      </table>
    </div>
    <!-- /.card-body -->
  </div>

  @push('js')
    <script>
      function approve (form_id){
        if(confirm('Approve data ini?')) {
          $(form_id).submit()
        }
      }
    </script>

    <script>
      function tolak (form_id){
        if(confirm('Tolak data ini?')) {
          $(form_id).submit()
        }
      }
    </script>
  @endpush
@endsection