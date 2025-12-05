@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

<form action="{{ route('admin.pengajuan-mutasi.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">
                        Informasi Mutasi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-2">
                            AP Asal
                        </label>
                        <select class="form-control select2bs4 @error('dari_cabang') is-invalid @enderror" 
                                name="dari_cabang" id="kode_branch">
                            <option value="" hidden>-- Pilih AP Asal --</option>
                            @foreach ($pilih_ap as $data)
                                <option value="{{ $data->kode_branch }}">{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('dari_cabang')
                            <small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold mb-2">
                            </i>AP Tujuan
                        </label>
                        <select class="form-control select2bs4 @error('ke_cabang') is-invalid @enderror" 
                                name="ke_cabang">
                            <option value="" hidden>-- Pilih AP Tujuan --</option>
                            @foreach ($mutasi_ke as $data)
                                <option value="{{ $data->kode_branch }}">{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('ke_cabang')
                            <small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="fw-bold mb-2">
                            Bulan Mutasi
                        </label>
                        <select class="form-control select2bs4 @error('bulan_mutasi') is-invalid @enderror" 
                                name="bulan_mutasi">
                            <option value="" hidden>-- Pilih Bulan --</option>
                            @foreach (["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] as $bulan)
                                <option value="{{ $bulan }}">{{ $bulan }}</option>
                            @endforeach
                        </select>
                        @error('bulan_mutasi')
                            <small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> Simpan Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Pilih Inventaris
                        </h5>
                        <span class="badge bg-light text-primary" id="selectedCount">0 dipilih</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Search Box -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" 
                                   id="searchInventaris" 
                                   placeholder="Cari kode, jenis, atau keterangan inventaris...">
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Memuat data inventaris...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Pilih AP terlebih dahulu</h5>
                        <p class="text-muted small">Data inventaris akan muncul setelah Anda memilih AP</p>
                    </div>

                    <!-- No Results State -->
                    <div id="noResultsState" class="text-center py-5 d-none">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada hasil</h5>
                        <p class="text-muted small">Coba gunakan kata kunci lain</p>
                    </div>

                    <!-- Inventaris List -->
                    <div id="inventarisList" class="p-3" style="max-height: 500px; overflow-y: auto;"></div>

                    <!-- Select All -->
                    <div id="selectAllContainer" class="p-3 border-top bg-light d-none">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">
                                Pilih Semua Inventaris
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .inventaris-item {
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .inventaris-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
        transform: translateX(5px);
    }

    .inventaris-item.selected {
        background-color: #e7f3ff;
        border-color: #0d6efd;
    }

    .inventaris-item .form-check-input {
        cursor: pointer;
        width: 20px;
        height: 20px;
    }

    .inventaris-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
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

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>

<script>
    $(document).ready(function() {
        let inventarisData = [];

        // Fungsi untuk format rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

        // Fungsi untuk render inventaris
        function renderInventaris(data) {
            $('#inventarisList').empty();
            
            if (data.length === 0) {
                $('#noResultsState').removeClass('d-none');
                $('#selectAllContainer').addClass('d-none');
                return;
            }

            $('#noResultsState').addClass('d-none');
            $('#selectAllContainer').removeClass('d-none');

            $.each(data, function(key, value) {
                let itemHtml = `
                    <div class="inventaris-item" data-id="${value.id}">
                        <div class="d-flex align-items-start">
                            <div class="form-check me-3">
                                <input class="form-check-input inventaris-checkbox" 
                                       type="checkbox" 
                                       name="peralatan_id[]" 
                                       value="${value.id}" 
                                       id="inv_${value.id}">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <label class="form-check-label text-bold text-primary mb-0" for="inv_${value.id}">
                                        ${value.kode_peralatan}
                                    </label>
                                    <span class="badge bg-success inventaris-badge">
                                        ${formatRupiah(value.harga_perolehan)}
                                    </span>
                                </div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-tag me-1"></i><strong>Jenis: </strong> ${value.jenis}
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i><strong>Keterangan:</strong> ${value.keterangan || '-'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#inventarisList').append(itemHtml);
            });

            updateSelectedCount();
        }

        // Fungsi untuk update jumlah item terpilih
        function updateSelectedCount() {
            let count = $('.inventaris-checkbox:checked').length;
            $('#selectedCount').text(count + ' dipilih');
        }

        // Event: Pilih AP
        $('#kode_branch').change(function() {
            var kodeBranch = $(this).val();
            
            if (kodeBranch) {
                // Tampilkan loading
                $('#emptyState').addClass('d-none');
                $('#loadingState').removeClass('d-none');
                $('#inventarisList').empty();
                $('#noResultsState').addClass('d-none');
                $('#selectAllContainer').addClass('d-none');

                $.ajax({
                    url: "{{ url('get-inventaris') }}/" + kodeBranch,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#loadingState').addClass('d-none');
                        inventarisData = data;
                        renderInventaris(data);
                    },
                    error: function() {
                        $('#loadingState').addClass('d-none');
                        $('#emptyState').removeClass('d-none');
                    }
                });
            } else {
                $('#emptyState').removeClass('d-none');
                $('#inventarisList').empty();
                $('#noResultsState').addClass('d-none');
                $('#selectAllContainer').addClass('d-none');
                inventarisData = [];
            }
        });

        // Event: Search
        $('#searchInventaris').on('keyup', function() {
            let searchTerm = $(this).val().toLowerCase();
            
            if (inventarisData.length === 0) return;

            let filteredData = inventarisData.filter(function(item) {
                return item.kode_peralatan.toLowerCase().includes(searchTerm) ||
                       item.jenis.toLowerCase().includes(searchTerm) ||
                       (item.keterangan && item.keterangan.toLowerCase().includes(searchTerm));
            });

            renderInventaris(filteredData);
        });

        // Event: Click pada item inventaris
        $(document).on('click', '.inventaris-item', function(e) {
            if (!$(e.target).is('input[type="checkbox"]')) {
                let checkbox = $(this).find('.inventaris-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                checkbox.trigger('change');
            }
        });

        // Event: Checkbox change
        $(document).on('change', '.inventaris-checkbox', function() {
            let item = $(this).closest('.inventaris-item');
            if ($(this).is(':checked')) {
                item.addClass('selected');
            } else {
                item.removeClass('selected');
                $('#selectAll').prop('checked', false);
            }
            updateSelectedCount();
        });

        // Event: Select All
        $('#selectAll').change(function() {
            let isChecked = $(this).is(':checked');
            $('.inventaris-checkbox').prop('checked', isChecked);
            
            if (isChecked) {
                $('.inventaris-item').addClass('selected');
            } else {
                $('.inventaris-item').removeClass('selected');
            }
            updateSelectedCount();
        });
    });
</script>
@endsection