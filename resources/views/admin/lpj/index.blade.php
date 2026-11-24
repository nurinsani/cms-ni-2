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
            <h3 class="card-title font-weight-bold mb-0">Daftar Antrean LPJ Uang Muka</h3>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor Pengajuan</th>
                        <th>Area</th>
                        <th>Nama Unit / AP</th>
                        <th>COA</th>
                        <th>Jenis Peralatan</th>
                        <th class="text-center">Tanggal Pengajuan</th>
                        <th width="140" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuan as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold align-middle">{{ $item->nomor }}</td>
                            <td class="align-middle">{{ $item->area?->area ?? '-' }}</td>
                            <td class="align-middle">
                                @foreach ($item->pengajuan_details as $data)
                                    <div>{{ $data->ap?->unit ?? ($item->area?->area ?? '-') }}</div>
                                @endforeach
                            </td>
                            <td class="align-middle">
                                @foreach ($item->pengajuan_details as $data)
                                    <div>{{ $data->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="align-middle">
                                @foreach ($item->pengajuan_details as $data)
                                    <div>{{ $data->jenis_peralatan?->nama_jenis ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="text-center align-middle">
                                {{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d-M-Y') : '-' }}
                            </td>
                            <td class="text-center align-middle text-nowrap">
                                <a href="{{ route('admin.lpj.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-invoice mr-1"></i> Buat LPJ
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada antrean pengajuan yang perlu dibuatkan LPJ.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
