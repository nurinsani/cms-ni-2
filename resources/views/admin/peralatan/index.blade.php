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
<a href="{{ url('admin/export-excel-peralatan') }}" class="btn btn-sm btn-success my-2">Export</a>
<div class="card">
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example1" class="table table-bordered table-hover">
        <thead>
        <tr>
          <th>No</th>
          <th>Barcode</th>
          <th>Jenis</th>
          <th>Merk</th>
          <th>AP</th>
          <th>Tanggal Perolehan</th>
          <th>Jumlah</th>
          <th>Harga Perolehan</th>
          <th>Pengguna</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($daftar_peralatan as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->kode_peralatan }}</td>
            <td>{{ $item->jenis }}</td>
            <td>{{ $item->merk }}</td>
            <td>{{ $item->ap->unit ?? 'JABAR 1' }}</td>
            <td>{{ $item->tanggal_perolehan}}</td>
            <td>{{ $item->jumlah }}</td>
            <td>Rp. {{ number_format($item->harga_perolehan) }}</td>
            <td>
              @if ($item->pengguna == 0)
                -
              @else
                {{ $item->pengguna }}
              @endif
            </td>
          </tr>
          @endforeach
        </tfoot>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
@endsection

@push('js')
    <script>
      $(document).ready(function() {
          $('#example1').DataTable({
              dom: 'Bfrtip', // Atur posisi tombol
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Daftar Peralatan',
                      text: 'Export Excel',
                      className: 'btn btn-success btn-sm'
                  },
                  {
                      extend: 'csvHtml5',
                      title: 'Daftar Peralatan',
                      text: 'Export CSV',
                      className: 'btn btn-primary btn-sm'
                  },
                  {
                      extend: 'print',
                      text: 'Print',
                      className: 'btn btn-warning btn-sm'
                  }
              ]
          });
      });
</script>
@endpush