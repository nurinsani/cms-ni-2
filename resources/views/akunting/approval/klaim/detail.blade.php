@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('akunting/klaim-pembelian') }}">Approval Klaim</a></li>
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
                    <a href="{{ url('akunting/klaim-pembelian') }}" class="btn btn-sm btn-outline-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    @if ($pengajuan->status_approve == 'klaim-akunting-it')
                        <button type="button" onclick="approve('#approve{{ $pengajuan->id }}')"
                            class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-check mr-1"></i> Approve Klaim
                        </button>
                        <form class="d-inline" id="approve{{ $pengajuan->id }}"
                            action="{{ url('akunting/klaim/approve', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="tolak('#tolak{{ $pengajuan->id }}')"
                            class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-times mr-1"></i> Tolak Klaim
                        </button>
                        <form class="d-inline" id="tolak{{ $pengajuan->id }}"
                            action="{{ url('akunting/klaim/tolak', $pengajuan->id) }}" method="post">
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
                            <td class="text-muted font-weight-bold">Dari / Pemohon</td>
                            <td>: {{ $pengajuan->user->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 140px;" class="text-muted font-weight-bold">Tanggal Pengajuan</td>
                            <td>:
                                {{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d-M-Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Area / Wilayah</td>
                            <td>:
                                @php
                                    $detailAreas = $pengajuan->pengajuan_details
                                        ->map(fn($item) => $item->area?->area)
                                        ->filter()
                                        ->unique();
                                @endphp
                                {{ $detailAreas->isNotEmpty() ? $detailAreas->implode(', ') : $pengajuan->area?->area ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Status</td>
                            <td>:
                                @if ($pengajuan->status_approve === 'klaim-akunting-it')
                                    <span class="badge badge-warning px-2 py-1">Menunggu Akunting</span>
                                @elseif ($pengajuan->status_approve === 'klaim-pengurus')
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
                            <th>NAMA AP / CABANG</th>
                            <th>COA</th>
                            <th>KEPERLUAN</th>
                            <th class="text-center" width="70">QTY</th>
                            <th class="text-right" width="140">HARGA SATUAN</th>
                            <th class="text-right" width="160">SUBTOTAL</th>
                            <th width="120" class="text-center">LAMPIRAN</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalKlaim = 0; @endphp
                        @forelse ($pengajuan->pengajuan_details as $item)
                            @php
                                $subtotal = $item->harga * $item->kuantitas;
                                $totalKlaim += $subtotal;
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap->unit ?? ($item->area->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle">
                                    <div class="font-weight-bold">{{ $item->jenis_peralatan->nama_jenis ?? '-' }}</div>
                                    @if ($item->merk || $item->type)
                                        <small class="text-muted">{{ $item->merk }} {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->file)
                                        <a href="{{ asset('files/moa/' . $item->file) }}" target="_blank"
                                            class="btn btn-xs btn-outline-primary mb-1">
                                            <i class="fas fa-file-pdf mr-1"></i> MOA
                                        </a>
                                    @endif
                                    @if ($item->struk_pembelian)
                                        <a href="{{ asset('images/struk/' . $item->struk_pembelian) }}" target="_blank"
                                            class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-receipt mr-1"></i> Struk
                                        </a>
                                    @endif
                                    @if (!$item->file && !$item->struk_pembelian)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item->catatan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">Tidak ada rincian item klaim</td>
                            </tr>
                        @endforelse
                        <tr class="font-weight-bold bg-light">
                            <td colspan="6" class="text-right">TOTAL KLAIM:</td>
                            <td class="text-right">Rp {{ number_format($totalKlaim, 0, ',', '.') }}</td>
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
                if (confirm('Approve klaim ini?')) {
                    $(form_id).submit()
                }
            }

            function tolak(form_id) {
                if (confirm('Tolak klaim ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection
