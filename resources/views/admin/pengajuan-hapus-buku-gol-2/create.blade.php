@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan-hapus-buku-gol-2.index') }}">Hapus Buku Gol 2</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Periksa kembali isian Anda:</h6>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.pengajuan-hapus-buku-gol-2.store') }}" method="POST" enctype="multipart/form-data"
        id="formHapusBuku">
        @csrf

        <div class="row">
            <!-- Kolom Kiri: Form Informasi Pengajuan -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="card-title font-weight-bold mb-0">
                            Form Pengajuan Hapus Buku Gol 2
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- KP / Cabang / AP -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">
                                KP / Cabang / AP <span class="text-danger">*</span>
                            </label>
                            <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror"
                                name="kode_branch" id="kode_branch" required>
                                <option value="" hidden>-- Pilih KP / Cabang / AP --</option>
                                @foreach ($pilih_ap as $branch)
                                    <option value="{{ $branch->kode_branch }}"
                                        {{ old('kode_branch') == $branch->kode_branch ? 'selected' : '' }}>
                                        {{ $branch->kode_branch }} - {{ $branch->unit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kode_branch')
                                <small class="text-danger"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Bulan Hapus Buku -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">
                                Bulan Hapus Buku <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('bulan_hapus_buku') is-invalid @enderror"
                                name="bulan_hapus_buku" id="bulan_hapus_buku" required>
                                <option value="" hidden>-- Pilih Bulan --</option>
                                @foreach ($bulan_list as $bln)
                                    <option value="{{ $bln }}"
                                        {{ old('bulan_hapus_buku', date('F')) == $bln ? 'selected' : '' }}>
                                        {{ $bln }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bulan_hapus_buku')
                                <small class="text-danger"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Alasan Hapus Buku -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">
                                Alasan Hapus Buku <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('alasan') is-invalid @enderror" name="alasan" id="alasan" rows="3"
                                placeholder="Tuliskan alasan penghapusan buku (misal: HP rusak berat/mati total, layar pecah, hilang, dll)..." required>{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <small class="text-danger"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Lampiran Surat MOA / Dokumen Pengajuan (Wajib) -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">
                                Lampiran Dokumen / Surat MOA <span class="text-danger">*</span>
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('surat_moa') is-invalid @enderror"
                                    name="surat_moa" id="surat_moa" accept=".pdf,.jpg,.jpeg,.png" required>
                                <label class="custom-file-label" for="surat_moa">Pilih file...</label>
                            </div>
                            <small class="text-muted">Format: PDF atau Gambar (Maks 10MB)</small>
                            @error('surat_moa')
                                <small class="text-danger d-block"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</small>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary btn-block" id="btnSubmit">
                                <i class="fas fa-paper-plane mr-2"></i> Ajukan Hapus Buku Gol 2
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: List Inventaris Gol 2 yang Ada & Upload Foto Tiap Barang -->
            <div class="col-lg-7 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="card-title font-weight-bold text-dark mb-0">
                                Daftar Inventaris Gol 2
                            </h5>
                            <div>
                                <span class="badge badge-light border px-2 py-1 mr-1" id="totalItemsBadge">0 inventaris</span>
                                <span class="badge badge-primary px-3 py-2" id="selectedCountBadge">0 barang dipilih</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Search & Filter -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="searchInventaris"
                                    placeholder="Cari IMEI, merk, type (misal: Redmi 14C), nama pengguna, keterangan...">
                            </div>
                        </div>

                        <!-- State: Sebelum Pilih AP -->
                        <div id="stateEmpty" class="text-center py-5">
                            <i class="fas fa-mobile-alt fa-4x text-muted mb-3 opacity-50"></i>
                            <h6 class="font-weight-bold text-secondary">Pilih KP / Cabang / AP Terlebih Dahulu</h6>
                            <p class="text-muted small">Daftar inventaris Gol 2 akan muncul secara otomatis sesuai cabang yang dipilih.</p>
                        </div>

                        <!-- State: Loading -->
                        <div id="stateLoading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Memuat...</span>
                            </div>
                            <p class="text-muted mt-2 small">Mengambil data inventaris Gol 2...</p>
                        </div>

                        <!-- State: Data Kosong -->
                        <div id="stateNoData" class="text-center py-5 d-none">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                            <h6 class="text-secondary font-weight-bold">Tidak ada inventaris Gol 2 aktif pada cabang ini</h6>
                        </div>

                        <!-- State: Hasil Pencarian / Filter Kosong -->
                        <div id="stateNoMatch" class="text-center py-4 d-none">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">Tidak ada inventaris Gol 2 yang cocok dengan kata kunci pencarian</p>
                        </div>

                        <!-- Container List Inventaris -->
                        <div id="inventarisList" class="p-3" style="max-height: 580px; overflow-y: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .inv-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 12px;
            background-color: #ffffff;
            transition: all 0.2s ease-in-out;
        }

        .inv-card:hover {
            border-color: #007bff;
            background-color: #f8faff;
        }

        .inv-card.selected {
            border-color: #007bff;
            background-color: #f0f7ff;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.15);
        }

        .inv-checkbox {
            transform: scale(1.3);
            cursor: pointer;
        }

        .upload-foto-box {
            background: #ffffff;
            border: 1px dashed #007bff;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        .img-preview {
            max-height: 120px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            object-fit: cover;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let inventarisData = [];

            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }

            function formatRupiah(val) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(val || 0);
            }

            // Fungsi fetch inventaris gol 2 dari backend
            function loadInventaris(kodeBranch) {
                if (!kodeBranch) {
                    $('#stateEmpty').removeClass('d-none');
                    $('#stateLoading').addClass('d-none');
                    $('#stateNoData').addClass('d-none');
                    $('#inventarisList').empty();
                    $('#selectedCountBadge').text('0 barang dipilih');
                    $('#totalItemsBadge').text('0 inventaris');
                    return;
                }

                $('#stateEmpty').addClass('d-none');
                $('#stateNoData').addClass('d-none');
                $('#stateNoMatch').addClass('d-none');
                $('#stateLoading').removeClass('d-none');
                $('#inventarisList').empty();

                $.ajax({
                    url: "{{ url('admin/pengajuan-hapus-buku-gol-2/get-inventaris') }}/" + encodeURIComponent(kodeBranch),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#stateLoading').addClass('d-none');
                        inventarisData = response || [];

                        $('#totalItemsBadge').text(inventarisData.length + ' inventaris');
                        renderInventaris(inventarisData);
                    },
                    error: function(xhr) {
                        $('#stateLoading').addClass('d-none');
                        console.error('Error fetching inventaris gol 2:', xhr);
                        alert('Gagal mengambil data inventaris Gol 2. Pastikan server aktif.');
                    }
                });
            }

            // Render HTML kartu inventaris
            function renderInventaris(items) {
                let container = $('#inventarisList');
                container.empty();

                if (!items || items.length === 0) {
                    $('#stateNoData').removeClass('d-none');
                    return;
                }
                $('#stateNoData').addClass('d-none');

                items.forEach(function(item) {
                    let harga = formatRupiah(item.harga_perolehan);
                    let tgl = item.tanggal_perolehan ? item.tanggal_perolehan : '-';
                    let penggunaText = (item.pengguna ? item.pengguna : 'Tidak ada pengguna') + (item.jabatan ? ' - ' + item.jabatan : '');

                    let html = `
                <div class="inv-card" id="card_${item.id}" data-id="${item.id}" 
                     data-text="${((item.kode_peralatan || '') + ' ' + (item.imei || '') + ' ' + (item.jenis || '') + ' ' + (item.merk || '') + ' ' + (item.type || '') + ' ' + (item.pengguna || '') + ' ' + (item.keterangan || '')).toLowerCase()}">
                    <div class="d-flex align-items-start">
                        <div class="mr-3 pt-1">
                            <input type="checkbox" name="inventaris_id[]" value="${item.id}" 
                                   class="inv-checkbox" id="chk_${item.id}">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="chk_${item.id}" class="font-weight-bold text-primary mb-1 cursor-pointer" style="cursor: pointer;">
                                    ${item.type ? item.type : (item.jenis || 'Inventaris Gol 2')}
                                </label>
                                <div>
                                    ${item.imei ? `<span class="badge badge-secondary font-monospace">IMEI: ${item.imei}</span>` : ''}
                                </div>
                            </div>
                            <div class="small text-dark font-weight-bold">
                                ${item.merk && item.merk !== '-' ? item.merk + ' - ' : ''} ${item.keterangan || ''}
                            </div>
                            <div class="small text-muted mb-1">
                                <i class="fas fa-user mr-1"></i> Pengguna: <strong>${penggunaText}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small border-top pt-1 mt-1">
                                <span><i class="fas fa-calendar-alt mr-1"></i> Tgl Perolehan: ${tgl}</span>
                                <span>Harga Perolehan: <strong class="text-dark">${harga}</strong></span>
                            </div>

                            <!-- Area Upload Foto Kondisi Barang -->
                            <div class="upload-foto-box d-none mt-2" id="upload_box_${item.id}">
                                <label class="small font-weight-bold mb-1">
                                    <i class="fas fa-camera mr-1"></i> Upload Foto Kondisi: <code>${item.type || item.jenis} (${item.imei ? 'IMEI: ' + item.imei : 'ID: ' + item.id})</code> <span class="text-danger">*</span>
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-sm-8">
                                        <input type="file" name="foto_barang[${item.id}]" class="form-control-file form-control-sm foto-input" 
                                               id="foto_${item.id}" accept="image/*" data-id="${item.id}">
                                        <input type="text" name="kondisi_barang[${item.id}]" class="form-control form-control-sm mt-1" 
                                               placeholder="Keterangan kondisi (contoh: Mati total / Layar pecah)">
                                    </div>
                                    <div class="col-sm-4 text-center mt-2 mt-sm-0">
                                        <img id="preview_${item.id}" src="#" alt="Preview" class="img-preview d-none w-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    container.append(html);
                });

                applyFilter();
                updateSelectedCount();
            }

            // Filter Search Live
            function applyFilter() {
                let keyword = ($('#searchInventaris').val() || '').toLowerCase().trim();
                let cards = $('.inv-card');
                let visibleCount = 0;

                cards.each(function() {
                    let text = $(this).data('text');
                    let matchText = (keyword === '' || text.includes(keyword));

                    if (matchText) {
                        $(this).removeClass('d-none');
                        visibleCount++;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (visibleCount === 0 && cards.length > 0) {
                    $('#stateNoMatch').removeClass('d-none');
                } else {
                    $('#stateNoMatch').addClass('d-none');
                }
            }

            // Event saat branch berubah
            $('#kode_branch').on('change select2:select', function() {
                let val = $(this).val();
                loadInventaris(val);
            });

            // Live search
            $('#searchInventaris').on('input', function() {
                applyFilter();
            });

            // Toggle Checkbox & Upload Area
            $(document).on('change', '.inv-checkbox', function() {
                let id = $(this).val();
                let card = $(`#card_${id}`);
                let uploadBox = $(`#upload_box_${id}`);
                let fileInput = $(`#foto_${id}`);

                if ($(this).is(':checked')) {
                    card.addClass('selected');
                    uploadBox.removeClass('d-none');
                    fileInput.prop('required', true);
                } else {
                    card.removeClass('selected');
                    uploadBox.addClass('d-none');
                    fileInput.prop('required', false);
                    fileInput.val('');
                    $(`#preview_${id}`).addClass('d-none').attr('src', '#');
                }

                updateSelectedCount();
            });

            // Preview Foto Saat Diupload
            $(document).on('change', '.foto-input', function(e) {
                let id = $(this).data('id');
                let file = e.target.files[0];
                let preview = $(`#preview_${id}`);

                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(evt) {
                        preview.attr('src', evt.target.result).removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.attr('src', '#').addClass('d-none');
                }
            });

            function updateSelectedCount() {
                let count = $('.inv-checkbox:checked').length;
                $('#selectedCountBadge').text(count + ' barang dipilih');
            }

            // Submit Validation
            $('#formHapusBuku').on('submit', function(e) {
                let checked = $('.inv-checkbox:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    alert('Harap pilih minimal 1 inventaris Gol 2 yang akan dihapus buku!');
                    return false;
                }
                $('#btnSubmit').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Pengajuan...');
            });

            // AUTO-LOAD jika kode_branch sudah terpilih saat reload
            let initialBranch = $('#kode_branch').val();
            if (initialBranch) {
                loadInventaris(initialBranch);
            }
        });
    </script>
@endsection
