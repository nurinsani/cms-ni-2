@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan-hapus-buku.index') }}">Hapus Buku</a></li>
            <li class="breadcrumb-item active">{{ $title }}</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
{{-- Ringkasan Akuntansi --}}
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
    <!-- Kolom Kiri: Informasi Pengajuan & Riwayat Approval -->
    <div class="col-lg-4 col-md-12 mb-4">
        <!-- Kartu Detail Pengajuan -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-info-circle mr-2"></i> Informasi Pengajuan
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
                        <th class="text-muted">Tgl Pengajuan</th>
                        <td>: {{ $pengajuan->created_at ? $pengajuan->created_at->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Diajukan Oleh</th>
                        <td>: {{ $pengajuan->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status Approval</th>
                        <td>: 
                            @if ($pengajuan->status_approve === 'admin-head')
                                <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Menunggu Admin Head</span>
                            @elseif ($pengajuan->status_approve === 'akunting')
                                <span class="badge badge-info"><i class="fas fa-user-check mr-1"></i> Menunggu Akunting</span>
                            @elseif ($pengajuan->status_approve === 'selesai')
                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Selesai (Disetujui)</span>
                            @elseif ($pengajuan->status_approve === 'ditolak')
                                <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Ditolak</span>
                            @else
                                <span class="badge badge-secondary">{{ $pengajuan->status_approve }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="border-top pt-3 mt-2">
                    <label class="font-weight-bold text-dark mb-1">Alasan Hapus Buku:</label>
                    <p class="text-muted p-2 bg-light rounded border">{{ $pengajuan->alasan }}</p>
                </div>

                @if ($pengajuan->surat_moa)
                    <div class="border-top pt-2">
                        <label class="font-weight-bold text-dark mb-1">Dokumen Lampiran (MOA):</label>
                        <div>
                            <a href="{{ asset('images/hapus-buku/moa/' . $pengajuan->surat_moa) }}" target="_blank" class="btn btn-outline-primary btn-sm btn-block">
                                <i class="fas fa-file-download mr-1"></i> Lihat / Unduh Dokumen
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kartu Riwayat Approval -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-tasks mr-2 text-primary"></i> Alur Persetujuan
                </h5>
            </div>
            <div class="card-body p-3">
                <!-- Tahap 1: Admin Head -->
                <div class="p-3 mb-3 border rounded {{ $pengajuan->atasan_1 ? 'border-success bg-light' : 'border-warning' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold text-dark">1. Admin Head</span>
                        @if ($pengajuan->atasan_1)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                        @elseif ($pengajuan->status_approve === 'ditolak')
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    <small class="text-muted d-block">Pejabat: {{ $pengajuan->admin_head->name ?? 'Belum diproses' }}</small>
                    @if ($pengajuan->tanggal_approve_atasan_1)
                        <small class="text-muted d-block">Waktu: {{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_1)->format('d-m-Y H:i') }}</small>
                    @endif
                    @if ($pengajuan->catatan_atasan_1)
                        <small class="text-info d-block mt-1">Catatan: "{{ $pengajuan->catatan_atasan_1 }}"</small>
                    @endif
                </div>

                <!-- Tahap 2: Akunting -->
                <div class="p-3 border rounded {{ $pengajuan->status_approve === 'selesai' ? 'border-success bg-light' : ($pengajuan->status_approve === 'akunting' ? 'border-primary' : 'border-secondary') }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold text-dark">2. Akunting</span>
                        @if ($pengajuan->status_approve === 'selesai')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                        @elseif ($pengajuan->status_approve === 'ditolak' && $pengajuan->atasan_2)
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-clock"></i> Menunggu</span>
                        @endif
                    </div>
                    <small class="text-muted d-block">Pejabat: {{ $pengajuan->akunting->name ?? 'Belum diproses' }}</small>
                    @if ($pengajuan->tanggal_approve_atasan_2)
                        <small class="text-muted d-block">Waktu: {{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_2)->format('d-m-Y H:i') }}</small>
                    @endif
                    @if ($pengajuan->catatan_atasan_2)
                        <small class="text-info d-block mt-1">Catatan: "{{ $pengajuan->catatan_atasan_2 }}"</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Barang & Foto Kondisi ATI (Format Seperti PDF MOA) -->
    <div class="col-lg-8 col-md-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-camera mr-2 text-primary"></i> Foto Kondisi Barang ATI & Spesifikasi
                </h5>
                <span class="badge badge-primary px-2 py-1">{{ $pengajuan->details->count() }} Unit Barang</span>
            </div>
            <div class="card-body">
                <!-- Tampilan Galeri Foto Kondisi Barang dengan Label Kode Peralatan (Konsep PDF) -->
                <div class="row">
                    @forelse ($pengajuan->details as $detail)
                        @php $inv = $detail->inventaris; @endphp
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border shadow-sm text-center">
                                <div class="p-2" style="background-color: #f8f9fa;">
                                    @if ($detail->foto && file_exists(public_path('images/hapus-buku/kondisi/' . $detail->foto)))
                                        <a href="{{ asset('images/hapus-buku/kondisi/' . $detail->foto) }}" target="_blank">
                                            <img src="{{ asset('images/hapus-buku/kondisi/' . $detail->foto) }}" 
                                                 alt="{{ $inv->kode_peralatan ?? 'Foto Barang' }}" 
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
                                    <!-- Label Kode Peralatan seperti di dokumen PDF -->
                                    <h6 class="font-weight-bold text-primary mb-1 font-monospace" style="font-size: 13px;">
                                        {{ $inv->kode_peralatan ?? '-' }}
                                    </h6>
                                    <div class="small font-weight-bold text-dark">
                                        {{ $inv->jenis ?? '' }} - {{ $inv->merk ?? '' }}
                                    </div>
                                    <div class="small text-muted mb-1">
                                        {{ $inv->type ?? '' }}
                                    </div>
                                    <div class="small badge badge-danger">
                                        {{ $detail->kondisi ?? 'Rusak Berat' }}
                                    </div>
                                    <div class="small text-muted mt-1 border-top pt-1">
                                        Harga: Rp. {{ number_format(intval($inv->harga_perolehan ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            Tidak ada barang inventaris pada pengajuan ini.
                        </div>
                    @endforelse
                </div>

                {{-- Tabel Rincian Finansial --}}
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
                                    $inv   = $detail->inventaris;
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


                <div class="text-right mt-3">
                    <a href="{{ route('admin.pengajuan-hapus-buku.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
