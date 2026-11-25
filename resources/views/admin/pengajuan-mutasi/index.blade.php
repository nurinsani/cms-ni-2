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

@push('css')
<style>
    #example2 thead th {
        text-transform: uppercase;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
        text-align: center;
        background-color: #f4f6f9;
    }
    #example2 tbody td {
        vertical-align: middle;
        font-size: 0.88rem;
    }
</style>
@endpush

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0 font-weight-bold text-dark">
            Daftar Pengajuan Mutasi
        </h5>
        <div class="card-tools ml-auto">
            <a href="{{ route('admin.pengajuan-mutasi.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Pengajuan
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th>NO PENGAJUAN</th>
                        <th>DARI AP</th>
                        <th>KE AP</th>
                        <th>BULAN MUTASI</th>
                        <th>TOTAL BARANG</th>
                        <th>STATUS</th>
                        <th style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan_mutasi as $key => $item)
                        <tr>
                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $item->kode_mutasi }}</td>
                            <td>{{ $item->dari_ap->unit ?? '-' }}</td>
                            <td>{{ $item->branch->unit ?? '-' }}</td>
                            <td class="text-center">{{ strtoupper($item->bulan_mutasi) }}</td>
                            <td class="text-center">
                                <span class="badge badge-info px-2 py-1">{{ $item->inventaris->count() }} Unit</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border text-dark font-weight-bold">
                                    {{ strtoupper($item->status_approve) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.pengajuan-mutasi.show', $item->id) }}" class="btn btn-default btn-sm" title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    @if ($item->status_approve === 'mutasi-admin')
                                        <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete_form_{{ $item->id }}" action="{{ route('admin.pengajuan-mutasi.destroy', $item->id) }}" method="post" class="d-none">
                                            @csrf
                                            @method('delete')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pengajuan Mutasi?',
            text: 'Data mutasi yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_form_' + id).submit();
            }
        });
    }
</script>
@endpush
