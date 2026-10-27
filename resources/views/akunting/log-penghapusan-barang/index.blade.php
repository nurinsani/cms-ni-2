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
    <!-- Filter Data Log Hapus Buku: Unit, Bulan, Tahun -->
    <div class="card card-outline card-primary mb-3">
        <div class="card-header py-2">
            <h6 class="card-title font-weight-bold m-0">
                Filter Log Hapus Buku (Inventaris Gol {{ $gol ?? '1' }})
            </h6>
        </div>
        <div class="card-body py-3">
            <form action="{{ route('akunting.log-penghapusan-barang.index') }}" method="GET">
                <input type="hidden" name="gol" value="{{ $gol ?? '1' }}">
                <div class="row align-items-end">
                    <!-- Filter Unit -->
                    <div class="col-lg-4 col-md-4 col-sm-12 mb-2">
                        <label for="unit" class="text-dark font-weight-bold small mb-1">
                            Unit
                        </label>
                        <select name="unit" id="unit" class="form-control form-control-sm select2"
                            style="width: 100%;">
                            <option value="">-- Semua Unit --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->kode_branch }}"
                                    {{ ($selectedUnit ?? request('unit', request('kode_branch'))) == $branch->kode_branch ? 'selected' : '' }}>
                                    {{ $branch->kode_branch }} - {{ $branch->unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Bulan -->
                    <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                        <label for="bulan" class="text-dark font-weight-bold small mb-1">
                            Bulan
                        </label>
                        <select name="bulan" id="bulan" class="form-control form-control-sm select2bs4"
                            style="width: 100%;">
                            <option value="">-- Semua Bulan --</option>
                            @foreach ($bulanList as $bln)
                                <option value="{{ $bln }}"
                                    {{ ($selectedBulan ?? request('bulan')) == $bln ? 'selected' : '' }}>
                                    {{ $bln }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="col-lg-2 col-md-2 col-sm-6 mb-2">
                        <label for="tahun" class="text-dark font-weight-bold small mb-1">
                            Tahun
                        </label>
                        <select name="tahun" id="tahun" class="form-control form-control-sm select2bs4"
                            style="width: 100%;">
                            <option value="">-- Semua Tahun --</option>
                            @foreach ($years as $yr)
                                <option value="{{ $yr }}"
                                    {{ ($selectedTahun ?? request('tahun')) == $yr ? 'selected' : '' }}>
                                    {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-lg-3 col-md-3 col-sm-12 mb-2 text-right">
                        <button type="submit" class="btn btn-primary btn-sm px-3 mr-1 shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if (request()->filled('unit') ||
                                request()->filled('kode_branch') ||
                                request()->filled('bulan') ||
                                request()->filled('tahun'))
                            <a href="{{ route('akunting.log-penghapusan-barang.index', ['gol' => $gol ?? '1']) }}"
                                class="btn btn-secondary btn-sm px-3 shadow-sm" title="Reset Filter">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Log Penghapusan Barang -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title font-weight-bold mb-0">
                Daftar Hapus Buku Inventaris Gol {{ $gol ?? '1' }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-hover text-nowrap table-sm">
                    <thead>
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            @if (($gol ?? '1') == '2')
                                <th>IMEI</th>
                                <th>Cabang / AP</th>
                                <th>Type / Merk</th>
                                <th>Pengguna</th>
                            @else
                                <th>Kode Inventaris</th>
                                <th>Cabang / AP</th>
                                <th>Jenis / Merk</th>
                                <th>Type</th>
                            @endif
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-right">Akum. Penyusutan</th>
                            <th class="text-right">Nilai Buku</th>
                            <th class="text-center">Tanggal Hapus</th>
                            <th width="8%" class="text-center no-export">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventaris as $item)
                            @php
                                $pengajuan = $item->pengajuan_hapus_buku->first();
                                $tglHapus = '-';
                                if ($pengajuan && $pengajuan->tanggal_approve_atasan_2) {
                                    $tglHapus = \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_2)->format(
                                        'd-m-Y',
                                    );
                                } elseif ($pengajuan && $pengajuan->created_at) {
                                    $tglHapus = $pengajuan->created_at->format('d-m-Y');
                                } elseif ($item->updated_at) {
                                    $tglHapus = $item->updated_at->format('d-m-Y');
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                @if (($gol ?? '1') == '2')
                                    <td class="font-weight-bold font-monospace">
                                        {{ $item->imei ?? ($item->kode_peralatan ?: '-') }}</td>
                                    <td>{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                    <td>{{ $item->type ?? $item->jenis }} {{ $item->merk ? '(' . $item->merk . ')' : '' }}
                                    </td>
                                    <td>{{ $item->pengguna ?? '-' }}</td>
                                @else
                                    <td class="font-weight-bold font-monospace">{{ $item->kode_peralatan }}</td>
                                    <td>{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                    <td>{{ $item->jenis }} {{ $item->merk ? '(' . $item->merk . ')' : '' }}</td>
                                    <td>{{ $item->type ?? '-' }}</td>
                                @endif
                                <td class="text-right">Rp.
                                    {{ number_format(intval($item->harga_perolehan ?? 0), 0, ',', '.') }}</td>
                                <td class="text-right">Rp. {{ number_format($item->calc_akumulasi, 0, ',', '.') }}</td>
                                <td class="text-right font-weight-bold">Rp.
                                    {{ number_format($item->calc_nilai_buku, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $tglHapus }}</td>
                                <td class="text-center no-export">
                                    <a href="{{ route('akunting.log-penghapusan-barang.show', ['id' => $item->id, 'gol' => $gol ?? '1']) }}"
                                        class="btn btn-info btn-xs" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada inventaris Gol
                                    {{ $gol ?? '1' }} dengan status hapus buku</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
