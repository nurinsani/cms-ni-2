@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ url("#") }}">Home</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Pembelian Peralatan
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>No</th>
            <th>Nomor</th>
            <th>Jenis BDD</th>
            <th>Keperluan</th>
            <th>Nama Ap</th>
            <th>Tanggal Pengajuan</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody>
            @foreach ($pengajuan_bdd as $data)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $data->nomor }}</td>
              <td>{{ $data->jenis_bdd->param_peralatan->coa }} {{ $data->jenis_bdd->param_peralatan->nama }} - {{ $data->jenis_bdd->nama_jenis }}</td>
              <td>{{ $data->type_bangunan }} {{ $data->lokasi }} {{ $data->jenis_asuransi }}</td>
              <td>{{ $data->branch->unit }}</td>
              <td>{{ $data->tanggal }}</td>
              <td>
                <a href="{{ url('pengurus/approval-bdd/detail', $data->id) }}" class="btn btn-sm btn-primary">Detail</a>
                {{-- <a href="{{ route('admin.pengajuan.show', $data->id) }}" class="btn btn-sm btn-primary">Detail</a> --}}
                <button type="submit" onclick="approve('#approve{{ $data->id }}')" class="btn btn-sm btn-success">Approve</button>
                <form class="d-inline" id="approve{{ $data->id }}" action="{{ url('pengurus/approval-bdd/approve', $data->id) }}" method="post">
                  @csrf
                </form>

                <button type="submit" onclick="tolak('#tolak{{ $data->id }}')" class="btn btn-sm btn-danger">Tolak</button>
                <form class="d-inline" id="tolak{{ $data->id }}" action="{{ url('pengurus/approval-bdd/tolak', $data->id) }}" method="post">
                  @csrf
                </form>
              </td>
            </tr>
            @endforeach
          </tfoot>
        </table>
      </div>
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