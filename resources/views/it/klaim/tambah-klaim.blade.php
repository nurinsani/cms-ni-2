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
                <li class="breadcrumb-item"><a href="{{ url('it/klaim') }}">Klaim</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    {{-- TABEL DAFTAR ITEM KLAIM SEMENTARA & TOMBOL TAMBAH DENGAN MODAL --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                Daftar Item Klaim Peralatan
            </h3>
            <div class="card-tools ml-auto">
                <button type="button" class="btn btn-sm btn-primary shadow-sm mr-1" data-toggle="modal" data-target="#modal-tambah-klaim">
                    <i class="fas fa-plus mr-1"></i> Tambah Klaim
                </button>
                <a href="{{ url('it/klaim') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
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
                                <td class="align-middle font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</td>
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
                                        onclick="return confirm('Hapus item ini dari daftar klaim?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block text-secondary"></i>
                                    Belum ada item di dalam daftar klaim.<br>
                                    <small>Silakan klik tombol <strong>"+ Tambah Klaim"</strong> di atas untuk memasukkan item baru.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($temp_pengajuan->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="8" class="text-right">Total Nilai Klaim:</th>
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

        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ url('it/klaim') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Klaim
                </a>

                <form action="{{ url('it/klaim') }}" method="post" class="m-0 ml-auto">
                    @csrf
                    <button type="submit" class="btn btn-success font-weight-bold px-4" {{ $temp_pengajuan->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-1"></i> Simpan & Ajukan Klaim
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH KLAIM PERALATAN --}}
    <div class="modal fade" id="modal-tambah-klaim" tabindex="-1" role="dialog" aria-labelledby="modalTambahKlaimLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTambahKlaimLabel">
                        Form Input Klaim Peralatan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="form-tambah-klaim" action="{{ url('it/temp-pengajuan') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div id="modal-alert-error" class="alert alert-danger d-none shadow-sm"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm">
                                <strong>Gagal menambahkan item:</strong>
                                <ul class="mb-0 mt-1 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nama_peralatan">Kategori Peralatan <span class="text-danger">*</span></label>
                                    <select id="nama_peralatan" class="form-control select2-modal" required>
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
                                    <select name="jenis_peralatan_id" id="nama_jenis" class="form-control jenis_peralatan select2-modal" required>
                                        <option value="">-- Pilih Kategori Dulu --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kode_area">Cabang / Area <span class="text-danger">*</span></label>
                                    <select name="kode_area" id="kode_area" class="form-control select2-modal" required>
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
                                    <select name="kode_branch" id="kode_branch" class="form-control select2-modal" required>
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
                                    <label for="harga">Harga Perolehan Satuan (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="harga" id="harga" min="0" class="form-control" value="{{ old('harga') }}" placeholder="Masukkan harga pembelian" required>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge badge-light border text-dark" id="preview_harga">Rp 0</span>
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
                                    <label for="tanggal_pembelian">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="struk_pembelian">Upload Struk Pembelian <span class="text-danger">*</span></label>
                                    <input type="file" name="struk_pembelian" id="struk_pembelian" class="form-control-file border rounded p-1 bg-light" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <small class="text-muted">Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="rekening_id">Rekening Tujuan Klaim <span class="text-muted font-weight-normal">(Opsional)</span></label>
                                    <select name="rekening_id" id="rekening_id" class="form-control select2-modal" style="width: 100%;">
                                        <option value="">-- Gunakan Rekening Pembuat ({{ auth()->user()->nama_bank }} - {{ auth()->user()->no_rek }}) --</option>
                                        @foreach ($rekening as $item)
                                            <option value="{{ $item->id }}">{{ $item->no_rek }} - {{ $item->nama_bank }} (a.n. {{ $item->atas_nama }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="keterangan">Keterangan Klaim</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan klaim (opsional)">{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <div>
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fas fa-plus-circle mr-1"></i> Tambahkan ke Daftar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script type="text/javascript">
            var allBranches = @json($branch);

            $(document).ready(function() {
                // Inisialisasi Select2 di dalam Modal agar search bar dan dropdown berfungsi dengan baik
                $('#modal-tambah-klaim').on('shown.bs.modal', function () {
                    $('.select2-modal').select2({
                        theme: 'bootstrap4',
                        dropdownParent: $('#modal-tambah-klaim'),
                        width: '100%'
                    });
                });

                function updateHargaPreview() {
                    var harga = parseFloat($('#harga').val()) || 0;

                    if (harga > 0) {
                        $('#preview_harga').text('Rp ' + harga.toLocaleString('id-ID'));
                    } else {
                        $('#preview_harga').text('Rp 0');
                    }
                }

                $('#harga').on('input keyup change', updateHargaPreview);

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

                $('#modal-tambah-klaim').on('hidden.bs.modal', function () {
                    $('#modal-alert-error').addClass('d-none').empty();
                    $('#preview_harga').text('Rp 0');
                });

                @if ($errors->any())
                    $('#modal-tambah-klaim').modal('show');
                @endif

                // Submit Form via AJAX agar modal tidak menutup saat validasi error
                $('#form-tambah-klaim').on('submit', function(e) {
                    e.preventDefault();
                    var form = this;
                    var formData = new FormData(form);
                    var $submitBtn = $(form).find('button[type="submit"]');
                    var originalBtnHtml = $submitBtn.html();

                    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
                    $('#modal-alert-error').addClass('d-none').empty();

                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Item berhasil ditambahkan ke daftar klaim.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                window.location.reload();
                            }
                        },
                        error: function(xhr) {
                            $submitBtn.prop('disabled', false).html(originalBtnHtml);
                            var errorListHtml = '<ul class="mb-0 pl-3">';
                            var errorMessages = [];

                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, val) {
                                    $.each(val, function(i, msg) {
                                        errorMessages.push(msg);
                                        errorListHtml += '<li>' + msg + '</li>';
                                    });
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessages.push(xhr.responseJSON.message);
                                errorListHtml += '<li>' + xhr.responseJSON.message + '</li>';
                            } else {
                                var genericMsg = 'Terjadi kesalahan saat menambahkan item.';
                                errorMessages.push(genericMsg);
                                errorListHtml += '<li>' + genericMsg + '</li>';
                            }
                            errorListHtml += '</ul>';

                            // Tampilkan alert di dalam modal (modal tetap terbuka)
                            $('#modal-alert-error').html('<strong>Gagal menambahkan item:</strong>' + errorListHtml).removeClass('d-none');

                            // Tampilkan SweetAlert tanpa menutup modal
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menambahkan Item',
                                    html: errorListHtml
                                });
                            }
                        },
                        complete: function(xhr, status) {
                            if (status !== 'success') {
                                $submitBtn.prop('disabled', false).html(originalBtnHtml);
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
