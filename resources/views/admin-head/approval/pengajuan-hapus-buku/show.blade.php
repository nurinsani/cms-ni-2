@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-head.approval-hapus-buku.index') }}">Approval Hapus Buku</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Kolom Kiri: Informasi Pengajuan & Form Aksi -->
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
                                <a href="{{ $moaUrl }}" target="_blank"
                                    class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-file-pdf mr-1"></i> Lihat / Unduh Dokumen
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Aksi Persetujuan Admin Head -->
            @if ($pengajuan->status_approve === 'admin-head')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title font-weight-bold mb-0">
                            Keputusan Admin Head
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Form Approve -->
                        <form action="{{ route('admin-head.approval-hapus-buku.approve', $pengajuan->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Apakah Anda yakin menyetujui pengajuan ini dan meneruskannya ke Akunting?');">
                                <i class="fas fa-check-circle mr-1"></i> Setujui & Teruskan ke Akunting
                            </button>
                        </form>

                        <hr>

                        <!-- Form Tolak -->
                        <form action="{{ route('admin-head.approval-hapus-buku.tolak', $pengajuan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                                <i class="fas fa-times-circle mr-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

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
                                                if (file_exists(base_path('files/hapus_buku/kondisi/' . $detail->foto))) {
                                                    $fotoUrl = url('files/hapus_buku/kondisi/' . $detail->foto);
                                                } elseif (file_exists(public_path('images/hapus-buku/kondisi/' . $detail->foto))) {
                                                    $fotoUrl = asset('images/hapus-buku/kondisi/' . $detail->foto);
                                                }
                                            }
                                        @endphp
                                        @if ($fotoUrl)
                                            <a href="{{ $fotoUrl }}"
                                                target="_blank">
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

                    <div class="text-right mt-3">
                        <a href="{{ route('admin-head.approval-hapus-buku.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
