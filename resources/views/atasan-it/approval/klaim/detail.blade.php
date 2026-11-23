@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('atasan-it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('atasan-it/klaim-pembelian') }}">Klaim Pembelian</a></li>
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
                <h4 class="font-weight-bold mb-1">PERMOHONAN KLAIM PEMBELIAN</h4>
                <h5 class="mb-0">TANGGAL
                    {{ $pengajuan->tanggal_pengajuan ? (is_string($pengajuan->tanggal_pengajuan) ? $pengajuan->tanggal_pengajuan : $pengajuan->tanggal_pengajuan->format('d-M-Y')) : '-' }}
                </h5>
            </div>
            <div></div>
        </div>

        {{-- Header Layar --}}
        <div class="card-header d-print-none">
            <div class="d-flex justify-content-between align-items-center">
                <div class="card-tools">
                    <a href="{{ url('atasan-it/klaim-pembelian') }}" class="btn btn-sm btn-secondary mr-1">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    @if ($pengajuan->status_approve == 'klaim-atasan-it')
                        <button type="button" onclick="confirmApprove('#approve-klaim-detail')"
                            class="btn btn-sm btn-success mr-1">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                        <form class="d-inline" id="approve-klaim-detail"
                            action="{{ url('atasan-it/klaim/approve', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="confirmTolak('#tolak-klaim-detail')"
                            class="btn btn-sm btn-danger mr-1">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                        <form class="d-inline" id="tolak-klaim-detail"
                            action="{{ url('atasan-it/klaim/tolak', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Metadata Box --}}
            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $pengajuan->nomor }}</td>
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
                            <td>{{ $pengajuan->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $pengajuan->tanggal_pengajuan ? (is_string($pengajuan->tanggal_pengajuan) ? $pengajuan->tanggal_pengajuan : $pengajuan->tanggal_pengajuan->format('d-M-Y')) : '-' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabel Rincian Item Klaim --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Lokasi (AP / Cabang)</th>
                            <th>COA</th>
                            <th>Keperluan / Item</th>
                            <th class="text-center" style="width: 65px;">Qty</th>
                            <th class="text-right" style="width: 140px;">Harga Satuan</th>
                            <th class="text-right" style="width: 150px;">Total</th>
                            <th class="text-center" style="width: 120px;">Berkas MOA</th>
                            <th class="text-center" style="width: 130px;">Bukti Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle">
                                    <span class="font-weight-bold">Klaim
                                        {{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</span>
                                    @if ($item->merk || $item->type)
                                        <br><small class="text-muted font-weight-normal">{{ $item->merk }}
                                            {{ $item->type }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp
                                    {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if (empty($item->moa))
                                        <span class="text-muted small"><i>-</i></span>
                                    @else
                                        <a href="{{ url('files/moa/' . $item->moa) }}" target="_blank"
                                            class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-file-download mr-1"></i> MOA
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if ($item->struk_pembelian)
                                        <a href="{{ url('files/struk_pembelian/' . $item->struk_pembelian) }}"
                                            target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-receipt mr-1"></i> Lihat Struk
                                        </a>
                                    @else
                                        <span class="text-muted small"><i>(Tidak ada struk)</i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">Tidak ada rincian item klaim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total Klaim Pembelian (Subtotal)</strong></td>
                            <td class="text-right font-weight-bold">Rp
                                {{ number_format($pengajuan->subtotal(), 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
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
                    title: 'Konfirmasi Approve Klaim',
                    text: 'Apakah Anda yakin ingin menyetujui (Approve) klaim ini?',
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
                    title: 'Konfirmasi Tolak Klaim',
                    text: 'Apakah Anda yakin ingin menolak klaim ini?',
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
