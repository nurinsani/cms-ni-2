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
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-default">
        Tambah
      </button>
        {{-- <a href="{{ route('admin.pengajuan-bdd.create') }}" class="btn btn-primary">Tambah</a> --}}
    </div>
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
            @foreach ($bdd as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->jenis_bdd->param_peralatan->nama }} - {{ $item->jenis_bdd->nama_jenis }}</td>
                    <td>
                      {{-- <a href="#" onclick="buat_lpj({{ $item->id }})" class="btn btn-sm btn-primary">Buat LPJ</a> --}}
                        <a href="{{ route('admin.pengajuan-bdd.show', $item->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                      @if ($item->jenis_bdd->group_bdd == 'sewa')
                      <a href="{{ route('admin.pengajuan-bdd.edit-sewa', $item->id) }}" class="btn btn-sm btn-success">Edit</a>
                      @else
                      <a href="{{ route('admin.pengajuan-bdd.edit-asuransi', $item->id) }}" class="btn btn-sm btn-success">Edit</a>
                      @endif
                      @if ($item->status_proses == 'supervisor')
                      <button type="submit" onclick="hapus_data('#delete_form{{ $item->id }}')" class="btn btn-sm btn-danger">Hapus</button>
                      <form class="d-inline" id="delete_form{{ $item->id }}" action="{{ route('admin.pengajuan-bdd.destroy', $item->id) }}" method="post">
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

  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Pilih Pengajuan BDD</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <a href="{{ route('admin.pengajuan-bdd-sewa') }}" class="btn btn-primary">Sewa</a>
          <a href="{{ route('admin.pengajuan-bdd-asuransi') }}" class="btn btn-primary">Asuransi</a>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
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