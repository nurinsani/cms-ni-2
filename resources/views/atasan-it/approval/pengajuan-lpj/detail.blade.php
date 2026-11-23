@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('atasan-it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('atasan-it/approval-lpj') }}">Approval LPJ</a></li>
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
        {{-- Header Cetak --}}
        <div class="card-header mt-4 d-print-flex justify-content-between d-none">
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg"
                width="150px" alt="Logo">
            <div class="text-center">
                <h4 class="font-weight-bold mb-1">LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                <h5 class="mb-0">TANGGAL {{ $lpj->tanggal_pengajuan ? $lpj->tanggal_pengajuan->format('d-M-Y') : '-' }}
                </h5>
            </div>
            <div></div>
        </div>

        {{-- Header Layar --}}
        <div class="card-header d-print-none">
            <div class="d-flex justify-content-between align-items-center">
                <div class="card-tools">
                    <a href="{{ url('atasan-it/approval-lpj') }}" class="btn btn-sm btn-secondary mr-1">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    @if ($lpj->status_approve == 'lpj-atasan-it')
                        <button type="button" onclick="confirmApprove('#approve-lpj-detail')"
                            class="btn btn-sm btn-success mr-1">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                        <form class="d-inline" id="approve-lpj-detail" action="{{ url('atasan-it/approve-lpj', $lpj->id) }}"
                            method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="confirmTolak('#tolak-lpj-detail')"
                            class="btn btn-sm btn-danger mr-1">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                        <form class="d-inline" id="tolak-lpj-detail"
                            action="{{ url('atasan-it/approval-lpj/tolak', $lpj->id) }}" method="post">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- @if ($lpj->nominal_pengembalian)
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Bukti Pengembalian Sisa:</strong> Nominal Rp {{ number_format($lpj->nominal_pengembalian, 0, ',', '.') }}
                    @if ($lpj->bukti_pengembalian)
                        &mdash; <a href="{{ url('images/lpj/bukti_pengembalian/' . $lpj->bukti_pengembalian) }}" target="_blank" class="font-weight-bold text-dark text-underline">
                            <i class="fas fa-file-invoice mr-1"></i> Lihat Bukti Pengembalian
                        </a>
                    @endif
                </div>
            @endif --}}

            {{-- Metadata Box --}}
            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $lpj->nomor }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Kepada</td>
                            <td>:</td>
                            <td>Ketua dan Pengurus KSPPS Nur Insani</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $lpj->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $lpj->tanggal_pengajuan ? $lpj->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabel Rincian Realisasi LPJ --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>AP / Lokasi</th>
                            <th>COA</th>
                            <th>Nama Jenis Peralatan</th>
                            <th>Merk / Tipe</th>
                            <th class="text-right" style="width: 140px;">Uang Muka</th>
                            <th class="text-right" style="width: 150px;">Realisasi Pembelian</th>
                            <th class="text-center" style="width: 120px;">Struk Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lpj->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}
                                </td>
                                <td class="align-middle">{{ $item->merk }} {{ $item->type }}</td>
                                <td class="text-right align-middle">Rp
                                    {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp
                                    {{ number_format($item->harga_pembelian ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->struk_pembelian)
                                        <a href="{{ url('images/lpj/struk_pembelian/' . $item->struk_pembelian) }}"
                                            target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-receipt mr-1"></i> Lihat Struk
                                        </a>
                                    @else
                                        <span class="text-muted small"><i>(Tidak ada struk)</i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada data rincian item LPJ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Uang Muka (Subtotal)</strong></td>
                            <td class="text-right"><strong>Rp {{ number_format($lpj->subtotal(), 0, ',', '.') }}</strong>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Realisasi Pembelian</strong></td>
                            <td class="text-right font-weight-bold"><strong>Rp
                                    {{ number_format($lpj->total_pembelian(), 0, ',', '.') }}</strong></td>
                            <td colspan="2"></td>
                        </tr>
                        @php
                            $selisih = $lpj->subtotal() - $lpj->total_pembelian();
                        @endphp
                        @if ($selisih < 0)
                            <tr>
                                <td colspan="5" class="text-right text-danger"><strong>Kekurangan (Klaim)</strong></td>
                                <td class="text-right font-weight-bold text-danger"><strong>Rp
                                        {{ number_format(abs($selisih), 0, ',', '.') }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-right"><strong>Sisa / Pengembalian</strong></td>
                                <td class="text-right font-weight-bold"><strong>Rp
                                        {{ number_format($selisih, 0, ',', '.') }}</strong></td>
                                <td colspan="2">
                                    @if ($lpj->bukti_pengembalian)
                                        <a href="{{ url('images/lpj/bukti_pengembalian/' . $lpj->bukti_pengembalian) }}"
                                            target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-file-invoice mr-1"></i> Bukti Pengembalian
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function confirmApprove(formId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Approve LPJ',
                    text: 'Apakah Anda yakin ingin menyetujui (Approve) LPJ ini?',
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
            } else if (confirm('Approve LPJ ini?')) {
                $(formId).submit();
            }
        }

        function confirmTolak(formId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Tolak LPJ',
                    text: 'Apakah Anda yakin ingin menolak LPJ ini?',
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
            } else if (confirm('Tolak LPJ ini?')) {
                $(formId).submit();
            }
        }
    </script>
@endpush
