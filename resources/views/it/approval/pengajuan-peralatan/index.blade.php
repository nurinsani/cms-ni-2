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
                Daftar Pengajuan Menunggu Approval
            </h3>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor</th>
                        <th>Nama AP</th>
                        <th>Nominal</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Keterangan</th>
                        <th width="200" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $data)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $data->nomor }}</td>
                            <td>{{ $data->area?->unit ?? '-' }}</td>
                            <td class="font-weight-bold">Rp. {{ number_format($data->subtotal()) }}</td>
                            <td>{{ $data->tanggal_pengajuan }}</td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('it.pengajuan-detail', $data->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <button type="button" onclick="approve('#approve{{ $data->id }}')" class="btn btn-sm btn-success mr-1">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <form class="d-inline" id="approve{{ $data->id }}" action="{{ route('it.approve', $data->id) }}" method="post">
                                    @csrf
                                </form>

                                <button type="button" onclick="tolak('#tolak{{ $data->id }}')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                                <form class="d-inline" id="tolak{{ $data->id }}" action="{{ route('it.tolak', $data->id) }}" method="post">
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
            function tolak(form_id) {
                if (confirm('Tolak data ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection