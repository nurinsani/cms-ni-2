@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Transaksi</li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Perhatian:</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Status Periode Berjalan --}}
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-calendar-alt text-primary mr-1"></i> Periode Aktif Gol 2: <span class="badge badge-primary px-3 py-1 font-size-14">{{ $periode }}</span>
                </h5>
                <div>
                    <span class="text-muted small"><i class="fas fa-info-circle mr-1"></i> Pastikan urutan proses dijalankan dari Langkah 1 hingga Langkah 3</span>
                </div>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row">
                {{-- Status Langkah 1 --}}
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="card border h-100 shadow-none mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Langkah 1</div>
                                    <div class="font-weight-bold text-dark mt-1">Penyusutan Aset Gol 2</div>
                                    <div class="small text-muted mt-1">
                                        @if ($isPenyusutanDone)
                                            <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> {{ $penyusutanCount }} aset disusutkan</span>
                                            <div class="mt-1">Total: Rp {{ number_format($penyusutanTotal, 0, ',', '.') }}</div>
                                        @else
                                            <span class="text-secondary"><i class="fas fa-clock mr-1"></i> Belum dijalankan</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if ($isPenyusutanDone)
                                        <span class="badge badge-success px-2 py-1">Selesai</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Belum</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Langkah 2 --}}
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="card border h-100 shadow-none mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Langkah 2</div>
                                    <div class="font-weight-bold text-dark mt-1">Jurnal Penyusutan Gol 2</div>
                                    <div class="small text-muted mt-1">
                                        @if ($isJurnalPenyusutanDone)
                                            <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Jurnal terbentuk</span>
                                            <div class="mt-1">KP: {{ $countJurnalPenyusutanKp }} entri | AP: {{ $countJurnalPenyusutanAp }} entri</div>
                                        @else
                                            <span class="text-secondary"><i class="fas fa-clock mr-1"></i> Belum dibuat</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if ($isPenyusutanUploaded)
                                        <span class="badge badge-info px-2 py-1">Uploaded</span>
                                    @elseif ($isJurnalPenyusutanDone)
                                        <span class="badge badge-success px-2 py-1">Dibuat</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Belum</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Langkah 3 --}}
                <div class="col-md-4">
                    <div class="card border h-100 shadow-none mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Langkah 3</div>
                                    <div class="font-weight-bold text-dark mt-1">Jurnal Pembebanan Gol 2</div>
                                    <div class="small text-muted mt-1">
                                        @if ($isJurnalPembebananDone)
                                            <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Jurnal terbentuk</span>
                                            <div class="mt-1">KP: {{ $countJurnalPembebananKp }} entri | AP: {{ $countJurnalPembebananAp }} entri</div>
                                        @else
                                            <span class="text-secondary"><i class="fas fa-clock mr-1"></i> Belum dibuat</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if ($isPembebananKpUploaded && $isPembebananApUploaded)
                                        <span class="badge badge-info px-2 py-1">Uploaded</span>
                                    @elseif ($isJurnalPembebananDone)
                                        <span class="badge badge-success px-2 py-1">Dibuat</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Belum</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Alur 3 Langkah --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title font-weight-bold text-dark mb-0">
                <i class="fas fa-tasks mr-2 text-primary"></i> Alur Proses Penyusutan & Pembebanan Golongan 2
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- STEP 1 --}}
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card h-100 {{ $isPenyusutanDone ? 'border-success' : 'border-primary' }}">
                        <div class="card-header {{ $isPenyusutanDone ? 'bg-success text-white' : 'bg-primary text-white' }} py-2 text-center">
                            <span class="font-weight-bold">1. Penyusutan Aset Gol 2</span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <p class="text-muted small">
                                Menghitung beban depresiasi bulanan inventaris golongan 2 yang berstatus aktif dan belum mencapai umur ekonomis (24 bulan).
                            </p>
                            <div>
                                @if ($isPenyusutanDone)
                                    <div class="alert alert-light border-success text-success small mb-3 py-2 text-center">
                                        <i class="fas fa-check-circle mr-1"></i> Penyusutan Gol 2 periode ini sudah selesai ({{ $penyusutanCount }} aset).
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-block font-weight-bold" disabled>
                                        <i class="fas fa-check mr-1"></i> Selesai
                                    </button>
                                @else
                                    <form action="{{ route('jalankanPenyusutanGol2') }}" method="POST" class="process-form">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-block font-weight-bold btn-process" data-title="Jalankan Penyusutan Gol 2">
                                            <i class="fas fa-play mr-1"></i> Jalankan Penyusutan Gol 2
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 2 --}}
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card h-100 {{ $isJurnalPenyusutanDone ? 'border-success' : 'border-secondary' }}">
                        <div class="card-header {{ $isJurnalPenyusutanDone ? 'bg-success text-white' : 'bg-secondary text-white' }} py-2 text-center">
                            <span class="font-weight-bold">2. Jurnal Penyusutan Gol 2</span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <p class="text-muted small">
                                Membentuk dan menyusun mutasi jurnal penyusutan inventaris Gol 2 untuk Kantor Pusat (KP) dan Kantor Cabang/Area (AP).
                            </p>
                            <div>
                                @if (!$isPenyusutanDone)
                                    <div class="alert alert-light border text-muted small mb-3 py-2 text-center">
                                        <i class="fas fa-lock mr-1"></i> Selesaikan Langkah 1 terlebih dahulu.
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-block" disabled>
                                        <i class="fas fa-lock mr-1"></i> Terkunci
                                    </button>
                                @elseif ($isPenyusutanUploaded)
                                    <div class="alert alert-light border-info text-info small mb-3 py-2 text-center">
                                        <i class="fas fa-cloud-upload-alt mr-1"></i> Jurnal sudah di-upload ke sistem NI.
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-block" disabled>
                                        <i class="fas fa-check mr-1"></i> Sudah Di-upload
                                    </button>
                                @else
                                    @if ($isJurnalPenyusutanDone)
                                        <div class="alert alert-light border-success text-success small mb-3 py-2 text-center">
                                            <i class="fas fa-check-circle mr-1"></i> Jurnal sudah terbentuk (siap di-upload).
                                        </div>
                                    @endif
                                    <form action="{{ route('jalankanJurnalPenyusutanGol2') }}" method="POST" class="process-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-block font-weight-bold btn-process" data-title="{{ $isJurnalPenyusutanDone ? 'Perbarui Jurnal Penyusutan Gol 2' : 'Jalankan Jurnal Penyusutan Gol 2' }}">
                                            <i class="fas fa-file-invoice mr-1"></i> {{ $isJurnalPenyusutanDone ? 'Perbarui Jurnal Penyusutan Gol 2' : 'Jalankan Jurnal Penyusutan Gol 2' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div class="col-lg-4">
                    <div class="card h-100 {{ $isJurnalPembebananDone ? 'border-success' : 'border-secondary' }}">
                        <div class="card-header {{ $isJurnalPembebananDone ? 'bg-success text-white' : 'bg-secondary text-white' }} py-2 text-center">
                            <span class="font-weight-bold">3. Jurnal Pembebanan Gol 2</span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <p class="text-muted small">
                                Mendistribusikan beban penyusutan Gol 2 ke cabang-cabang operasional sesuai dengan region/area terkait.
                            </p>
                            <div>
                                @if (!$isPenyusutanDone)
                                    <div class="alert alert-light border text-muted small mb-3 py-2 text-center">
                                        <i class="fas fa-lock mr-1"></i> Selesaikan Langkah 1 terlebih dahulu.
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-block" disabled>
                                        <i class="fas fa-lock mr-1"></i> Terkunci
                                    </button>
                                @elseif ($isJurnalPembebananDone)
                                    <div class="alert alert-light border-success text-success small mb-3 py-2 text-center">
                                        <i class="fas fa-check-circle mr-1"></i> Jurnal pembebanan Gol 2 sudah diproses.
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-block font-weight-bold" disabled>
                                        <i class="fas fa-check mr-1"></i> Selesai
                                    </button>
                                @else
                                    <form action="{{ route('pembebanan-gol-2.proses') }}" method="POST" class="process-form">
                                        @csrf
                                        <button type="submit" class="btn btn-warning text-white btn-block font-weight-bold btn-process" data-title="Jalankan Jurnal Pembebanan Gol 2">
                                            <i class="fas fa-bolt mr-1"></i> Jalankan Jurnal Pembebanan Gol 2
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Upload API Sistem NI --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title font-weight-bold text-dark mb-0">
                <i class="fas fa-cloud-upload-alt mr-2 text-info"></i> Upload Jurnal Transaksi Gol 2 ke Sistem NI
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-4">
                Klik tombol di bawah ini untuk mengirimkan transaksi jurnal Gol 2 yang telah dibuat ke API Sistem NI.
            </p>

            <div class="row">
                {{-- Upload Penyusutan --}}
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="p-3 border rounded bg-light text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="font-weight-bold text-dark mb-1">Jurnal Penyusutan Gol 2</div>
                            <div class="text-muted small mb-3">Upload jurnal penyusutan KP & AP Gol 2 ke Sistem NI</div>
                        </div>
                        <div>
                            @if (!$isJurnalPenyusutanDone)
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="fas fa-times-circle mr-1"></i> Jurnal Belum Dibuat
                                </button>
                            @elseif ($isPenyusutanUploaded)
                                <button type="button" class="btn btn-success btn-block" disabled>
                                    <i class="fas fa-check-double mr-1"></i> Sudah Di-upload ke NI
                                </button>
                            @else
                                <button type="button" class="btn btn-primary btn-block btn-upload" id="penyusutanUploadGol2" data-name="Jurnal Penyusutan Gol 2">
                                    <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Jurnal Penyusutan Gol 2
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Upload Pembebanan KP --}}
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="p-3 border rounded bg-light text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="font-weight-bold text-dark mb-1">Jurnal Pembebanan KP Gol 2</div>
                            <div class="text-muted small mb-3">Upload jurnal pembebanan Kantor Pusat Gol 2 ke Sistem NI</div>
                        </div>
                        <div>
                            @if (!$isJurnalPembebananDone)
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="fas fa-times-circle mr-1"></i> Jurnal Belum Dibuat
                                </button>
                            @elseif ($isPembebananKpUploaded)
                                <button type="button" class="btn btn-success btn-block" disabled>
                                    <i class="fas fa-check-double mr-1"></i> Sudah Di-upload ke NI
                                </button>
                            @else
                                <button type="button" class="btn btn-info btn-block btn-upload" id="pembebananKpGol2" data-name="Jurnal Pembebanan KP Gol 2">
                                    <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Pembebanan KP Gol 2
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Upload Pembebanan AP --}}
                <div class="col-md-4">
                    <div class="p-3 border rounded bg-light text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="font-weight-bold text-dark mb-1">Jurnal Pembebanan AP Gol 2</div>
                            <div class="text-muted small mb-3">Upload jurnal pembebanan Cabang/Area Gol 2 ke Sistem NI</div>
                        </div>
                        <div>
                            @if (!$isJurnalPembebananDone)
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled>
                                    <i class="fas fa-times-circle mr-1"></i> Jurnal Belum Dibuat
                                </button>
                            @elseif ($isPembebananApUploaded)
                                <button type="button" class="btn btn-success btn-block" disabled>
                                    <i class="fas fa-check-double mr-1"></i> Sudah Di-upload ke NI
                                </button>
                            @else
                                <button type="button" class="btn btn-warning text-white btn-block btn-upload" id="pembebananApGol2" data-name="Jurnal Pembebanan AP Gol 2">
                                    <i class="fas fa-cloud-upload-alt mr-1"></i> Upload Pembebanan AP Gol 2
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        let isProcessing = false;

        // Anti Double-Click & Konfirmasi untuk Form Proses (Langkah 1, 2, 3)
        $('.process-form').on('submit', function (e) {
            e.preventDefault();

            if (isProcessing) {
                return false;
            }

            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            const actionTitle = submitBtn.data('title') || 'proses ini';

            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: `Apakah Anda yakin ingin menjalankan ${actionTitle} untuk periode {{ $periode }}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Jalankan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    isProcessing = true;

                    // Disable semua tombol proses & upload agar tidak terjadi klik ganda
                    $('.btn-process, .btn-upload').prop('disabled', true);
                    submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Mohon tunggu, proses sedang dijalankan. Jangan menutup halaman ini.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form asli
                    form.submit();
                }
            });
        });

        // AJAX Upload Handler Helper dengan Proteksi Klik Ganda
        function handleAjaxUpload(triggerBtn, routeUrl, titleName) {
            if (isProcessing) {
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Upload',
                text: `Apakah Anda yakin ingin mengupload ${titleName} ke Sistem NI?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Upload Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    isProcessing = true;
                    $('.btn-process, .btn-upload').prop('disabled', true);

                    const originalText = $(triggerBtn).html();
                    $(triggerBtn).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengupload...');

                    Swal.fire({
                        title: 'Proses Upload Sedang Berjalan',
                        text: 'Mohon tunggu hingga proses komunikasi dengan Sistem NI selesai...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: routeUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `${titleName} berhasil di-upload ke Sistem NI!`
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            isProcessing = false;
                            $('.btn-process, .btn-upload').prop('disabled', false);
                            $(triggerBtn).html(originalText);

                            let errorMsg = 'Terjadi kesalahan saat upload data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                errorMsg = xhr.responseText.substring(0, 300);
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Upload!',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        }

        // Event listener Upload
        $('#penyusutanUploadGol2').on('click', function (e) {
            e.preventDefault();
            handleAjaxUpload(this, "{{ route('penyusutan.upload-api-gol-2') }}", 'Jurnal Penyusutan Gol 2');
        });

        $('#pembebananKpGol2').on('click', function (e) {
            e.preventDefault();
            handleAjaxUpload(this, "{{ route('pembebanan.upload-api-kp-gol-2') }}", 'Jurnal Pembebanan KP Gol 2');
        });

        $('#pembebananApGol2').on('click', function (e) {
            e.preventDefault();
            handleAjaxUpload(this, "{{ route('pembebanan.upload-api-ap-gol-2') }}", 'Jurnal Pembebanan AP Gol 2');
        });
    });
</script>
@endsection
