@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-head.approval-hapus-buku-gol-2.index') }}">Approval Hapus Buku Gol 2</a></li>
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
                            if (file_exists(base_path('files/hapus_buku_gol_2/moa/' . $pengajuan->surat_moa))) {
                                $moaUrl = url('files/hapus_buku_gol_2/moa/' . $pengajuan->surat_moa);
                            } elseif (file_exists(base_path('files/hapus_buku_gol_2/' . $pengajuan->surat_moa))) {
                                $moaUrl = url('files/hapus_buku_gol_2/' . $pengajuan->surat_moa);
                            } else {
                                $moaUrl = asset('images/hapus-buku-gol-2/moa/' . $pengajuan->surat_moa);
                            }
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

            <!-- Kartu Aksi Approval Admin Head -->
            @if ($pengajuan->status_approve === 'admin-head')
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title font-weight-bold mb-0">Tindakan Persetujuan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Form Setujui -->
                        <form action="{{ route('admin-head.approval-hapus-buku-gol-2.approve', $pengajuan->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Catatan Persetujuan (Opsional):</label>
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan persetujuan..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini dan meneruskannya ke Akunting?');">
                                <i class="fas fa-check mr-1"></i> Setujui & Teruskan ke Akunting
                            </button>
                        </form>

                        <!-- Form Tolak -->
                        <form action="{{ route('admin-head.approval-hapus-buku-gol-2.tolak', $pengajuan->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold text-danger">Alasan Penolakan:</label>
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Tuliskan alasan penolakan..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                                <i class="fas fa-times mr-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Galeri Foto Kondisi ATI Gol 2 & Rincian Finansial -->
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">
                        Foto Kondisi Barang & Rincian (Gol 2)
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Tampilan Galeri Foto Kondisi Barang -->
                    <div class="row">
                        @forelse ($pengajuan->details as $detail)
                            @php $inv = $detail->inventaris_gol_2; @endphp
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 border text-center">
                                    <div class="p-2" style="background-color: #f8f9fa;">
                                        @php
                                            $fotoUrl = null;
                                            if ($detail->foto) {
                                                if (file_exists(base_path('files/hapus_buku_gol_2/kondisi/' . $detail->foto))) {
                                                    $fotoUrl = url('files/hapus_buku_gol_2/kondisi/' . $detail->foto);
                                                } elseif (file_exists(base_path('files/hapus_buku_gol_2/' . $detail->foto))) {
                                                    $fotoUrl = url('files/hapus_buku_gol_2/' . $detail->foto);
                                                } elseif (file_exists(public_path('images/hapus-buku-gol-2/kondisi/' . $detail->foto))) {
                                                    $fotoUrl = asset('images/hapus-buku-gol-2/kondisi/' . $detail->foto);
                                                }
                                            }
                                        @endphp
                                        @if ($fotoUrl)
                                            <a href="{{ $fotoUrl }}"
                                                target="_blank">
                                                <img src="{{ $fotoUrl }}"
                                                    alt="{{ $inv->type ?? 'Foto Barang' }}"
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
                                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">
                                            {{ $inv->type ?? ($inv->jenis ?? 'Handphone') }}
                                        </h6>
                                        @if ($inv && $inv->imei)
                                            <div class="badge badge-secondary mb-1 font-monospace">
                                                IMEI: {{ $inv->imei }}
                                            </div>
                                        @endif
                                        <div class="small text-muted">
                                            Pengguna: <strong>{{ $inv->pengguna ?? '-' }}</strong>
                                        </div>
                                        <div class="small text-muted">
                                            Kondisi: <strong>{{ $detail->kondisi ?? '-' }}</strong>
                                        </div>
                                        <div class="small font-weight-bold mt-1 border-top pt-1">
                                            Harga: Rp. {{ number_format(intval($inv->harga_perolehan ?? 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                Tidak ada barang inventaris Gol 2 pada pengajuan ini.
                            </div>
                        @endforelse
                    </div>

                    {{-- Tabel Rincian Finansial --}}
                    <h6 class="font-weight-bold text-dark mt-3 mb-2">
                        Rincian Nilai Perolehan, Penyusutan & Nilai Buku (Gol 2)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" width="4%">No</th>
                                    <th>IMEI</th>
                                    <th>Type / Keterangan</th>
                                    <th>Pengguna</th>
                                    <th class="text-right">Harga Perolehan</th>
                                    <th class="text-right">Akum. Penyusutan</th>
                                    <th class="text-right">Nilai Buku</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuan->details as $detail)
                                    @php
                                        $inv = $detail->inventaris_gol_2;
                                        $harga = intval($inv->harga_perolehan ?? 0);
                                        $akum = $inv ? $inv->penyusutans()->sum('penyusutan_per_bulan') : 0;
                                        $nb = max(0, $harga - $akum);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold font-monospace">{{ $inv->imei ?? '-' }}</td>
                                        <td>{{ $inv->type ?? '-' }} ({{ $inv->keterangan ?? '-' }})</td>
                                        <td>{{ $inv->pengguna ?? '-' }}</td>
                                        <td class="text-right">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp. {{ number_format($akum, 0, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold">Rp. {{ number_format($nb, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="4" class="text-right">Total:</td>
                                    <td class="text-right">Rp. {{ number_format($totalHargaPerolehan, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp. {{ number_format($totalAkumulasiPenyusutan, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp. {{ number_format($totalNilaiBuku, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <a href="{{ route('admin-head.approval-hapus-buku-gol-2.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
