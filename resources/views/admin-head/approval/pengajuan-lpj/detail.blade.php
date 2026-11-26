@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('admin-head/approval-lpj') }}">Approval LPJ</a></li>
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
                    <a href="{{ url('admin-head/approval-lpj') }}" class="btn btn-sm btn-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    @if ($lpj->status_approve == 'lpj-admin-head')
                        <button type="button" onclick="approve('#approve{{ $lpj->id }}')" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-check mr-1"></i> Approve LPJ
                        </button>
                        <form class="d-inline" id="approve{{ $lpj->id }}" action="{{ url('admin-head/approval-lpj/approve', $lpj->id) }}" method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="tolak('#tolak{{ $lpj->id }}')" class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-times mr-1"></i> Tolak LPJ
                        </button>
                        <form class="d-inline" id="tolak{{ $lpj->id }}" action="{{ url('admin-head/approval-lpj/tolak', $lpj->id) }}" method="post">
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
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $lpj->nomor }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Cabang / Area</td>
                            <td>:</td>
                            <td>{{ $lpj->ap?->unit ?? ($lpj->area?->area ?? '-') }}</td>
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
                            <td style="width: 140px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $lpj->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal Pengajuan</td>
                            <td>:</td>
                            <td>{{ $lpj->tanggal_pengajuan ? $lpj->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabel Rincian LPJ --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center align-middle" style="width: 45px;">NO</th>
                            <th class="align-middle">UNIT / AP</th>
                            <th class="align-middle">COA</th>
                            <th class="align-middle">JENIS PERALATAN</th>
                            <th class="align-middle">MERK / TIPE</th>
                            <th class="text-right align-middle" style="width: 130px;">ESTIMASI UM</th>
                            <th class="text-right align-middle" style="width: 140px;">REALISASI BELI</th>
                            <th class="text-center align-middle" style="width: 110px;">STRUK BELI</th>
                            <th class="text-center align-middle" style="width: 130px;">BUKTI PENGEMBALIAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lpj->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle"><strong>{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</strong></td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-muted">
                                        {{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</td>
                                <td class="align-middle">{{ $item->merk }} {{ $item->type }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($item->harga_pembelian, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    @if (empty($item->struk_pembelian))
                                        <span class="text-muted small"><i>(Tidak ada struk)</i></span>
                                    @else
                                        <a href="{{ url('images/lpj/struk_pembelian/' . $item->struk_pembelian) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-receipt mr-1"></i> Lihat Struk
                                        </a>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if (empty($lpj->bukti_pengembalian))
                                        <span class="text-muted small"><i>-</i></span>
                                    @else
                                        <a href="{{ url('images/lpj/bukti_pengembalian/' . $lpj->bukti_pengembalian) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                                            <i class="fas fa-file-invoice mr-1"></i> Bukti Sisa
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">Tidak ada rincian item LPJ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right font-weight-bold">Total Estimasi Uang Muka:</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($lpj->subtotal(), 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right font-weight-bold">Total Realisasi Pembelian:</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($lpj->total_pembelian(), 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                        @if ($lpj->total_pembelian() > $lpj->subtotal())
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Kekurangan Dana:</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($lpj->total_pembelian() - $lpj->subtotal(), 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Sisa / Pengembalian Dana:</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($lpj->subtotal() - $lpj->total_pembelian(), 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
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
        function approve(form_id) {
            if (confirm('Apakah Anda yakin ingin MENYETUJUI (Approve) LPJ ini?')) {
                $(form_id).submit();
            }
        }

        function tolak(form_id) {
            if (confirm('Apakah Anda yakin ingin MENOLAK LPJ ini?')) {
                $(form_id).submit();
            }
        }
    </script>
@endpush
