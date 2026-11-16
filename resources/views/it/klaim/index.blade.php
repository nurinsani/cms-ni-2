@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                Daftar Klaim Pembelian
            </h3>
            <div class="card-tools ml-auto">
                <a href="{{ url('it/peralatan/klaim') }}" class="btn btn-sm btn-success shadow-sm mr-1">
                    <i class="fas fa-plus mr-1"></i> Klaim Peralatan
                </a>
                <a href="{{ url('it/inventaris/klaim') }}" class="btn btn-sm btn-warning shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Klaim Inventaris
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor</th>
                        <th>Area</th>
                        <th>COA</th>
                        <th>Nama Jenis</th>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-center">Status</th>
                        <th width="140" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $data)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $data->nomor }}</td>
                            <td>{{ $data->area?->area ?? '-' }}</td>
                            <td>
                                <ul class="p-0 mb-0" style="list-style: none">
                                    @foreach ($data->pengajuan_details as $item)
                                        <li>{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="p-0 mb-0" style="list-style: none">
                                    @foreach ($data->pengajuan_details as $item)
                                        <li>{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $data->tanggal_pengajuan?->format('d-M-Y') ?? '-' }}</td>
                            <td class="text-center">
                                @if ($data->status_approve == 'klaim-admin-head')
                                    <span class="badge bg-warning px-2 py-1">Admin Head</span>
                                @elseif($data->status_approve == 'klaim-pengurus')
                                    <span class="badge bg-warning px-2 py-1">Pengurus</span>
                                @elseif($data->status_approve == 'klaim-finance')
                                    <span class="badge bg-warning px-2 py-1">Finance</span>
                                @elseif($data->status_approve == 'klaim-akunting-it')
                                    <span class="badge bg-warning px-2 py-1">Akunting</span>
                                @elseif($data->status_approve == 'tolak')
                                    <span class="badge bg-danger px-2 py-1">Ditolak</span>
                                @elseif($data->status_approve == 'klaim-selesai')
                                    <span class="badge bg-success px-2 py-1">Selesai</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">{{ $data->status_approve }}</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($data->status_proses == 'admin')
                                    <a href="{{ route('it.pengajuan.edit', $data->id) }}"
                                        class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ url('it/klaim', $data->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                @if ($data->status_approve == 'admin')
                                    <button type="button" onclick="hapus_data('#delete_form{{ $data->id }}')"
                                        class="btn btn-sm btn-outline-danger ml-1">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                    <form class="d-inline" id="delete_form{{ $data->id }}"
                                        action="{{ route('it.pengajuan.destroy', $data->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
