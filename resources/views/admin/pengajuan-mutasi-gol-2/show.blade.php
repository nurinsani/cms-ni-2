@extends('layouts.main')

@section('content')

<a href="{{ route('admin.pengajuan-mutasi-gol-2.index') }}" class="btn btn-primary mb-3">
    <i class="fas fa-arrow-left"></i> Kembali
</a>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">
            Informasi Mutasi
        </h5>

        <div class="card border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">AP Asal</label>
                            <div class="text-bold">{{ $pengajuan->dari_ap->unit }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">AP Tujuan</label>
                            <div class="text-bold text-success">{{ $pengajuan->branch->unit }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="text-muted small mb-1">Bulan Mutasi</label>
                            <div class="text-bold">{{ strtoupper($pengajuan->bulan_mutasi) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-sm table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KODE INV</th>
                    <th>JENIS</th>
                    <th>IMEI</th>
                    <th>HRG PEROLEHAN</th>
                    <th>AKM PENYUSUTAN</th>
                    <th>NILAI BUKU</th>
                    <th>PENGGUNA</th>
                    <th>PENGGUNA LAMA</th>
                    <th>JABATAN</th>
                    <th>JABATAN LAMA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                        $jumlahBulan = $inventaris->penyusutans()->count();
                        
                        $harga_perolehan = $inventaris->harga_perolehan;
                        $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                        $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;

                        // Akumulasi total
                        $total_harga_perolehan += $harga_perolehan;
                        $total_akumulasi_penyusutan += $akumulasi_penyusutan;
                        $total_nilai_buku += $nilai_buku;
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $inventaris->kode_peralatan }}</td>
                        <td>{{ $inventaris->jenis }}</td>
                        <td>{{ $inventaris->imei }}</td>

                        
                        <td>{{ number_format($harga_perolehan) }}</td>
                        <td>{{ number_format($akumulasi_penyusutan) }}</td>
                        <td>{{ number_format($nilai_buku) }}</td>
                        <td>
                            <input type="text"
                                class="form-control form-control-sm editable"
                                data-id="{{ $inventaris->id }}"
                                data-field="pengguna"
                                data-old="{{ $inventaris->pengguna }}"
                                value="{{ $inventaris->pengguna }}">
                        </td>
                        <td>{{ $inventaris->old_pengguna }}</td>


                        <td>
                            <input type="text"
                                class="form-control form-control-sm editable"
                                data-id="{{ $inventaris->id }}"
                                data-field="jabatan"
                                data-old="{{ $inventaris->jabatan }}"
                                value="{{ $inventaris->jabatan }}">
                        </td>
                        <td>{{ $inventaris->old_jabatan }}</td>


                    </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                    <td><strong>{{ number_format($total_harga_perolehan) }}</strong></td>
                    <td><strong>{{ number_format($total_akumulasi_penyusutan) }}</strong></td>
                    <td><strong>{{ number_format($total_nilai_buku) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">Jurnal Berkurang</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                        $jumlahBulan = $inventaris->penyusutans()->count();
                        
                        $harga_perolehan = $inventaris->harga_perolehan;
                        $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                        $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;

                        // Akumulasi total
                        $total_harga_perolehan += $harga_perolehan;
                        $total_akumulasi_penyusutan += $akumulasi_penyusutan;
                        $total_nilai_buku += $nilai_buku;
                    @endphp
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1674000</td>
                        <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>2500000</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1654000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <h5 class="mb-3">Jurnal Bertambah</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                        $jumlahBulan = $inventaris->penyusutans()->count();
                        
                        $harga_perolehan = $inventaris->harga_perolehan;
                        $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                        $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;

                        // Akumulasi total
                        $total_harga_perolehan += $harga_perolehan;
                        $total_akumulasi_penyusutan += $akumulasi_penyusutan;
                        $total_nilai_buku += $nilai_buku;
                    @endphp
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1654000</td>
                        <td class="text-right">{{ number_format($harga_perolehan) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1674000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($akumulasi_penyusutan) }}</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>2500000</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <h5 class="mb-3">Jurnal KP</h5>

        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>JENIS</th>
                    <th>KETERANGAN</th>
                    <th>KODE REKENING</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_harga_perolehan = 0;
                    $total_akumulasi_penyusutan = 0;
                    $total_nilai_buku = 0;
                @endphp
                @foreach ($pengajuan->inventaris_gol_2 as $key => $inventaris)
                    @php
                        $jumlahPenyusutan = $inventaris->penyusutan_ke;
                        $penyusutanPerBulan = $inventaris->harga_perolehan / 24;
                        $jumlahBulan = $inventaris->penyusutans()->count();
                        
                        $harga_perolehan = $inventaris->harga_perolehan;
                        $akumulasi_penyusutan = $penyusutanPerBulan * $jumlahBulan;
                        $nilai_buku = $harga_perolehan - $akumulasi_penyusutan;

                        // Akumulasi total
                        $total_harga_perolehan += $harga_perolehan;
                        $total_akumulasi_penyusutan += $akumulasi_penyusutan;
                        $total_nilai_buku += $nilai_buku;
                    @endphp
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1010-{{ $pengajuan->branch->GL }}</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Bukti SYSTEM</td>
                        <td>MUTASI INVENTARIS GOL 2</td>
                        <td>1010-{{ $pengajuan->dari_ap->GL }}</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($nilai_buku) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('.editable').forEach(input => {
        input.addEventListener('change', function () {

            let el = this;
            let oldValue = el.getAttribute('data-old') ?? el.defaultValue;
            let id = el.dataset.id;
            let field = el.dataset.field;
            let value = el.value;

            // Jika tidak ada perubahan, hentikan
            if (value === oldValue) {
                return;
            }

            Swal.fire({
                title: 'Simpan perubahan?',
                text: 'Data akan diperbarui',
                icon: 'question',
                showCancelButton: true,
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
                                text: 'Data berhasil disimpan'
                            });

                            // update nilai awal
                            el.defaultValue = value;
                            el.setAttribute('data-old', value);
                        } else {
                            el.value = oldValue;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal disimpan'
                            });
                        }
                    })
                    .catch(() => {
                        el.value = oldValue;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem'
                        });
                    });

                } else {
                    // Batal → kembalikan nilai lama
                    el.value = oldValue;
                }
            });
        });
    });
</script>

@endsection
