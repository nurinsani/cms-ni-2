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
            <a href="{{ url('it/peralatan/klaim') }}" class="btn btn-sm btn-success mr-1">
                <i class="fas fa-plus"></i> Klaim Peralatan
            </a>
            <a href="{{ url('it/inventaris/klaim') }}" class="btn btn-sm btn-warning">
                <i class="fas fa-plus"></i> Klaim Inventaris
            </a>
        </div>
        <div class="card-body table-sm table-responsive">
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
                            <td>{{ $data->tanggal_pengajuan->format('d-M-Y') }}</td>
                            <td>
                            @if ($data->status_approve == 'klaim-admin-head')
                            <span class="badge bg-warning">Admin Head</span>
                            @elseif($data->status_approve == 'klaim-pengurus')
                                <span class="badge bg-warning">Pengurus</span>
                            @elseif($data->status_approve == 'klaim-finance')
                                <span class="badge bg-warning">Finance</span>
                            @elseif($data->status_approve == 'klaim-akunting-it')
                                <span class="badge bg-warning">Akunting</span>
                            @elseif($data->status_approve == 'tolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif($data->status_approve == 'klaim-selesai')
                                <span class="badge bg-success">Selesai</span>
                            @endif
                            </td>
                            <td>
                                @if ($data->status_proses == 'admin')
                                    <a href="{{ route('admin.pengajuan.edit', $data->id) }}"
                                        class="btn btn-xs btn-primary">Edit</a>
                                @endif
                                <a href="{{ url('it/klaim', $data->id) }}"
                                    class="btn btn-xs btn-success">Detail</a>
                                @if ($data->status_approve == 'admin')
                                    <button type="submit" onclick="hapus_data('#delete_form{{ $data->id }}')"
                                        class="btn btn-sm btn-danger">Hapus</button>
                                    <form class="d-inline" id="delete_form{{ $data->id }}"
                                        action="{{ route('admin.pengajuan.destroy', $data->id) }}" method="post">
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
