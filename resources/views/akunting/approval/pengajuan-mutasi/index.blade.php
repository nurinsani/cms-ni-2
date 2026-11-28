@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">Daftar Approval Mutasi Inventaris Gol 1</h3>
            <span class="badge badge-light border ml-auto">{{ $mutasi->count() }} Antrean</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">NO</th>
                        <th>NO PENGAJUAN</th>
                        <th>DARI AP</th>
                        <th>KE AP</th>
                        <th>BULAN MUTASI</th>
                        <th class="text-center">JUMLAH BARANG</th>
                        <th width="220" class="text-center no-export">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mutasi as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold align-middle">{{ $item->kode_mutasi }}</td>
                            <td class="align-middle">{{ $item->dari_ap->unit ?? '-' }}</td>
                            <td class="align-middle">{{ $item->branch->unit ?? '-' }}</td>
                            <td class="align-middle">{{ strtoupper($item->bulan_mutasi) }}</td>
                            <td class="text-center align-middle font-weight-bold">{{ $item->inventaris->count() }}</td>
                            <td class="text-center align-middle text-nowrap">
                                <a href="{{ url('akunting/approval-mutasi/detail', $item->id) }}"
                                    class="btn btn-sm btn-outline-info mr-1">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>

                                <button type="button" onclick="approve('#approve{{ $item->id }}')"
                                    class="btn btn-sm btn-outline-success mr-1">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <form class="d-inline" id="approve{{ $item->id }}"
                                    action="{{ url('akunting/approval-mutasi/approve', $item->id) }}" method="post">
                                    @csrf
                                </form>

                                <button type="button" onclick="tolak('#tolak{{ $item->id }}')"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                                <form class="d-inline" id="tolak{{ $item->id }}"
                                    action="{{ url('akunting/approval-mutasi/tolak', $item->id) }}" method="post">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada pengajuan mutasi Gol 1 yang memerlukan approval saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            function approve(form_id) {
                if (confirm('Approve pengajuan mutasi ini?')) {
                    $(form_id).submit()
                }
            }

            function tolak(form_id) {
                if (confirm('Tolak pengajuan mutasi ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection
