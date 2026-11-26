@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">Daftar Approval Klaim Pembelian</h3>
            <span class="badge badge-light border ml-auto">{{ $pengajuan->count() }} Antrean</span>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">NO</th>
                        <th>NOMOR PENGAJUAN</th>
                        <th>AREA / CABANG</th>
                        <th>COA</th>
                        <th>KETERANGAN / BARANG</th>
                        <th class="text-right">TOTAL</th>
                        <th class="text-center">TANGGAL PENGAJUAN</th>
                        <th width="200" class="text-center no-export">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuan as $data)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold align-middle">{{ $data->nomor }}</td>
                            <td class="align-middle">
                                @php
                                    $detailAreas = $data->pengajuan_details
                                        ->map(fn($item) => $item->area?->area)
                                        ->filter()
                                        ->unique();
                                @endphp
                                {{ $detailAreas->isNotEmpty() ? $detailAreas->implode(', ') : $data->area?->area ?? '-' }}
                            </td>
                            <td class="align-middle">
                                @foreach ($data->pengajuan_details as $item)
                                    <div>{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="align-middle">
                                @foreach ($data->pengajuan_details as $item)
                                    <div class="font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</div>
                                    @if ($item->merk || $item->type)
                                        <small class="text-muted">{{ $item->merk }} {{ $item->type }}</small>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-right align-middle font-weight-bold">
                                Rp {{ number_format($data->subtotal(), 0, ',', '.') }}
                            </td>
                            <td class="text-center align-middle">
                                {{ $data->tanggal_pengajuan ? $data->tanggal_pengajuan->format('d-M-Y') : '-' }}
                            </td>
                            <td class="text-center align-middle text-nowrap">
                                <a href="{{ url('admin-head/klaim/detail', $data->id) }}"
                                    class="btn btn-sm btn-outline-info mr-1">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>

                                <button type="button" onclick="approve('#approve{{ $data->id }}')"
                                    class="btn btn-sm btn-outline-success mr-1">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <form class="d-inline" id="approve{{ $data->id }}"
                                    action="{{ url('admin-head/klaim/approve', $data->id) }}" method="post">
                                    @csrf
                                </form>

                                <button type="button" onclick="tolak('#tolak{{ $data->id }}')"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                                <form class="d-inline" id="tolak{{ $data->id }}"
                                    action="{{ url('admin-head/klaim/tolak', $data->id) }}" method="post">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                Tidak ada pengajuan klaim yang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function approve(form_id) {
            if (confirm('Apakah Anda yakin ingin MENYETUJUI (Approve) klaim ini?')) {
                $(form_id).submit();
            }
        }

        function tolak(form_id) {
            if (confirm('Apakah Anda yakin ingin MENOLAK klaim ini?')) {
                $(form_id).submit();
            }
        }
    </script>
@endpush
