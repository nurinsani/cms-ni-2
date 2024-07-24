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
          <th>Nomor</th>
          <th>Tanggal Pengajuan</th>
          <th>Jenis BDD</th>
          <th>Aksi</th>
          
        </tr>
        </thead>
        <tbody>
            {{-- @dd($daftar_bdd) --}}
            @foreach ($daftar_bdd as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor }}</td>
                    <td>{{ $item->pengajuan_bdd }}</td>
                    <td>
                      {{-- <a href="#" onclick="buat_lpj({{ $item->id }})" class="btn btn-sm btn-primary">Buat LPJ</a> --}}
                        <a href="{{ route('admin.pengajuan-bdd.show', $item->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                        {{-- <a href="{{ route('admin.lpj.edit', $item->id) }}" class="btn btn-sm btn-success">Edit</a> --}}
                    </td>
                </tr>
            @endforeach
          
        </tfoot>
      </table>
    </div>

  </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_id){
            let modal = $('#modal_lpj').modal('show') 
            // modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
            modal.find('#pengajuan_id').val(pengajuan_id)
        }
    </script>
@endpush