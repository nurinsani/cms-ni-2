@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title font-weight-bold">
                Data Pengajuan Hapus Buku Inventaris Gol 1
            </h5>
            <div class="card-tools">
                <a href="{{ route('admin.pengajuan-hapus-buku.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus mr-1"></i> Tambah Pengajuan
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Kode Pengajuan</th>
                            <th>KP/Cabang/AP</th>
                            <th>Jenis Aktiva</th>
                            <th>Bulan Hapus Buku</th>
                            <th class="text-center">Jml Barang</th>
                            <th class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan_hapus_buku as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $item->kode_pengajuan }}</td>
                                <td>{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                <td><span class="badge badge-secondary">{{ $item->jenis_aktiva }}</span></td>
                                <td>{{ $item->bulan_hapus_buku }}</td>
                                <td class="text-center font-weight-bold">{{ $item->details->count() }}</td>
                                <td class="text-center">
                                    @if ($item->status_approve === 'admin-head')
                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Admin Head</span>
                                    @elseif ($item->status_approve === 'akunting')
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-user-check mr-1"></i> Menunggu Akunting</span>
                                    @elseif ($item->status_approve === 'selesai')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Disetujui (Selesai)</span>
                                    @elseif ($item->status_approve === 'ditolak')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> Ditolak</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ $item->status_approve }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pengajuan-hapus-buku.show', $item->id) }}" class="btn btn-info btn-xs mr-1" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    @if ($item->status_approve === 'admin-head' || $item->status_approve === 'ditolak')
                                        <form action="{{ route('admin.pengajuan-hapus-buku.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" title="Hapus Pengajuan">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data pengajuan hapus buku</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
