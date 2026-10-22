@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filter Cabang -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form action="{{ route('akunting.log-penghapusan-barang.index') }}" method="GET" class="form-inline">
                <label for="kode_branch" class="mr-2 text-muted font-weight-bold">Filter Cabang / AP:</label>
                <select name="kode_branch" id="kode_branch" class="form-control form-control-sm mr-2 select2"
                    style="min-width: 250px;">
                    <option value="">-- Semua Cabang / AP --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->kode_branch }}"
                            {{ request('kode_branch') == $branch->kode_branch ? 'selected' : '' }}>
                            {{ $branch->unit }} ({{ $branch->kode_branch }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if (request()->filled('kode_branch'))
                    <a href="{{ route('akunting.log-penghapusan-barang.index') }}" class="btn btn-default btn-sm">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabel Data Log Penghapusan Barang -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title font-weight-bold mb-0">
                Daftar Inventaris Status Hapus Buku
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-hover text-nowrap table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th>Kode Inventaris</th>
                            <th>Cabang / AP</th>
                            <th>Jenis / Merk</th>
                            <th>Type</th>
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-right">Akum. Penyusutan</th>
                            <th class="text-right">Nilai Buku</th>
                            <th>No. Pengajuan</th>
                            <th width="8%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventaris as $item)
                            @php
                                $pengajuan = $item->pengajuan_hapus_buku->first();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold font-monospace">{{ $item->kode_peralatan }}</td>
                                <td>{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                <td>{{ $item->jenis }} {{ $item->merk ? '(' . $item->merk . ')' : '' }}</td>
                                <td>{{ $item->type ?? '-' }}</td>
                                <td class="text-right">Rp.
                                    {{ number_format(intval($item->harga_perolehan ?? 0), 0, ',', '.') }}</td>
                                <td class="text-right">Rp. {{ number_format($item->calc_akumulasi, 0, ',', '.') }}</td>
                                <td class="text-right">Rp. {{ number_format($item->calc_nilai_buku, 0, ',', '.') }}</td>
                                <td>{{ $pengajuan->kode_pengajuan ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('akunting.log-penghapusan-barang.show', $item->id) }}"
                                        class="btn btn-info btn-xs" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada inventaris dengan status
                                    hapus buku</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
