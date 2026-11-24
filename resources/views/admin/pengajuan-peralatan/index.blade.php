@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">Daftar Pengajuan Uang Muka</h3>
            <div class="card-tools ml-auto">
                <a href="{{ url('admin/pengajuan/peralatan') }}" class="btn btn-sm btn-success mr-1">
                    <i class="fas fa-plus mr-1"></i> Pengajuan Peralatan
                </a>
                <a href="{{ url('admin/pengajuan/inventaris') }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-plus mr-1"></i> Pengajuan Inventaris
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor Pengajuan</th>
                        <th>Area</th>
                        <th>COA</th>
                        <th>Jenis Pengajuan</th>
                        <th class="text-center">Tanggal Pengajuan</th>
                        <th class="text-center">Status</th>
                        <th width="150" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuan as $data)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold align-middle">{{ $data->nomor }}</td>
                            <td class="align-middle">{{ $data->area?->area ?? '-' }}</td>
                            <td class="align-middle">
                                @foreach ($data->pengajuan_details as $item)
                                    <div>{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="align-middle">
                                @foreach ($data->pengajuan_details as $item)
                                    <div>{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="text-center align-middle">
                                {{ $data->tanggal_pengajuan ? $data->tanggal_pengajuan->format('d-M-Y') : '-' }}
                            </td>
                            <td class="text-center align-middle">
                                @if (in_array($data->status_approve, ['lpj-admin', 'lpj-admin-head', 'lpj-akunting-it', 'lpj-pengurus', 'lpj-finance']))
                                    <span class="badge badge-success px-2 py-1">Disetujui</span>
                                @elseif($data->status_approve == 'admin-head')
                                    <span class="badge badge-warning px-2 py-1">AH</span>
                                @elseif($data->status_approve == 'akunting-it')
                                    <span class="badge badge-warning px-2 py-1">IT/Akunting</span>
                                @elseif($data->status_approve == 'pengurus')
                                    <span class="badge badge-warning px-2 py-1">Pengurus</span>
                                @elseif($data->status_approve == 'finance')
                                    <span class="badge badge-warning px-2 py-1">Finance</span>
                                @elseif($data->status_approve == 'atasan-akunting')
                                    <span class="badge badge-warning px-2 py-1">Atasan Akunting</span>
                                @elseif($data->status_approve == 'atasan-it')
                                    <span class="badge badge-warning px-2 py-1">Atasan IT</span>
                                @elseif($data->status_approve == 'admin')
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                @elseif(in_array($data->status_approve, ['selesai', 'klaim-selesai']))
                                    <span class="badge badge-success px-2 py-1">Selesai</span>
                                @elseif($data->status_approve == 'klaim-akunting-it')
                                    <span class="badge badge-warning px-2 py-1">Akunting</span>
                                @elseif(in_array($data->status_approve, ['klaim-pengurus', 'klaim-finance']))
                                    <span class="badge badge-warning px-2 py-1">{{ ucfirst(str_replace('klaim-', '', $data->status_approve)) }}</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ $data->status_approve }}</span>
                                @endif
                            </td>
                            <td class="text-center align-middle text-nowrap">
                                @if ($data->status_proses == 'admin')
                                    <a href="{{ route('admin.pengajuan.edit', $data->id) }}"
                                        class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('admin.pengajuan.show', $data->id) }}"
                                    class="btn btn-sm btn-outline-info mr-1">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                @if ($data->status_approve == 'admin')
                                    <button type="button" onclick="hapus_data('#delete_form{{ $data->id }}')"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                    <form class="d-inline" id="delete_form{{ $data->id }}"
                                        action="{{ route('admin.pengajuan.destroy', $data->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada data pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
