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
      <a href="{{ url('it/pengajuan/peralatan-it') }}" class="btn btn-success">+ Pengajuan Peralatan</a>
      <a href="{{ url('it/pengajuan/inventaris-it') }}" class="btn btn-warning">+ Pengajuan Inventaris</a>
  </div>
  <div class="card-body table-responsive">
      <table id="example2" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Nomor</th>
          <th>Area</th>
          <th>COA</th>
          <th>Nama Jenis</th>
          <th>Tanggal Pengajuan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($pengajuan as $data)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->nomor }}</td>
            <td>{{ $data->area->area }}</td>
            <td>
              <ul class="p-0" style="list-style: none"> 
                @foreach ($data->pengajuan_details as $item)
                    <li> {{ $item->jenis_peralatan->param_peralatan->coa }}</li>
                @endforeach
              </ul>
            </td>
            <td>
              <ul class="p-0" style="list-style: none">
                @foreach ($data->pengajuan_details as $item)
                    <li> {{ $item->jenis_peralatan->nama_jenis }}</li>
                @endforeach
              </ul>
            </td>
            <td>{{ $data->tanggal_pengajuan }}</td>
            <td>
              @if ($data->status_approve == 'lpj-admin')
                <span class="badge bg-success">Disetujui</span>
              @elseif($data->status_approve == 'lpj-admin-head')
                <span class="badge bg-success">Disetujui</span>
              @elseif($data->status_approve == 'lpj-akunting-it')
                <span class="badge bg-success">Disetujui</span>
              @elseif($data->status_approve == 'lpj-pengurus')
                <span class="badge bg-success">Disetujui</span>
              @elseif($data->status_approve == 'lpj-finance')
                <span class="badge bg-success">Disetujui</span>
              @elseif($data->status_approve == 'admin-head')
                <span class="badge bg-warning">AH</span>
              @elseif($data->status_approve == 'akunting-it')
                <span class="badge bg-warning">IT/Akunting</span>
              @elseif($data->status_approve == 'pengurus')
                <span class="badge bg-warning">Pengurus</span>
              @elseif($data->status_approve == 'finance')
                <span class="badge bg-warning">Finance</span>
                @elseif($data->status_approve == 'atasan-akunting')
                <span class="badge bg-warning">Atasan Akunting</span>
              @elseif($data->status_approve == 'admin')
                <span class="badge bg-danger">Ditolak</span>
              @elseif($data->status_approve == 'selesai')
              <span class="badge bg-success">Selesai</span>
              @endif
            </td>
            <td>
              @if ($data->status_proses == 'admin')
                <a href="{{ route('it.pengajuan.edit', $data->id) }}" class="btn btn-xs btn-primary">Edit</a>
              @endif
              <a href="{{ route('it.pengajuan.show', $data->id) }}" class="btn btn-xs btn-success">Detail</a>
              @if ($data->status_proses == 'admin')
                  <button type="submit" onclick="hapus_data('#delete_form{{ $data->id }}')" class="btn btn-sm btn-danger">Hapus</button>
                  <form class="d-inline" id="delete_form{{ $data->id }}" action="{{ route('admin.pengajuan.destroy', $data->id) }}" method="post">
                    @csrf
                    @method('delete')
                  </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tfoot>
      </table>
  </div>
</div>
@endsection