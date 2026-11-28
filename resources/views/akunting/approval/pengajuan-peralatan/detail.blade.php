@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('akunting/approval-peralatan') }}">Approval Peralatan</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @push('css')
        <style>
            @media print {
                .d-print-none {
                    display: none !important;
                }

                .d-print-flex {
                    display: flex !important;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin: 0.4in;
                }
            }
        </style>
    @endpush

    <div class="card card-outline card-primary shadow-sm">
        {{-- Header Layar --}}
        <div class="card-header d-print-none">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <a href="{{ url('akunting/approval-peralatan') }}" class="btn btn-sm btn-outline-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    @if ($pengajuan->status_approve == 'akunting-it')
                        <button type="button" onclick="approve('#approve{{ $pengajuan->id }}')" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-check mr-1"></i> Approve Pengajuan
                        </button>
                        <form class="d-inline" id="approve{{ $pengajuan->id }}" action="{{ url('akunting/approval-peralatan/approve', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="tolak('#tolak{{ $pengajuan->id }}')" class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-times mr-1"></i> Tolak Pengajuan
                        </button>
                        <form class="d-inline" id="tolak{{ $pengajuan->id }}" action="{{ url('akunting/approval-peralatan/tolak', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>
                    @endif

                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Info Metadata --}}
            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 140px;" class="text-muted font-weight-bold">Nomor Pengajuan</td>
                            <td>: <span class="font-weight-bold text-dark">{{ $pengajuan->nomor }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Kepada</td>
                            <td>: Ketua dan Pengurus KSPPS Nur Insani</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Dari / Pengaju</td>
                            <td>: {{ $pengajuan->user->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 140px;" class="text-muted font-weight-bold">Tanggal Pengajuan</td>
                            <td>: {{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Area / Wilayah</td>
                            <td>:
                                @php
                                    $detailAreas = $pengajuan->pengajuan_details->map(fn($item) => $item->area?->area)->filter()->unique();
                                @endphp
                                {{ $detailAreas->isNotEmpty() ? $detailAreas->implode(', ') : ($pengajuan->area?->area ?? '-') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Status</td>
                            <td>:
                                @if ($pengajuan->status_approve === 'akunting-it')
                                    <span class="badge badge-warning px-2 py-1">Menunggu Akunting</span>
                                @elseif ($pengajuan->status_approve === 'pengurus')
                                    <span class="badge badge-info px-2 py-1">Menunggu Pengurus</span>
                                @elseif ($pengajuan->status_approve === 'selesai')
                                    <span class="badge badge-success px-2 py-1">Disetujui (Selesai)</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ $pengajuan->status_approve }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" class="text-center">NO</th>
                            <th>CABANG / AP</th>
                            <th>JENIS PERALATAN</th>
                            <th class="text-center" width="70">QTY</th>
                            <th class="text-right" width="140">HARGA SATUAN</th>
                            <th class="text-right" width="160">SUBTOTAL</th>
                            <th width="120" class="text-center">MOA</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalPengajuan = 0; @endphp
                        @forelse ($pengajuan->pengajuan_details as $item)
                            @php
                                $subtotal = $item->harga * $item->kuantitas;
                                $totalPengajuan += $subtotal;
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap->unit ?? ($item->area->area ?? '-') }}</td>
                                <td class="align-middle">
                                    <div class="font-weight-bold">{{ $item->jenis_peralatan->nama_jenis ?? '-' }}</div>
                                    @if ($item->merk || $item->type)
                                        <small class="text-muted">{{ $item->merk }} {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->file)
                                        <a href="{{ asset('files/moa/' . $item->file) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-file-pdf mr-1"></i> Lihat MOA
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item->catatan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada rincian item pengajuan</td>
                            </tr>
                        @endforelse
                        <tr class="font-weight-bold bg-light">
                            <td colspan="5" class="text-right">TOTAL PENGAJUAN:</td>
                            <td class="text-right">Rp {{ number_format($totalPengajuan, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function approve(form_id) {
                if (confirm('Approve pengajuan ini?')) {
                    $(form_id).submit()
                }
            }

            function tolak(form_id) {
                if (confirm('Tolak pengajuan ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection
