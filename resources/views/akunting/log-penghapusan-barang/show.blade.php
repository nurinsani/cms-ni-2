@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('akunting.log-penghapusan-barang.index', ['gol' => $gol ?? '1']) }}">Log Hapus Buku</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Kolom Kiri: Informasi Barang & Pengajuan Hapus Buku -->
        <div class="col-lg-5 col-md-12 mb-4">
            <!-- Informasi Inventaris -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Informasi Barang Inventaris Gol {{ $gol ?? '1' }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        @if (($gol ?? '1') == '2')
                            <tr>
                                <th class="text-muted" width="40%">IMEI</th>
                                <td class="font-weight-bold font-monospace">: {{ $inventaris->imei ?? ($inventaris->kode_peralatan ?: '-') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Type / Merk</th>
                                <td>: {{ $inventaris->type ?? '-' }} {{ $inventaris->merk ? '(' . $inventaris->merk . ')' : '' }}</td>
                            </tr>
                        @else
                            <tr>
                                <th class="text-muted" width="40%">Kode Inventaris</th>
                                <td class="font-weight-bold font-monospace">: {{ $inventaris->kode_peralatan }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Merk / Type</th>
                                <td>: {{ $inventaris->merk ?? '-' }} / {{ $inventaris->type ?? '-' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted">Cabang / AP</th>
                            <td>: {{ $inventaris->branch->unit ?? $inventaris->kode_branch }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Jenis Barang</th>
                            <td>: {{ $inventaris->jenis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tgl Perolehan</th>
                            <td>: {{ $inventaris->tanggal_perolehan ?? ($inventaris->tanggal_pembelian ? \Carbon\Carbon::parse($inventaris->tanggal_pembelian)->format('d-m-Y') : '-') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Pengguna</th>
                            <td>: {{ $inventaris->pengguna ?? '-' }} {{ $inventaris->jabatan ? '(' . $inventaris->jabatan . ')' : '' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Keterangan</th>
                            <td>: {{ $inventaris->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status Aset</th>
                            <td>: <span class="badge badge-secondary px-2 py-1">Hapus Buku</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Pengesahan Hapus Buku -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Riwayat Pengesahan Hapus Buku</h5>
                </div>
                <div class="card-body">
                    @if ($pengajuan)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" width="40%">Kode Pengajuan</th>
                                <td class="font-weight-bold">: 
                                    @if (($gol ?? '1') == '2')
                                        <a href="{{ route('akunting.approval-hapus-buku-gol-2.show', $pengajuan->id) }}">
                                            {{ $pengajuan->kode_pengajuan }}
                                        </a>
                                    @else
                                        <a href="{{ route('akunting.approval-hapus-buku.show', $pengajuan->id) }}">
                                            {{ $pengajuan->kode_pengajuan }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Bulan Hapus Buku</th>
                                <td>: {{ $pengajuan->bulan_hapus_buku }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Diajukan Oleh</th>
                                <td>: {{ $pengajuan->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tgl Pengajuan</th>
                                <td>: {{ $pengajuan->created_at ? $pengajuan->created_at->format('d-m-Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Disetujui Admin Head</th>
                                <td>: 
                                    @if ($pengajuan->atasan_1)
                                        <span class="font-weight-bold text-dark">{{ $pengajuan->admin_head->name ?? '-' }}</span>
                                        @if ($pengajuan->tanggal_approve_atasan_1)
                                            <small class="text-muted d-block">({{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_1)->format('d-m-Y H:i') }})</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Disetujui Akunting</th>
                                <td>: 
                                    @if ($pengajuan->atasan_2)
                                        <span class="font-weight-bold text-dark">{{ $pengajuan->akunting->name ?? '-' }}</span>
                                        @if ($pengajuan->tanggal_approve_atasan_2)
                                            <small class="text-muted d-block">({{ \Carbon\Carbon::parse($pengajuan->tanggal_approve_atasan_2)->format('d-m-Y H:i') }})</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div class="border-top pt-2 mt-2">
                            <label class="font-weight-bold text-dark mb-1">Alasan Hapus Buku:</label>
                            <p class="text-muted p-2 bg-light rounded border">{{ $pengajuan->alasan }}</p>
                        </div>

                        @if ($pengajuan->surat_moa)
                            @php
                                $isGol2 = (($gol ?? '1') == '2');
                                $subDirMoa = $isGol2 ? 'hapus_buku_gol_2/moa' : 'hapus_buku/moa';
                                $oldMoa = $isGol2 ? 'images/hapus-buku-gol-2/moa/' : 'images/hapus-buku/moa/';

                                if (file_exists(base_path('files/' . $subDirMoa . '/' . $pengajuan->surat_moa))) {
                                    $moaUrl = url('files/' . $subDirMoa . '/' . $pengajuan->surat_moa);
                                } elseif ($isGol2 && file_exists(base_path('files/hapus_buku_gol_2/' . $pengajuan->surat_moa))) {
                                    $moaUrl = url('files/hapus_buku_gol_2/' . $pengajuan->surat_moa);
                                } elseif (file_exists(public_path($oldMoa . $pengajuan->surat_moa))) {
                                    $moaUrl = asset($oldMoa . $pengajuan->surat_moa);
                                } else {
                                    $moaUrl = url('files/' . $subDirMoa . '/' . $pengajuan->surat_moa);
                                }
                            @endphp
                            <div class="border-top pt-2">
                                <label class="font-weight-bold text-dark mb-1">Dokumen Lampiran (MOA):</label>
                                <div>
                                    <a href="{{ $moaUrl }}" target="_blank"
                                        class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat / Unduh Dokumen MOA
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Barang ini diubah statusnya menjadi hapus buku secara langsung.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Foto Fisik & Nilai Akuntansi -->
        <div class="col-lg-7 col-md-12">
            <!-- Foto Fisik Barang -->
            @php
                if ($pengajuan) {
                    $detailHapusBuku = (($gol ?? '1') == '2')
                        ? $pengajuan->details->where('inventaris_gol_2_id', $inventaris->id)->first()
                        : $pengajuan->details->where('inventaris_id', $inventaris->id)->first();
                } else {
                    $detailHapusBuku = null;
                }
                $fotoBarang = $detailHapusBuku ? $detailHapusBuku->foto : null;
                $kondisiBarang = $detailHapusBuku ? $detailHapusBuku->kondisi : null;

                $fotoUrl = null;
                if ($fotoBarang) {
                    $isGol2 = (($gol ?? '1') == '2');
                    $subDirKondisi = $isGol2 ? 'hapus_buku_gol_2/kondisi' : 'hapus_buku/kondisi';
                    $oldKondisi = $isGol2 ? 'images/hapus-buku-gol-2/kondisi/' : 'images/hapus-buku/kondisi/';

                    if (file_exists(base_path('files/' . $subDirKondisi . '/' . $fotoBarang))) {
                        $fotoUrl = url('files/' . $subDirKondisi . '/' . $fotoBarang);
                    } elseif ($isGol2 && file_exists(base_path('files/hapus_buku_gol_2/' . $fotoBarang))) {
                        $fotoUrl = url('files/hapus_buku_gol_2/' . $fotoBarang);
                    } elseif (file_exists(public_path($oldKondisi . $fotoBarang))) {
                        $fotoUrl = asset($oldKondisi . $fotoBarang);
                    }
                }
            @endphp
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Foto & Kondisi Fisik Barang</h5>
                </div>
                <div class="card-body text-center">
                    @if ($fotoUrl)
                        <a href="{{ $fotoUrl }}" target="_blank">
                            <img src="{{ $fotoUrl }}"
                                alt="{{ $inventaris->type ?? ($inventaris->kode_peralatan ?? 'Foto Barang') }}"
                                class="img-fluid rounded border"
                                style="max-height: 350px; object-fit: contain;">
                        </a>
                        <div class="mt-2 text-muted small">
                            Klik foto untuk melihat ukuran penuh
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded border" style="height: 220px;">
                            <div class="small">Tidak Ada Foto Barang Terlampir</div>
                        </div>
                    @endif

                    <div class="mt-3 text-left border-top pt-2">
                        <span class="font-weight-bold text-dark">Kondisi saat Hapus Buku:</span>
                        <span class="text-muted ml-1">{{ $kondisiBarang ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Nilai Finansial saat Hapus Buku -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">Rincian Finansial Akuntansi saat Hapus Buku</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Keterangan</th>
                                    <th class="text-right" width="40%">Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Harga Perolehan Awal</td>
                                    <td class="text-right font-weight-bold">
                                        Rp. {{ number_format(intval($inventaris->harga_perolehan ?? 0), 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Akumulasi Penyusutan</td>
                                    <td class="text-right">
                                        Rp. {{ number_format($inventaris->calc_akumulasi, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="bg-light font-weight-bold">
                                    <td>Nilai Buku Bersih (Telah Dihapuskan)</td>
                                    <td class="text-right font-weight-bold">
                                        Rp. {{ number_format($inventaris->calc_nilai_buku, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <a href="{{ route('akunting.log-penghapusan-barang.index', ['gol' => $gol ?? '1']) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
