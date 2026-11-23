@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('atasan-it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold mb-0">Daftar Pengajuan Peralatan Menunggu Approval</h3>
        </div>
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nomor Surat</th>
                        <th>Cabang / Area</th>
                        <th>Keperluan / Item</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-center">Tanggal Pengajuan</th>
                        <th width="210" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $data)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold align-middle">{{ $data->nomor }}</td>
                            <td class="align-middle">{{ $data->area?->area ?? '-' }}</td>
                            <td class="align-middle">
                                @foreach ($data->pengajuan_details as $item)
                                    <div class="mb-1">
                                        <span
                                            class="font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</span>
                                        @if ($item->merk || $item->type)
                                            <small class="text-muted">({{ $item->merk }} {{ $item->type }})</small>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-right align-middle font-weight-bold">Rp
                                {{ number_format($data->subtotal(), 0, ',', '.') }}</td>
                            <td class="text-center align-middle">
                                {{ $data->tanggal_pengajuan ? $data->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                            <td class="text-center align-middle text-nowrap">
                                <a href="{{ url('atasan-it/approval-peralatan/detail', $data->id) }}"
                                    class="btn btn-sm btn-outline-primary mr-1">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                <button type="button" onclick="confirmApprove('#approve{{ $data->id }}')"
                                    class="btn btn-sm btn-success mr-1">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <form class="d-inline" id="approve{{ $data->id }}"
                                    action="{{ url('atasan-it/approval-peralatan/approve', $data->id) }}" method="post">
                                    @csrf
                                </form>

                                <button type="button" onclick="confirmTolak('#tolak{{ $data->id }}')"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                                <form class="d-inline" id="tolak{{ $data->id }}"
                                    action="{{ url('atasan-it/approval-peralatan/tolak', $data->id) }}" method="post">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function confirmApprove(formId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Approve',
                    text: 'Apakah Anda yakin ingin menyetujui (Approve) pengajuan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(formId).submit();
                    }
                });
            } else if (confirm('Approve data ini?')) {
                $(formId).submit();
            }
        }

        function confirmTolak(formId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Tolak',
                    text: 'Apakah Anda yakin ingin menolak pengajuan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(formId).submit();
                    }
                });
            } else if (confirm('Tolak data ini?')) {
                $(formId).submit();
            }
        }
    </script>
@endpush
