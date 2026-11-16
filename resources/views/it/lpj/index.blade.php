@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold mb-0">
                Daftar LPJ Pengajuan
            </h3>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor</th>
                        <th>Cabang</th>
                        <th>AP</th>
                        <th>COA</th>
                        <th>Nama Jenis Peralatan</th>
                        <th>Tanggal Pengajuan</th>
                        <th width="100" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $item->nomor }}</td>
                            <td>{{ $item->area?->area ?? '-' }}</td>
                            <td>
                                <ul class="p-0 mb-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li>{{ $data->ap?->unit ?? ($item->area?->area ?? '-') }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="p-0 mb-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li>{{ $data->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="p-0 mb-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li>{{ $data->jenis_peralatan?->nama_jenis ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $item->tanggal_pengajuan?->format('d-M-Y') ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('it.lpj.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
