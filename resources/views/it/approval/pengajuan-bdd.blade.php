@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold mb-0">
                Daftar Pengajuan BDD Menunggu Approval
            </h3>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor</th>
                        <th>Jenis BDD</th>
                        <th>Keperluan</th>
                        <th>Nama AP</th>
                        <th>Tanggal Pengajuan</th>
                        <th width="160" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan_bdd as $data)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $data->nomor }}</td>
                            <td>{{ $data->jenis_bdd?->param_peralatan?->coa }} {{ $data->jenis_bdd?->param_peralatan?->nama }} - {{ $data->jenis_bdd?->nama_jenis }}</td>
                            <td>{{ $data->type_bangunan }} {{ $data->lokasi }} {{ $data->jenis_asuransi }}</td>
                            <td>{{ $data->branch?->unit ?? '-' }}</td>
                            <td>{{ $data->tanggal }}</td>
                            <td class="text-center text-nowrap">
                                <button type="button" onclick="approve('#approve{{ $data->id }}')" class="btn btn-sm btn-success mr-1">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <form class="d-inline" id="approve{{ $data->id }}" action="{{ route('it.approve_bdd', $data->id) }}" method="post">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            function approve(form_id) {
                if (confirm('Approve data ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection