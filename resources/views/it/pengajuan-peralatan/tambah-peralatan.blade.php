@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">
                {{ $title }}
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('it.pengajuan.index') }}">Pengajuan</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menambahkan item:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD 1: FORM TAMBAH ITEM PERALATAN --}}
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                Form Input Item Peralatan
            </h3>
            <div class="card-tools">
                <a href="{{ route('it.pengajuan.index') }}" class="btn btn-sm btn-secondary mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <form action="{{ url('it/temp-pengajuan') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_peralatan">Kategori Peralatan <span class="text-danger">*</span></label>
                            <select id="nama_peralatan" class="form-control select2bs4" required>
                                <option value="">-- Pilih Peralatan --</option>
                                @foreach ($param_peralatan as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_jenis">Jenis Peralatan <span class="text-danger">*</span></label>
                            <select name="jenis_peralatan_id" id="nama_jenis" class="form-control jenis_peralatan select2bs4" required>
                                <option value="">-- Pilih Kategori Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kode_area">Cabang / Area <span class="text-danger">*</span></label>
                            <select name="kode_area" id="kode_area" class="form-control select2bs4" required>
                                <option value="">-- Pilih Cabang / Area --</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->code_area }}">{{ $item->area }} ({{ $item->code_area }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kode_branch">Nama Unit / AP <span class="text-danger">*</span></label>
                            <select name="kode_branch" id="kode_branch" class="form-control select2bs4" required>
                                <option value="">-- Pilih Cabang Dulu --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="merk">Merk <span class="text-danger">*</span></label>
                            <input type="text" name="merk" id="merk" class="form-control" value="{{ old('merk') }}" placeholder="Contoh: Logitech / TP-Link" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Tipe / Model <span class="text-danger">*</span></label>
                            <input type="text" name="type" id="type" class="form-control" value="{{ old('type') }}" placeholder="Contoh: B100 / TL-WR840N" required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kuantitas">Kuantitas <span class="text-danger">*</span></label>
                            <input type="number" name="kuantitas" id="kuantitas" min="1" class="form-control" value="{{ old('kuantitas', 1) }}" placeholder="1" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="harga">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="harga" id="harga" min="0" class="form-control" value="{{ old('harga') }}" placeholder="Masukkan nominal harga" required>
                            </div>
                            <div class="mt-1">
                                <span class="badge badge-light border text-dark" id="preview_harga">Rp 0</span>
                                <span class="badge badge-info ml-1" id="preview_total_harga" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_toko">Nama Toko / Vendor <span class="text-danger">*</span></label>
                            <input type="text" name="nama_toko" id="nama_toko" class="form-control" value="{{ old('nama_toko') }}" placeholder="Nama toko pembelian" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="no_telp">No. Telepon Toko <span class="text-danger">*</span></label>
                            <input type="number" name="no_telp" id="no_telp" class="form-control" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pengguna">Nama Pengguna (PIC) <span class="text-danger">*</span></label>
                            <input type="text" name="pengguna" id="pengguna" class="form-control" value="{{ old('pengguna') }}" placeholder="Nama pemakai barang" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="moa">Upload Berkas MOA <span class="text-danger">*</span></label>
                            <input type="file" name="moa" id="moa" class="form-control-file border rounded p-1 bg-light" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: PDF/JPG/PNG (Maks. 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="rekening_id">Rekening Tujuan Transfer <span class="text-muted font-weight-normal">(Opsional)</span></label>
                            <select name="rekening_id" id="rekening_id" class="form-control select2bs4" style="width: 100%;">
                                <option value="">-- Gunakan Rekening Pembuat ({{ auth()->user()->nama_bank }} - {{ auth()->user()->no_rek }}) --</option>
                                @foreach ($rekening as $item)
                                    <option value="{{ $item->id }}">{{ $item->no_rek }} - {{ $item->nama_bank }} (a.n. {{ $item->atas_nama }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">*Pilih jika ingin menggunakan rekening tujuan lain. Kosongkan untuk menggunakan rekening Anda.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="keterangan">Keterangan / Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Contoh: Penggantian peralatan jaringan cabang" required>{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-end">
                <button type="reset" class="btn btn-outline-secondary mr-2">
                    <i class="fas fa-undo mr-1"></i> Reset Input
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold px-4">
                    <i class="fas fa-cart-plus mr-1"></i> Tambahkan ke Daftar Pengajuan
                </button>
            </div>
        </form>
    </div>

    {{-- CARD 2: TABEL DAFTAR ITEM SEMENTARA & TOMBOL AJUKAN --}}
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                Daftar Peralatan yang Akan Diajukan
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Jenis Peralatan</th>
                            <th>Lokasi (AP / Cabang)</th>
                            <th>Merk & Tipe</th>
                            <th>Toko & No. Telp</th>
                            <th>Pengguna</th>
                            <th class="text-center" style="width: 70px;">Qty</th>
                            <th class="text-right">Harga Satuan</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-center" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @forelse ($temp_pengajuan as $item)
                            @php
                                $subtotal = $item->harga * $item->kuantitas;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle font-weight-bold">
                                    {{ $item->jenis_peralatan?->nama_jenis ?? '-' }}
                                    @if ($item->keterangan)
                                        <br><small class="text-muted font-weight-normal"><i class="far fa-comment-dots mr-1"></i>{{ $item->keterangan }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-info">{{ $item->ap?->unit ?? ($item->area?->area ?? $item->kode_branch) }}</span>
                                </td>
                                <td class="align-middle">{{ $item->merk }} - {{ $item->type }}</td>
                                <td class="align-middle">
                                    {{ $item->nama_toko }}
                                    @if ($item->no_telp)
                                        <br><small class="text-muted"><i class="fas fa-phone-alt mr-1"></i>{{ $item->no_telp }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item->pengguna }}</td>
                                <td class="text-center align-middle font-weight-bold">{{ $item->kuantitas }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold text-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    <a href="{{ url('it/temp-pengajuan/' . $item->id) }}"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus dari daftar"
                                        onclick="return confirm('Hapus item ini dari daftar pengajuan?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                    Belum ada item peralatan di dalam daftar pengajuan.<br>
                                    <small>Silakan lengkapi form di atas lalu klik tombol <strong>"Tambahkan ke Daftar Pengajuan"</strong>.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($temp_pengajuan->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="8" class="text-right">Total Estimasi Pengajuan:</th>
                                <th class="text-right text-success h6 font-weight-bold mb-0">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('it.pengajuan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pengajuan
            </a>

            <form action="{{ url('it/pengajuan') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-success font-weight-bold px-4" {{ $temp_pengajuan->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane mr-1"></i> Simpan & Ajukan Sekarang ({{ $temp_pengajuan->count() }} Item)
                </button>
            </form>
        </div>
    </div>

    @push('js')
        <script type="text/javascript">
            var allBranches = @json($branch);

            $(document).ready(function() {
                $('.select2bs4').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });

                function updateHargaPreview() {
                    var harga = parseFloat($('#harga').val()) || 0;
                    var kuantitas = parseInt($('#kuantitas').val()) || 1;

                    if (harga > 0) {
                        $('#preview_harga').text('Rp ' + harga.toLocaleString('id-ID') + ' / unit');
                        var total = harga * kuantitas;
                        $('#preview_total_harga').text('Subtotal: Rp ' + total.toLocaleString('id-ID')).show();
                    } else {
                        $('#preview_harga').text('Rp 0');
                        $('#preview_total_harga').hide();
                    }
                }

                $('#harga, #kuantitas').on('input keyup change', updateHargaPreview);

                $('#kode_area').on('change', function() {
                    var selectedArea = $(this).val();
                    var $apSelect = $('#kode_branch');

                    $apSelect.empty();
                    $apSelect.append('<option value="">-- Pilih Unit / AP --</option>');

                    if (selectedArea) {
                        var filteredBranches = allBranches.filter(function(branch) {
                            return branch.code_area == selectedArea;
                        });

                        $.each(filteredBranches, function(index, item) {
                            $apSelect.append('<option value="' + item.kode_branch + '">' + item.unit + '</option>');
                        });
                    }

                    $apSelect.trigger('change');
                });

                $('#nama_peralatan').on('change', function() {
                    var jenis_peralatan_id = $(this).val();
                    var $jenisSelect = $('.jenis_peralatan');
                    $jenisSelect.empty();

                    if (jenis_peralatan_id) {
                        $.ajax({
                            url: "{{ url('jenis-peralatan-dropdown') }}/" + jenis_peralatan_id,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                $jenisSelect.append('<option value="">-- Pilih Jenis Peralatan --</option>');
                                if (data) {
                                    $.each(data, function(key, jenis_peralatan) {
                                        $jenisSelect.append('<option value="' + jenis_peralatan.id + '">' + jenis_peralatan.nama_jenis + '</option>');
                                    });
                                }
                                $jenisSelect.trigger('change');
                            }
                        });
                    } else {
                        $jenisSelect.append('<option value="">-- Pilih Kategori Dulu --</option>');
                        $jenisSelect.trigger('change');
                    }
                });
            });
        </script>
    @endpush
@endsection
