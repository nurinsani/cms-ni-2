@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}">Pengajuan Mutasi Gol 2</a></li>
            <li class="breadcrumb-item active">{{ $pengajuan->kode_mutasi }}</li>
        </ol>
    </div>
</div>
@endsection

@push('css')
<style>
    .table-custom thead th {
        text-transform: uppercase;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
        background-color: #f8f9fa;
        vertical-align: middle;
    }
    .table-custom tbody td {
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        font-size: 0.88rem;
    }
    .nav-tabs .nav-link#jurnal-berkurang-tab {
        color: #dc3545;
    }
    .nav-tabs .nav-link#jurnal-berkurang-tab.active {
        color: #dc3545;
        border-top: 3px solid #dc3545;
        background-color: #fff;
    }
    .nav-tabs .nav-link#jurnal-bertambah-tab {
        color: #28a745;
    }
    .nav-tabs .nav-link#jurnal-bertambah-tab.active {
        color: #28a745;
        border-top: 3px solid #28a745;
        background-color: #fff;
    }
    .nav-tabs .nav-link#jurnal-kp-tab {
        color: #007bff;
    }
    .nav-tabs .nav-link#jurnal-kp-tab.active {
        color: #007bff;
        border-top: 3px solid #007bff;
        background-color: #fff;
    }
    .input-editable {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 4px 8px;
        background-color: #fff;
        transition: all 0.2s;
    }
    .input-editable:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@section('content')

<!-- Header Action -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Mutasi Gol 2
    </a>
    <div>
        <span class="badge badge-light border px-3 py-2 text-dark font-weight-bold" style="font-size: 0.85rem;">
            STATUS: {{ strtoupper($pengajuan->status_approve) }}
        </span>
    </div>
</div>

@php
    $total_harga_perolehan = 0;
    $total_akumulasi_penyusutan = 0;
    $total_nilai_buku = 0;

    foreach ($pengajuan->inventaris_gol_2 as $inv) {
        $penyusutanPerBulan = $inv->harga_perolehan / 24;
        $jumlahBulan = $inv->penyusutans()->count();
        $akumulasi = $penyusutanPerBulan * $jumlahBulan;
        $nilaiBuku = $inv->harga_perolehan - $akumulasi;

        $total_harga_perolehan += $inv->harga_perolehan;
        $total_akumulasi_penyusutan += $akumulasi;
        $total_nilai_buku += $nilaiBuku;
    }
@endphp

