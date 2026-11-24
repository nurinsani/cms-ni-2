@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('admin/klaim-pembelian') }}">Klaim Pembelian</a></li>
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
                <h4 class="font-weight-bold mb-1">KLAIM PEMBELIAN / PERMOHONAN DANA</h4>
                <h5 class="mb-0">TANGGAL {{ $klaim->tanggal_pengajuan ? $klaim->tanggal_pengajuan->format('d-M-Y') : '-' }}</h5>
            </div>
            <div></div>
        </div>

        {{-- Header Layar --}}
        <div class="card-header d-print-none">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ url('admin/klaim-pembelian') }}" class="btn btn-sm btn-secondary mr-1">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
                <div>
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
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $klaim->nomor }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Kepada</td>
                            <td>:</td>
                            <td>Ketua dan Pengurus KSPPS Nur Insani</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Status</td>
                            <td>:</td>
                            <td>
                                @php
                                    $badgeClass = 'badge-secondary';
                                    $statusText = strtoupper($klaim->status_approve ?? '-');
                                    if (str_contains(strtolower($statusText), 'acc') || str_contains(strtolower($statusText), 'cair') || str_contains(strtolower($statusText), 'selesai')) {
                                        $badgeClass = 'badge-success';
                                    } elseif (str_contains(strtolower($statusText), 'tolak')) {
                                        $badgeClass = 'badge-danger';
                                    } elseif (str_contains(strtolower($statusText), 'admin') || str_contains(strtolower($statusText), 'menunggu') || str_contains(strtolower($statusText), 'pending')) {
                                        $badgeClass = 'badge-warning';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1">{{ $statusText }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $klaim->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $klaim->tanggal_pengajuan ? $klaim->tanggal_pengajuan->format('d-M-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Jenis Pengajuan</td>
                            <td>:</td>
                            <td>
                                <span class="badge badge-info px-2 py-1">Klaim Pembelian</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabel Rincian Item Klaim --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th style="width: 40px;">No</th>
                            <th>Cabang / Area</th>
                            <th>Keperluan & Spesifikasi</th>
                            <th style="width: 60px;">Qty</th>
                            <th style="width: 140px;">Harga Satuan</th>
                            <th style="width: 150px;">Total</th>
                            <th style="width: 140px;">Bukti / Berkas</th>
                            <th style="width: 250px;">Rekening & Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($klaim->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="font-weight-bold">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</div>
                                    @if ($item->ap && $item->area)
                                        <small class="text-muted">{{ $item->area->area }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div><strong>{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</strong></div>
                                    <div class="small text-muted">
                                        Merk/Tipe: {{ $item->merk ?? '-' }} {{ $item->type ? '(' . $item->type . ')' : '' }}
                                    </div>
                                    @if ($item->jenis_peralatan?->param_peralatan?->coa)
                                        <div class="small text-muted">
                                            COA: <code>{{ $item->jenis_peralatan->param_peralatan->coa }}</code>
                                        </div>
                                    @endif
                                    @if ($item->nama_toko)
                                        <div class="small text-muted">
                                            Toko: {{ $item->nama_toko }} {{ $item->no_telp ? '('.$item->no_telp.')' : '' }}
                                        </div>
                                    @endif
                                    @if ($item->tanggal_pembelian)
                                        <div class="small text-muted">
                                            Tgl Pembelian: {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d-M-Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                                <td class="text-right align-middle font-weight-bold">
                                    Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    @if (!empty($item->struk_pembelian))
                                        <div class="mb-1">
                                            <a href="{{ url('files/struk_pembelian/' . $item->struk_pembelian) }}" target="_blank"
                                                class="btn btn-xs btn-outline-primary btn-block">
                                                <i class="fas fa-receipt mr-1"></i> Struk Nota
                                            </a>
                                        </div>
                                    @endif

                                    @if (!empty($item->moa))
                                        <div>
                                            <a href="{{ url('files/moa/' . $item->moa) }}" target="_blank"
                                                class="btn btn-xs btn-outline-info btn-block">
                                                <i class="fas fa-file-download mr-1"></i> Berkas MOA
                                            </a>
                                        </div>
                                    @endif

                                    @if (empty($item->struk_pembelian) && empty($item->moa))
                                        <span class="text-muted small"><i>(Tidak ada berkas)</i></span>
                                    @endif
                                </td>
                                <td class="align-middle small">
                                    @if ($item->rekening_id == null || !$item->rekening)
                                        Transfer ke:<br>
                                        <strong>{{ $klaim->user?->nama_bank }} - {{ $klaim->user?->no_rek }}</strong><br>
                                        a.n. {{ $klaim->user?->atas_nama }}
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
                                <td colspan="8" class="text-center text-muted py-3">Tidak ada rincian item klaim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Pengajuan Klaim (Subtotal)</strong></td>
                            <td class="text-right font-weight-bold">
                                Rp {{ number_format($klaim->subtotal(), 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
