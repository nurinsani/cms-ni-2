@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">NO</th>
                            <th>NO PENGAJUAN</th>
                            <th>DARI AP</th>
                            <th>KE AP</th>
                            <th>BULAN MUTASI</th>
                            <th class="text-center">JUMLAH BARANG</th>
                            <th class="text-center" width="22%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan_mutasi as $key => $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $item->kode_mutasi }}</td>
                                <td>{{ $item->dari_ap->unit ?? '-' }}</td>
                                <td>{{ $item->branch->unit ?? '-' }}</td>
                                <td>{{ strtoupper($item->bulan_mutasi) }}</td>
                                <td class="text-center font-weight-bold">{{ $item->inventaris_gol_2->count() }}</td>
                                <td class="text-center">
                                    <a href="{{ url('admin-head/approval-mutasi-gol-2/detail', $item->id) }}"
                                        class="btn btn-outline-info btn-sm mr-1">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>

                                    <button type="button" onclick="approve('#approve{{ $item->id }}')"
                                        class="btn btn-outline-success btn-sm mr-1">
                                        <i class="fas fa-check mr-1"></i> Approve
                                    </button>
                                    <form class="d-inline" id="approve{{ $item->id }}"
                                        action="{{ url('admin-head/approval-mutasi-gol-2/approve', $item->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="button" onclick="tolak('#tolak{{ $item->id }}')"
                                        class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>
                                    <form class="d-inline" id="tolak{{ $item->id }}"
                                        action="{{ url('admin-head/approval-mutasi-gol-2/tolak', $item->id) }}" method="post">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada pengajuan mutasi gol 2 menunggu persetujuan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function approve(form_id) {
                if (confirm('Approve data ini?')) {
                    $(form_id).submit()
                }
            }

            function tolak(form_id) {
                if (confirm('Tolak data ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection
