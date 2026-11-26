@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('admin-head/pengajuan-peralatan') }}">Approval Peralatan</a></li>
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
                    <a href="{{ url('admin-head/pengajuan-peralatan') }}" class="btn btn-sm btn-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    @if ($pengajuan->status_approve == 'admin-head')
                        <button type="button" onclick="approve('#approve{{ $pengajuan->id }}')" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-check mr-1"></i> Approve Pengajuan
                        </button>
                        <form class="d-inline" id="approve{{ $pengajuan->id }}" action="{{ url('admin-head/pengajuan-peralatan/approve', $pengajuan->id) }}" method="post">
                            @csrf
                        </form>

                        <button type="button" onclick="tolak('#tolak{{ $pengajuan->id }}')" class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-times mr-1"></i> Tolak Pengajuan
                        </button>
                        <form class="d-inline" id="tolak{{ $pengajuan->id }}" action="{{ url('admin-head/pengajuan-peralatan/tolak', $pengajuan->id) }}" method="post">
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
                            <td style="width: 140px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $pengajuan->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabel Rincian Item --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center align-middle" style="width: 45px;">NO</th>
                            <th class="align-middle">LOKASI (AP / CABANG)</th>
                            <th class="align-middle">KEPERLUAN / BARANG</th>
                            <th class="text-center align-middle" style="width: 65px;">QTY</th>
                            <th class="text-right align-middle" style="width: 140px;">HARGA SATUAN</th>
                            <th class="text-right align-middle" style="width: 150px;">TOTAL</th>
                            <th class="text-center align-middle" style="width: 110px;">BERKAS MOA</th>
                            <th class="align-middle">REKENING TUJUAN & KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</strong>
                                    @if ($item->area && $item->ap)
                                        <br><small class="text-muted">{{ $item->area->area }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</span>
                                    @if ($item->merk || $item->type)
                                        <br><small class="text-muted font-weight-normal">{{ $item->merk }} {{ $item->type }}</small>
                                    @endif
                                    @if ($item->jenis_peralatan?->param_peralatan?->coa)
                                        <br><span class="badge badge-light border text-muted">COA: {{ $item->jenis_peralatan->param_peralatan->coa }}</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">
                                    Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @if (empty($item->moa))
                                        <span class="text-muted small"><i>(Tidak ada berkas)</i></span>
                                    @else
                                        <a href="{{ url('files/moa/' . $item->moa) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-file-download mr-1"></i> Lihat MOA
                                        </a>
                                    @endif
                                </td>
                                <td class="align-middle small">
                                    @if ($item->rekening_id == null || !$item->rekening)
                                        Transfer ke:<br>
                                        <strong>{{ $pengajuan->user?->nama_bank }} - {{ $pengajuan->user?->no_rek }}</strong><br>
                                        a.n. {{ $pengajuan->user?->atas_nama }}
                                    @else
                                        Transfer ke:<br>
                                        <strong>{{ $item->rekening->nama_bank }} - {{ $item->rekening->no_rek }}</strong><br>
                                        a.n. {{ $item->rekening->atas_nama }}
                                    @endif
                                    @if ($item->keterangan)
                                        <hr class="my-1">
                                        <span class="text-muted">Ket: {{ $item->keterangan }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada rincian item pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right font-weight-bold">Subtotal / Total Uang Muka:</td>
                            <td class="text-right font-weight-bold">
                                Rp {{ number_format($pengajuan->subtotal(), 0, ',', '.') }}
                            </td>
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
        function approve(form_id) {
            if (confirm('Apakah Anda yakin ingin MENYETUJUI (Approve) pengajuan ini?')) {
                $(form_id).submit();
            }
        }

        function tolak(form_id) {
            if (confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?')) {
                $(form_id).submit();
            }
        }
    </script>
@endpush
