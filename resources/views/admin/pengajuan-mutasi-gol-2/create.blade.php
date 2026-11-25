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
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@push('css')
<style>
    .inventaris-item {
        transition: all 0.2s ease;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .inventaris-item:hover {
        background-color: #f8f9fa;
        border-color: #007bff;
        transform: translateX(4px);
    }

    .inventaris-item.selected {
        background-color: #e7f3ff;
        border-color: #007bff;
    }

    .inventaris-item .form-check-input {
        cursor: pointer;
        width: 20px;
        height: 20px;
    }

    #inventarisList::-webkit-scrollbar {
        width: 8px;
    }

    #inventarisList::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #inventarisList::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #inventarisList::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush

@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5><i class="icon fas fa-ban"></i> Validasi Gagal!</h5>
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

<form id="form-pengajuan-mutasi" action="{{ route('admin.pengajuan-mutasi-gol-2.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 font-weight-bold">
                        Informasi Mutasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-2">
                            AP Asal <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2bs4 @error('dari_cabang') is-invalid @enderror" 
                                name="dari_cabang" id="kode_branch">
                            <option value="" hidden>-- Pilih AP Asal --</option>
                            @foreach ($pilih_ap as $data)
                                <option value="{{ $data->kode_branch }}" {{ old('dari_cabang') == $data->kode_branch ? 'selected' : '' }}>
                                    {{ $data->unit }} ({{ $data->kode_branch }})
                                </option>
                            @endforeach
                        </select>
                        @error('dari_cabang')
                            <small class="text-danger d-block mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold mb-2">
                            AP Tujuan <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2bs4 @error('ke_cabang') is-invalid @enderror" 
                                name="ke_cabang" id="ke_cabang">
                            <option value="" hidden>-- Pilih AP Tujuan --</option>
                            @foreach ($mutasi_ke as $data)
                                <option value="{{ $data->kode_branch }}" {{ old('ke_cabang') == $data->kode_branch ? 'selected' : '' }}>
                                    {{ $data->unit }} ({{ $data->kode_branch }})
                                </option>
                            @endforeach
                        </select>
                        @error('ke_cabang')
                            <small class="text-danger d-block mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold mb-2">
                            Bulan Mutasi <span class="text-danger">*</span>
                        </label>
                        <select class="form-control select2bs4 @error('bulan_mutasi') is-invalid @enderror" 
                                name="bulan_mutasi" id="bulan_mutasi">
                            <option value="" hidden>-- Pilih Bulan --</option>
                            @foreach (["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] as $bulan)
                                <option value="{{ $bulan }}" {{ old('bulan_mutasi') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                        @error('bulan_mutasi')
                            <small class="text-danger d-block mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-primary btn-block py-2">
                            <i class="fas fa-save mr-2"></i> Simpan Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 font-weight-bold">
                            Pilih Inventaris Gol 2 <span class="text-danger">*</span>
                        </h5>
                        <span class="badge badge-primary px-2 py-1" id="selectedCount">0 dipilih</span>
                    </div>
                </div>
                
                @error('peralatan_id')
                    <div class="alert alert-danger mx-3 mt-3 mb-0 py-2">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </div>
                @enderror

                <div class="card-body p-0">
                    <!-- Search Box -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" 
                                   id="searchInventaris" 
                                   placeholder="Cari kode, jenis, atau keterangan inventaris...">
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-muted mt-3 mb-0">Memuat data inventaris...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Pilih AP terlebih dahulu</h5>
                        <p class="text-muted small mb-0">Data inventaris akan muncul setelah Anda memilih AP Asal</p>
                    </div>

                    <!-- No Results State -->
                    <div id="noResultsState" class="text-center py-5 d-none">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted" id="noResultsTitle">Tidak ada hasil</h5>
                        <p class="text-muted small mb-0" id="noResultsDesc">Coba gunakan kata kunci lain</p>
                    </div>

                    <!-- Inventaris List -->
                    <div id="inventarisList" class="p-3" style="max-height: 500px; overflow-y: auto;"></div>

                    <!-- Select All -->
                    <div id="selectAllContainer" class="p-3 border-top bg-light d-none">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="selectAll">
                            <label class="custom-control-label font-weight-bold" for="selectAll">
                                Pilih Semua Inventaris
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        let inventarisData = [];
        let oldSelected = @json(array_map('strval', (array) old('peralatan_id', [])));

        // Inisialisasi select2 jika ada
        if ($.fn.select2) {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        }

        // Fungsi format rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka || 0);
        }

        // Fungsi render inventaris
        function renderInventaris(data) {
            $('#inventarisList').empty();
            
            if (!data || data.length === 0) {
                $('#noResultsTitle').text('Tidak ada data inventaris');
                $('#noResultsDesc').text('Tidak ada inventaris Gol 2 yang tersedia untuk dimutasi pada AP ini.');
                $('#noResultsState').removeClass('d-none');
                $('#selectAllContainer').addClass('d-none');
                updateSelectedCount();
                return;
            }

            $('#noResultsState').addClass('d-none');
            $('#selectAllContainer').removeClass('d-none');

            $.each(data, function(key, value) {
                let isChecked = oldSelected.includes(String(value.id));
                let itemHtml = `
                    <div class="inventaris-item ${isChecked ? 'selected' : ''}" data-id="${value.id}">
                        <div class="d-flex align-items-start">
                            <div class="custom-control custom-checkbox mr-3 mt-1">
                                <input class="custom-control-input inventaris-checkbox" 
                                       type="checkbox" 
                                       name="peralatan_id[]" 
                                       value="${value.id}" 
                                       id="inv_${value.id}"
                                       ${isChecked ? 'checked' : ''}>
                                <label class="custom-control-label" for="inv_${value.id}"></label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <label class="text-bold text-primary mb-0" style="cursor: pointer;" for="inv_${value.id}">
                                        ${value.kode_peralatan || '-'}
                                    </label>
                                </div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-tag mr-1"></i><strong> Jenis:</strong> ${value.jenis || '-'}
                                </div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-info-circle mr-1"></i><strong> Keterangan:</strong> ${value.keterangan || '-'}
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-money-bill-wave mr-1"></i><strong> Harga Perolehan:</strong> ${formatRupiah(value.harga_perolehan)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#inventarisList').append(itemHtml);
            });

            // Update status select all
            let totalItems = $('.inventaris-checkbox').length;
            let checkedItems = $('.inventaris-checkbox:checked').length;
            $('#selectAll').prop('checked', totalItems > 0 && totalItems === checkedItems);

            updateSelectedCount();
        }

        // Fungsi update counter terpilih
        function updateSelectedCount() {
            let count = $('.inventaris-checkbox:checked').length;
            $('#selectedCount').text(count + ' dipilih');
        }

        // Fungsi AJAX fetch inventaris Gol 2
        function fetchInventarisGol2(kodeBranch) {
            if (!kodeBranch) {
                $('#emptyState').removeClass('d-none');
                $('#loadingState').addClass('d-none');
                $('#inventarisList').empty();
                $('#noResultsState').addClass('d-none');
                $('#selectAllContainer').addClass('d-none');
                inventarisData = [];
                updateSelectedCount();
                return;
            }

            $('#emptyState').addClass('d-none');
            $('#loadingState').removeClass('d-none');
            $('#inventarisList').empty();
            $('#noResultsState').addClass('d-none');
            $('#selectAllContainer').addClass('d-none');

            $.ajax({
                url: "{{ url('get-inventaris-gol-2') }}/" + kodeBranch,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#loadingState').addClass('d-none');
                    inventarisData = Array.isArray(data) ? data : [];
                    renderInventaris(inventarisData);
                },
                error: function() {
                    $('#loadingState').addClass('d-none');
                    $('#noResultsTitle').text('Gagal memuat data');
                    $('#noResultsDesc').text('Terjadi kesalahan saat mengambil data inventaris Gol 2 dari server.');
                    $('#noResultsState').removeClass('d-none');
                }
            });
        }

        // Event: Pilih AP Asal
        $('#kode_branch').on('change', function() {
            var kodeBranch = $(this).val();
            fetchInventarisGol2(kodeBranch);
        });

        // Trigger otomatis jika sudah ada nilai old('dari_cabang')
        if ($('#kode_branch').val()) {
            fetchInventarisGol2($('#kode_branch').val());
        }

        // Event: Search filter
        $('#searchInventaris').on('keyup', function() {
            let searchTerm = $(this).val().toLowerCase().trim();
            
            if (inventarisData.length === 0) return;

            let filteredData = inventarisData.filter(function(item) {
                let kode = (item.kode_peralatan || '').toLowerCase();
                let jenis = (item.jenis || '').toLowerCase();
                let ket = (item.keterangan || '').toLowerCase();
                return kode.includes(searchTerm) || jenis.includes(searchTerm) || ket.includes(searchTerm);
            });

            if (filteredData.length === 0) {
                $('#inventarisList').empty();
                $('#noResultsTitle').text('Tidak ada hasil');
                $('#noResultsDesc').text('Tidak ada inventaris yang cocok dengan kata kunci "' + searchTerm + '"');
                $('#noResultsState').removeClass('d-none');
                $('#selectAllContainer').addClass('d-none');
            } else {
                renderInventaris(filteredData);
            }
        });

        // Event: Klik pada card inventaris (selain langsung klik input)
        $(document).on('click', '.inventaris-item', function(e) {
            if (!$(e.target).is('input[type="checkbox"]') && !$(e.target).is('label')) {
                let checkbox = $(this).find('.inventaris-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });

        // Event: Checkbox item berubah
        $(document).on('change', '.inventaris-checkbox', function() {
            let item = $(this).closest('.inventaris-item');
            let itemId = $(this).val();

            if ($(this).is(':checked')) {
                item.addClass('selected');
                if (!oldSelected.includes(itemId)) {
                    oldSelected.push(itemId);
                }
            } else {
                item.removeClass('selected');
                oldSelected = oldSelected.filter(id => id !== itemId);
                $('#selectAll').prop('checked', false);
            }

            let totalItems = $('.inventaris-checkbox').length;
            let checkedItems = $('.inventaris-checkbox:checked').length;
            $('#selectAll').prop('checked', totalItems > 0 && totalItems === checkedItems);

            updateSelectedCount();
        });

        // Event: Select All
        $('#selectAll').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('.inventaris-checkbox').prop('checked', isChecked);
            
            if (isChecked) {
                $('.inventaris-item').addClass('selected');
                $('.inventaris-checkbox').each(function() {
                    let val = $(this).val();
                    if (!oldSelected.includes(val)) {
                        oldSelected.push(val);
                    }
                });
            } else {
                $('.inventaris-item').removeClass('selected');
                $('.inventaris-checkbox').each(function() {
                    let val = $(this).val();
                    oldSelected = oldSelected.filter(id => id !== val);
                });
            }
            updateSelectedCount();
        });

        // Event: Validasi Form Submit
        $('#form-pengajuan-mutasi').on('submit', function(e) {
            let dariCabang = $('#kode_branch').val();
            let keCabang = $('#ke_cabang').val();
            let bulanMutasi = $('#bulan_mutasi').val();
            let checkedCount = $('.inventaris-checkbox:checked').length;

            if (!dariCabang) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'AP Asal Belum Dipilih',
                    text: 'Silakan pilih AP Asal terlebih dahulu!'
                });
                return false;
            }

            if (!keCabang) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'AP Tujuan Belum Dipilih',
                    text: 'Silakan pilih AP Tujuan terlebih dahulu!'
                });
                return false;
            }

            if (dariCabang === keCabang) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'AP Tujuan Tidak Valid',
                    text: 'AP Tujuan tidak boleh sama dengan AP Asal!'
                });
                return false;
            }

            if (!bulanMutasi) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Bulan Mutasi Belum Dipilih',
                    text: 'Silakan pilih Bulan Mutasi terlebih dahulu!'
                });
                return false;
            }

            if (checkedCount === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang Belum Dipilih',
                    text: 'Silakan pilih minimal 1 barang inventaris yang akan dimutasi!'
                });
                return false;
            }

            let $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
    });
</script>
@endpush
