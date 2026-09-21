@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('akunting.approval-hapus-buku.index') }}">Approval Hapus Buku</a></li>
            <li class="breadcrumb-item active">{{ $title }}</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<!-- Ringkasan Akuntansi -->
<div class="row mb-3">
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Harga Perolehan</span>
                <span class="info-box-number" style="font-size: 1.25rem;">Rp. {{ number_format($totalHargaPerolehan, 0, ',', '.') }}</span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description text-white-50">Harga awal aset tercatat</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Akumulasi Penyusutan</span>
                <span class="info-box-number" style="font-size: 1.25rem;">Rp. {{ number_format($totalAkumulasiPenyusutan, 0, ',', '.') }}</span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description text-white-50">Penyusutan hingga saat ini</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-book-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Nilai Buku</span>
                <span class="info-box-number" style="font-size: 1.25rem;">Rp. {{ number_format($totalNilaiBuku, 0, ',', '.') }}</span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description text-white-50">Sisa nilai buku yang dihapuskan</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Detail Pengajuan & Formulir Keputusan Akunting -->
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-info-circle mr-2"></i> Data Pengajuan
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th class="text-muted" width="45%">Kode Pengajuan</th>
                        <td class="font-weight-bold">: {{ $pengajuan->kode_pengajuan }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Cabang / AP</th>
                        <td>: {{ $pengajuan->branch->unit ?? $pengajuan->kode_branch }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jenis Aktiva</th>
                        <td>: <span class="badge badge-info">{{ $pengajuan->jenis_aktiva }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bulan Hapus Buku</th>
                        <td>: {{ $pengajuan->bulan_hapus_buku }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Admin Pengaju</th>
                        <td>: {{ $pengajuan->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Disetujui Admin Head</th>
                        <td>: 
                            <span class="text-success font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i> {{ $pengajuan->admin_head->name ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @if ($pengajuan->catatan_atasan_1)
                        <tr>
                            <th class="text-muted">Catatan Admin Head</th>
                            <td class="text-info font-italic">: "{{ $pengajuan->catatan_atasan_1 }}"</td>
                        </tr>
                    @endif
                </table>

                <div class="border-top pt-3 mt-2">
                    <label class="font-weight-bold text-dark mb-1">Alasan Hapus Buku:</label>
                    <p class="text-muted p-2 bg-light rounded border">{{ $pengajuan->alasan }}</p>
                </div>

                @if ($pengajuan->surat_moa)
                    <div class="border-top pt-2">
                        <label class="font-weight-bold text-dark mb-1">Dokumen MOA Terlampir:</label>
                        <div>
                            <a href="{{ asset('images/hapus-buku/moa/' . $pengajuan->surat_moa) }}" target="_blank" class="btn btn-outline-primary btn-sm btn-block">
                                <i class="fas fa-file-pdf mr-1"></i> Buka Dokumen MOA
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kartu Aksi Approval Akunting -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-stamp mr-2 text-primary"></i> Keputusan Akunting
                </h5>
            </div>
            <div class="card-body">
                <!-- Form Approve Akunting -->
                <form action="{{ route('akunting.approval-hapus-buku.approve', $pengajuan->id) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Apakah Anda yakin menyetujui hapus buku ini? Status inventaris akan diperbarui menjadi Hapus Buku.');">
                        <i class="fas fa-check-double mr-1"></i> Setujui Hapus Buku
                    </button>
                </form>

                <hr>

                <!-- Form Tolak Akunting -->
                <form action="{{ route('akunting.approval-hapus-buku.tolak', $pengajuan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                        <i class="fas fa-times-circle mr-1"></i> Tolak Pengajuan
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('akunting.approval-hapus-buku.index') }}" class="btn btn-link btn-sm text-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Foto Kondisi Barang ATI & Tabel Nilai Buku Akuntansi -->
    <div class="col-lg-8 col-md-12">
        <!-- Grid Foto Kondisi Barang (Konsep PDF MOA) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-camera mr-2 text-primary"></i> Foto Kondisi Barang Fisik (ATI)
                </h5>
                <span class="badge badge-primary px-2 py-1">{{ $pengajuan->details->count() }} Unit</span>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($pengajuan->details as $detail)
                        @php $inv = $detail->inventaris; @endphp
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border shadow-sm text-center">
                                <div class="p-2" style="background-color: #f8f9fa;">
                                    @if ($detail->foto && file_exists(public_path('images/hapus-buku/kondisi/' . $detail->foto)))
                                        <a href="{{ asset('images/hapus-buku/kondisi/' . $detail->foto) }}" target="_blank">
                                            <img src="{{ asset('images/hapus-buku/kondisi/' . $detail->foto) }}" 
                                                 alt="{{ $inv->kode_peralatan ?? 'Foto' }}" 
                                                 class="img-fluid rounded" 
                                                 style="height: 220px; width: 100%; object-fit: cover;">
                                        </a>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 220px;">
                                            <div>
                                                <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                                <div class="small">Tidak Ada Foto</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body p-2 bg-white border-top">
                                    <h6 class="font-weight-bold text-primary mb-1 font-monospace" style="font-size: 13px;">
                                        {{ $inv->kode_peralatan ?? '-' }}
                                    </h6>
                                    <div class="small font-weight-bold text-dark">
                                        {{ $inv->jenis ?? '' }} - {{ $inv->merk ?? '' }}
                                    </div>
                                    <div class="small text-muted mb-1">
                                        {{ $inv->type ?? '' }}
                                    </div>
                                    <span class="badge badge-danger small">
                                        {{ $detail->kondisi ?? 'Rusak Berat' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            Tidak ada barang inventaris.
                        </div>
                    @endforelse
                </div>

                <!-- Tabel Detail Finansial Akuntansi -->
                <h6 class="font-weight-bold text-dark mt-3 mb-2">
                    <i class="fas fa-table mr-1"></i> Rincian Nilai Perolehan, Penyusutan & Nilai Buku
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="4%">No</th>
                                <th>Kode Peralatan</th>
                                <th>Jenis / Merk</th>
                                <th class="text-right">Harga Perolehan</th>
                                <th class="text-right">Akum. Penyusutan</th>
                                <th class="text-right">Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengajuan->details as $detail)
                                @php
                                    $inv  = $detail->inventaris;
                                    $harga = intval($inv->harga_perolehan ?? 0);

                                    $akumDariPenyusutan = $inv ? $inv->penyusutans()->sum('penyusutan_per_bulan') : 0;
                                    $penyusutanAwal     = floatval($inv->total_penyusutan_awal ?? 0);
                                    $auditAkum          = ($inv && $inv->status_branch === 'AUDIT')
                                        ? floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0)
                                        : 0;

                                    $akum = $penyusutanAwal + $akumDariPenyusutan + $auditAkum;
                                    $nb   = max(0, $harga - $akum);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold text-primary">{{ $inv->kode_peralatan ?? '-' }}</td>
                                    <td>{{ $inv->jenis ?? '-' }} ({{ $inv->merk ?? '-' }})</td>
                                    <td class="text-right">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
                                    <td class="text-right text-muted">Rp. {{ number_format($akum, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold text-success">Rp. {{ number_format($nb, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td colspan="3" class="text-right">Total:</td>
                                <td class="text-right text-primary">Rp. {{ number_format($totalHargaPerolehan, 0, ',', '.') }}</td>
                                <td class="text-right text-muted">Rp. {{ number_format($totalAkumulasiPenyusutan, 0, ',', '.') }}</td>
                                <td class="text-right text-success">Rp. {{ number_format($totalNilaiBuku, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
