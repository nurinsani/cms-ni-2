@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold mb-0">Data Inventaris Peralatan</h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="table-peralatan" class="table table-bordered table-striped table-hover mb-0 w-100">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th style="width: 45px;">No</th>
                            <th style="width: 140px;">Barcode</th>
                            <th>Jenis Peralatan</th>
                            <th>Merk / Tipe</th>
                            <th>Unit / Cabang (AP)</th>
                            <th style="width: 130px;">Tgl Perolehan</th>
                            <th style="width: 60px;">Jumlah</th>
                            <th style="width: 140px;">Harga Perolehan</th>
                            <th style="width: 150px;">Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftar_peralatan as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="text-center align-middle font-weight-bold">
                                    <code>{{ $item->kode_peralatan }}</code>
                                </td>
                                <td class="align-middle font-weight-bold">{{ $item->jenis }}</td>
                                <td class="align-middle">
                                    {{ $item->merk }}
                                    @if (!empty($item->type))
                                        <small class="text-muted d-block">Tipe: {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item->ap->unit ?? ($item->kode_branch ?? '-') }}</td>
                                <td class="text-center align-middle">
                                    @php
                                        $tgl = $item->tanggal_perolehan ?? $item->tanggal_pembelian;
                                    @endphp
                                    {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d-M-Y') : '-' }}
                                </td>
                                <td class="text-center align-middle">{{ $item->jumlah ?? 1 }}</td>
                                <td class="text-right align-middle font-weight-bold text-dark">
                                    Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}
                                </td>
                                <td class="align-middle">
                                    @if (empty($item->pengguna) || $item->pengguna == '0')
                                        <span class="text-muted font-italic">-</span>
                                    @else
                                        {{ $item->pengguna }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data peralatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            var exportOptions = {
                columns: ':visible'
            };

            var table = $("#table-peralatan").DataTable({
                responsive: false,
                lengthChange: true,
                autoWidth: false,
                ordering: true,
                destroy: true,
                pageLength: 25,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daftar Peralatan',
                        text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Daftar Peralatan',
                        text: '<i class="fas fa-file-csv mr-1"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        exportOptions: exportOptions
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print mr-1"></i> Print',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: exportOptions
                    }
                ]
            });

            table.buttons().container().appendTo('#table-peralatan_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