<!-- Card Informasi Mutasi -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold mb-0 text-dark">
                Informasi Mutasi Gol 2
            </h5>
            <span class="badge badge-light border text-dark font-weight-bold px-3 py-1">
                {{ $pengajuan->kode_mutasi }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <span class="text-muted small text-uppercase d-block mb-1 font-weight-bold">AP Asal</span>
                <div class="font-weight-bold text-dark">{{ $pengajuan->dari_ap->unit ?? '-' }}</div>
                <small class="text-muted">Kode Branch: {{ $pengajuan->dari_cabang }}</small>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <span class="text-muted small text-uppercase d-block mb-1 font-weight-bold">AP Tujuan</span>
                <div class="font-weight-bold text-dark">{{ $pengajuan->branch->unit ?? '-' }}</div>
                <small class="text-muted">Kode Branch: {{ $pengajuan->ke_cabang }}</small>
            </div>
            <div class="col-md-4">
                <span class="text-muted small text-uppercase d-block mb-1 font-weight-bold">Bulan Mutasi</span>
                <div class="font-weight-bold text-dark">{{ strtoupper($pengajuan->bulan_mutasi) }}</div>
                <small class="text-muted">{{ $pengajuan->inventaris_gol_2->count() }} Unit Barang</small>
            </div>
        </div>
    </div>
</div>

<!-- Card Daftar Inventaris Gol 2 yang Dimutasi -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title font-weight-bold mb-0 text-dark">
            Daftar Barang Inventaris Gol 2
        </h5>
    </div>
    <div class="p-3 pb-2">
        <div class="alert alert-light border d-flex align-items-center mb-2 py-2 small text-muted">
            <i class="fas fa-info-circle text-primary mr-2" style="font-size: 1.1rem;"></i>
            <span><strong>Petunjuk:</strong> Kolom <strong>NIP</strong>, <strong>Pengguna</strong>, dan <strong>Jabatan</strong> dapat diedit langsung pada tabel. Ubah teks lalu tekan <strong>Enter</strong> atau klik di luar kotak untuk menyimpan perubahan.</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">NO</th>
                        <th>KODE INV</th>
                        <th>JENIS</th>
                        <th>IMEI</th>
                        <th class="text-right">HRG PEROLEHAN</th>
                        <th class="text-right">AKM PENYUSUTAN</th>
                        <th class="text-right">NILAI BUKU</th>
                        <th style="min-width: 140px;">NIP <i class="fas fa-pencil-alt text-muted ml-1" style="font-size: 0.75rem;" title="Dapat diedit langsung"></i></th>
                        <th style="min-width: 160px;">PENGGUNA <i class="fas fa-pencil-alt text-muted ml-1" style="font-size: 0.75rem;" title="Dapat diedit langsung"></i></th>
                        <th style="min-width: 160px;">JABATAN <i class="fas fa-pencil-alt text-muted ml-1" style="font-size: 0.75rem;" title="Dapat diedit langsung"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                        @php
                            $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                            $jumlahBulan = $inventaris->penyusutans()->count();
                            $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                            $nilai_buku = $inventaris->harga_perolehan - $akumulasi_penyusutan;
                        @endphp
                        <tr>
                            <td class="text-center font-weight-bold">{{ $key + 1 }}</td>
                            <td class="font-weight-bold">{{ $inventaris->kode_peralatan }}</td>
                            <td>{{ $inventaris->jenis ?? '-' }}</td>
                            <td>{{ $inventaris->imei ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($inventaris->harga_perolehan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($akumulasi_penyusutan, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($nilai_buku, 0, ',', '.') }}</td>
                            <td>
                                <input type="text"
                                    class="form-control form-control-sm editable input-editable"
                                    data-id="{{ $inventaris->id }}"
                                    data-field="nip"
                                    data-old="{{ $inventaris->nip }}"
                                    value="{{ $inventaris->nip }}"
                                    placeholder="Input NIP...">
                            </td>
                            <td>
                                <input type="text"
                                    class="form-control form-control-sm editable input-editable"
                                    data-id="{{ $inventaris->id }}"
                                    data-field="pengguna"
                                    data-old="{{ $inventaris->pengguna }}"
                                    value="{{ $inventaris->pengguna }}"
                                    placeholder="Input pengguna...">
                            </td>
                            <td>
                                <input type="text"
                                    class="form-control form-control-sm editable input-editable"
                                    data-id="{{ $inventaris->id }}"
                                    data-field="jabatan"
                                    data-old="{{ $inventaris->jabatan }}"
                                    value="{{ $inventaris->jabatan }}"
                                    placeholder="Input jabatan...">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Tidak ada rincian inventaris dalam pengajuan mutasi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-center text-uppercase">TOTAL</td>
                        <td class="text-right">Rp {{ number_format($total_harga_perolehan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($total_akumulasi_penyusutan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($total_nilai_buku, 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Card Jurnal Akuntansi Mutasi Gol 2 (Tabbed) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white p-2">
        <ul class="nav nav-tabs card-header-tabs" id="jurnalTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="jurnal-berkurang-tab" data-toggle="tab" href="#jurnal-berkurang" role="tab" aria-selected="true">
                    <i class="fas fa-arrow-down text-danger mr-1"></i> Jurnal Berkurang (AP Asal)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jurnal-bertambah-tab" data-toggle="tab" href="#jurnal-bertambah" role="tab" aria-selected="false">
                    <i class="fas fa-arrow-up text-success mr-1"></i> Jurnal Bertambah (AP Tujuan)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jurnal-kp-tab" data-toggle="tab" href="#jurnal-kp" role="tab" aria-selected="false">
                    <i class="fas fa-university text-primary mr-1"></i> Jurnal Kantor Pusat (KP)
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="jurnalTabContent">
            
            <!-- Tab 1: Jurnal Berkurang -->
            <div class="tab-pane fade show active" id="jurnal-berkurang" role="tabpanel">
                <div class="text-muted small mb-3">
                    Pencatatan jurnal pengurangan aset Gol 2 pada cabang asal: <strong>{{ $pengajuan->dari_ap->unit ?? '-' }}</strong>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">JENIS</th>
                                <th>KETERANGAN</th>
                                <th style="width: 180px;">KODE REKENING</th>
                                <th style="width: 180px;" class="text-right">DEBET (RP)</th>
                                <th style="width: 180px;" class="text-right">KREDIT (RP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_debet_berkurang = 0;
                                $total_kredit_berkurang = 0;
                            @endphp
                            @foreach ($pengajuan->inventaris_gol_2 as $inventaris)
                                @php
                                    $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                                    $jumlahBulan = $inventaris->penyusutans()->count();
                                    $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                                    $nilai_buku = $inventaris->harga_perolehan - $akumulasi_penyusutan;
                                    $harga_perolehan = $inventaris->harga_perolehan;

                                    $total_debet_berkurang += ($akumulasi_penyusutan + $nilai_buku);
                                    $total_kredit_berkurang += $harga_perolehan;
                                @endphp
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1674000</td>
                                    <td class="text-right">{{ number_format($akumulasi_penyusutan, 0, ',', '.') }}</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>2500000</td>
                                    <td class="text-right">{{ number_format($nilai_buku, 0, ',', '.') }}</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1654000</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">{{ number_format($harga_perolehan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="3" class="text-center text-uppercase">TOTAL</td>
                                <td class="text-right">Rp {{ number_format($total_debet_berkurang, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($total_kredit_berkurang, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Jurnal Bertambah -->
            <div class="tab-pane fade" id="jurnal-bertambah" role="tabpanel">
                <div class="text-muted small mb-3">
                    Pencatatan jurnal penambahan aset Gol 2 pada cabang tujuan: <strong>{{ $pengajuan->branch->unit ?? '-' }}</strong>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">JENIS</th>
                                <th>KETERANGAN</th>
                                <th style="width: 180px;">KODE REKENING</th>
                                <th style="width: 180px;" class="text-right">DEBET (RP)</th>
                                <th style="width: 180px;" class="text-right">KREDIT (RP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_debet_bertambah = 0;
                                $total_kredit_bertambah = 0;
                            @endphp
                            @foreach ($pengajuan->inventaris_gol_2 as $inventaris)
                                @php
                                    $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                                    $jumlahBulan = $inventaris->penyusutans()->count();
                                    $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                                    $nilai_buku = $inventaris->harga_perolehan - $akumulasi_penyusutan;
                                    $harga_perolehan = $inventaris->harga_perolehan;

                                    $total_debet_bertambah += $harga_perolehan;
                                    $total_kredit_bertambah += ($akumulasi_penyusutan + $nilai_buku);
                                @endphp
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1654000</td>
                                    <td class="text-right">{{ number_format($harga_perolehan, 0, ',', '.') }}</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1674000</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">{{ number_format($akumulasi_penyusutan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>2500000</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">{{ number_format($nilai_buku, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="3" class="text-center text-uppercase">TOTAL</td>
                                <td class="text-right">Rp {{ number_format($total_debet_bertambah, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($total_kredit_bertambah, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Jurnal KP -->
            <div class="tab-pane fade" id="jurnal-kp" role="tabpanel">
                <div class="text-muted small mb-3">
                    Pencatatan rekening perantara pada Kantor Pusat (KP)
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">JENIS</th>
                                <th>KETERANGAN</th>
                                <th style="width: 180px;">KODE REKENING</th>
                                <th style="width: 180px;" class="text-right">DEBET (RP)</th>
                                <th style="width: 180px;" class="text-right">KREDIT (RP)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_debet_kp = 0;
                                $total_kredit_kp = 0;
                            @endphp
                            @foreach ($pengajuan->inventaris_gol_2 as $inventaris)
                                @php
                                    $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                                    $jumlahBulan = $inventaris->penyusutans()->count();
                                    $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                                    $nilai_buku = $inventaris->harga_perolehan - $akumulasi_penyusutan;

                                    $total_debet_kp += $nilai_buku;
                                    $total_kredit_kp += $nilai_buku;
                                @endphp
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1010-{{ $pengajuan->branch->GL ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($nilai_buku, 0, ',', '.') }}</td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>Bukti SYSTEM</td>
                                    <td>MUTASI INVENTARIS GOL 2 - {{ $inventaris->kode_peralatan }}</td>
                                    <td>1010-{{ $pengajuan->dari_ap->GL ?? '-' }}</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">{{ number_format($nilai_buku, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="3" class="text-center text-uppercase">TOTAL</td>
                                <td class="text-right">Rp {{ number_format($total_debet_kp, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($total_kredit_kp, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    document.querySelectorAll('.editable').forEach(input => {
        // Tekan Enter untuk langsung simpan (trigger blur)
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
        });

        input.addEventListener('change', function () {
            let el = this;
            let oldValue = el.getAttribute('data-old') ?? el.defaultValue;
            let id = el.dataset.id;
            let field = el.dataset.field;
            let value = el.value;

            if (value === oldValue) {
                return;
            }

            Swal.fire({
                title: 'Simpan perubahan?',
                text: 'Data ' + field + ' akan diperbarui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('inventaris-gol-2.update.inline') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            id: id,
                            field: field,
                            value: value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil diperbarui',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            el.defaultValue = value;
                            el.setAttribute('data-old', value);
                        } else {
                            el.value = oldValue;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Data gagal disimpan'
                            });
                        }
                    })
                    .catch(() => {
                        el.value = oldValue;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem saat menyimpan'
                        });
                    });
                } else {
                    el.value = oldValue;
                }
            });
        });
    });
</script>
@endpush
