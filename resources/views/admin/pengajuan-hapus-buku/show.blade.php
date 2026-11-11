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
    <div class="row">
        <!-- Kolom Kiri: Informasi Pengajuan & Riwayat Approval -->
        <div class="col-lg-4 col-md-12 mb-4">
            <!-- Kartu Detail Pengajuan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Informasi Pengajuan</h5>
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
                            <td>: {{ $pengajuan->jenis_aktiva }}</td>
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
                                    <span class="badge badge-warning px-2 py-1">Menunggu Admin Head</span>
                                @elseif ($pengajuan->status_approve === 'akunting')
                                    <span class="badge badge-info px-2 py-1">Menunggu Akunting</span>
                                @elseif ($pengajuan->status_approve === 'selesai')
                                    <span class="badge badge-success px-2 py-1">Disetujui (Selesai)</span>
                                @elseif ($pengajuan->status_approve === 'ditolak')
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ $pengajuan->status_approve }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="border-top pt-3 mt-2">
                        <label class="font-weight-bold text-dark mb-1">Alasan Hapus Buku:</label>
                        <p class="text-muted p-2 bg-light rounded border">{{ $pengajuan->alasan }}</p>
                    </div>

                    @if ($pengajuan->surat_moa)
                        @php
                            $moaUrl = file_exists(base_path('files/hapus_buku/moa/' . $pengajuan->surat_moa))
                                ? url('files/hapus_buku/moa/' . $pengajuan->surat_moa)
                                : asset('images/hapus-buku/moa/' . $pengajuan->surat_moa);
                        @endphp
                        <div class="border-top pt-2">
                            <label class="font-weight-bold text-dark mb-1">Dokumen Lampiran (MOA):</label>
                            <div>
                                <a href="{{ $moaUrl }}" target="_blank" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-file-pdf mr-1"></i> Lihat / Unduh Dokumen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kartu Riwayat Approval -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Alur Persetujuan</h5>
                </div>
                <div class="card-body p-3">
                    <!-- Tahap 1: Admin Head -->
                    <div class="p-3 mb-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark">1. Admin Head</span>
                            @if ($pengajuan->atasan_1)
                                <span class="badge badge-success px-2 py-1">Disetujui</span>
                            @elseif ($pengajuan->status_approve === 'ditolak')
                                <span class="badge badge-danger px-2 py-1">Ditolak</span>
                            @else
                                <span class="badge badge-warning px-2 py-1">Pending</span>
                            @endif
                        </div>
                        <small class="text-muted d-block">Oleh:
                            {{ $pengajuan->admin_head->name ?? '-' }}</small>
                        @if ($pengajuan->tanggal_approve_atasan_1)
                            <small class="text-muted d-block">Waktu:
                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_1)->format('d-m-Y H:i') }}</small>
                        @endif
                        @if ($pengajuan->catatan_atasan_1)
                            <small class="text-muted d-block mt-1">Catatan: "{{ $pengajuan->catatan_atasan_1 }}"</small>
                        @endif
                    </div>

                    <!-- Tahap 2: Akunting -->
                    <div class="p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark">2. Akunting</span>
                            @if ($pengajuan->status_approve === 'selesai')
                                <span class="badge badge-success px-2 py-1">Disetujui</span>
                            @elseif ($pengajuan->status_approve === 'ditolak' && $pengajuan->atasan_2)
                                <span class="badge badge-danger px-2 py-1">Ditolak</span>
                            @else
                                <span class="badge badge-secondary px-2 py-1">Menunggu</span>
                            @endif
                        </div>
                        <small class="text-muted d-block">Oleh:
                            {{ $pengajuan->akunting->name ?? '-' }}</small>
                        @if ($pengajuan->tanggal_approve_atasan_2)
                            <small class="text-muted d-block">Waktu:
                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_2)->format('d-m-Y H:i') }}</small>
                        @endif
                        @if ($pengajuan->catatan_atasan_2)
                            <small class="text-muted d-block mt-1">Catatan: "{{ $pengajuan->catatan_atasan_2 }}"</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Barang & Foto Kondisi ATI (Format Seperti PDF MOA) -->
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">
                        Foto Kondisi Barang ATI & Spesifikasi
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Tampilan Galeri Foto Kondisi Barang dengan Label Kode Peralatan (Konsep PDF) -->
                    <div class="row">
                        @forelse ($pengajuan->details as $detail)
                            @php $inv = $detail->inventaris; @endphp
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 border text-center">
                                    <div class="p-2" style="background-color: #f8f9fa;">
                                        @php
                                            $fotoUrl = null;
                                            if ($detail->foto) {
                                                if (
                                                    file_exists(base_path('files/hapus_buku/kondisi/' . $detail->foto))
                                                ) {
                                                    $fotoUrl = url('files/hapus_buku/kondisi/' . $detail->foto);
                                                } elseif (
                                                    file_exists(
                                                        public_path('images/hapus-buku/kondisi/' . $detail->foto),
                                                    )
                                                ) {
                                                    $fotoUrl = asset('images/hapus-buku/kondisi/' . $detail->foto);
                                                }
                                            }
                                        @endphp
                                        @if ($fotoUrl)
                                            <a href="{{ $fotoUrl }}" target="_blank">
                                                <img src="{{ $fotoUrl }}"
                                                    alt="{{ $inv->kode_peralatan ?? 'Foto Barang' }}"
                                                    class="img-fluid rounded"
                                                    style="height: 220px; width: 100%; object-fit: cover;">
                                            </a>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-light text-muted"
                                                style="height: 220px;">
                                                <div class="small">Tidak Ada Foto</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body p-2 bg-white border-top">
                                        <!-- Label Kode Peralatan seperti di dokumen PDF -->
                                        <h6 class="font-weight-bold text-dark mb-1 font-monospace" style="font-size: 13px;">
                                            {{ $inv->kode_peralatan ?? '-' }}
                                        </h6>
                                        <div class="small font-weight-bold text-dark">
                                            {{ $inv->jenis ?? '' }} - {{ $inv->merk ?? '-' }}
                                        </div>
                                        <div class="small text-muted mb-1">
                                            {{ $inv->type ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            Kondisi: {{ $detail->kondisi ?? '-' }}
                                        </div>
                                        <div class="small font-weight-bold mt-1 border-top pt-1">
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
                        Rincian Nilai Perolehan, Penyusutan & Nilai Buku
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
                                        $inv = $detail->inventaris;
                                        $harga = intval($inv->harga_perolehan ?? 0);

                                        $akumDariPenyusutan = $inv
                                            ? $inv->penyusutans()->sum('penyusutan_per_bulan')
                                            : 0;
                                        $penyusutanAwal = floatval($inv->total_penyusutan_awal ?? 0);
                                        $auditAkum =
                                            $inv && $inv->status_branch === 'AUDIT'
                                                ? floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0)
                                                : 0;

                                        $akum = $penyusutanAwal + $akumDariPenyusutan + $auditAkum;
                                        $nb = max(0, $harga - $akum);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">{{ $inv->kode_peralatan ?? '-' }}</td>
                                        <td>{{ $inv->jenis ?? '-' }} ({{ $inv->merk ?? '-' }})</td>
                                        <td class="text-right">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp. {{ number_format($akum, 0, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold">Rp. {{ number_format($nb, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="3" class="text-right">Total:</td>
                                    <td class="text-right">Rp. {{ number_format($totalHargaPerolehan, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp. {{ number_format($totalAkumulasiPenyusutan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">Rp. {{ number_format($totalNilaiBuku, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Preview Jurnal Hapus Buku --}}
                    <h6 class="font-weight-bold text-dark mt-4 mb-2">
                        Jurnal Hapus Buku (Golongan 1)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="15%">Kode Rekening</th>
                                    <th>Keterangan</th>
                                    <th class="text-right" width="18%">Debet</th>
                                    <th class="text-right" width="18%">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalDebetJurnal = 0;
                                    $totalKreditJurnal = 0;
                                @endphp
                                @forelse ($pengajuan->details as $detail)
                                    @php
                                        $inv = $detail->inventaris;
                                        if (!$inv) {
                                            continue;
                                        }

                                        $harga = intval($inv->harga_perolehan ?? 0);
                                        $akumDariPenyusutan = $inv->penyusutans()->sum('penyusutan_per_bulan');
                                        $penyusutanAwal = floatval($inv->total_penyusutan_awal ?? 0);
                                        $auditAkum =
                                            $inv->status_branch === 'AUDIT'
                                                ? floatval($inv->getRawOriginal('akumulasi_penyusutan') ?? 0)
                                                : 0;

                                        $akum = $penyusutanAwal + $akumDariPenyusutan + $auditAkum;
                                        $nb = max(0, $harga - $akum);
                                        $ketJurnal = 'HAPUS BUKU INVENTARIS 1 - ' . ($inv->keterangan ?? '-');
                                    @endphp
                                    <tr class="bg-light">
                                        <td colspan="4" class="font-weight-bold text-dark">
                                            {{ $loop->iteration }}. {{ $inv->kode_peralatan ?? '-' }} —
                                            {{ $inv->jenis ?? '-' }} ({{ $inv->merk ?? '-' }})
                                        </td>
                                    </tr>
                                    @if ($nb > 0)
                                        @php
                                            $totalDebetJurnal += $nb;
                                            $totalKreditJurnal += $nb;
                                        @endphp
                                        <tr>
                                            <td class="font-monospace">58114</td>
                                            <td>{{ $ketJurnal }}</td>
                                            <td class="text-right">Rp. {{ number_format($nb, 0, ',', '.') }}</td>
                                            <td class="text-right">-</td>
                                        </tr>
                                        <tr>
                                            <td class="font-monospace">1672000</td>
                                            <td>{{ $ketJurnal }}</td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">Rp. {{ number_format($nb, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                    @php
                                        $totalDebetJurnal += $harga;
                                        $totalKreditJurnal += $harga;
                                    @endphp
                                    <tr>
                                        <td class="font-monospace">1672000</td>
                                        <td>{{ $ketJurnal }}</td>
                                        <td class="text-right">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
                                        <td class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-monospace">1652000</td>
                                        <td>{{ $ketJurnal }}</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Tidak ada data jurnal.</td>
                                    </tr>
                                @endforelse
                                @if ($pengajuan->details->count() > 0)
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="2" class="text-right"></td>
                                        <td class="text-right">Rp. {{ number_format($totalDebetJurnal, 0, ',', '.') }}
                                        </td>
                                        <td class="text-right">Rp. {{ number_format($totalKreditJurnal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <a href="{{ route('admin.pengajuan-hapus-buku.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
