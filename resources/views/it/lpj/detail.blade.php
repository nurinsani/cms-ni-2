@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center d-print-none">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">
                {{ $title }}
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('it.lpj.index') }}">LPJ</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @push('css')
        <style>
            @media print {
                .d-print-none {
                    display: none !important;
                }

                .d-print-flex {
                    display: flex !important;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin: 0.4in;
                }
            }
        </style>
    @endpush

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header mt-4 d-print-flex justify-content-between d-none">
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg"
                width="150px" alt="Logo">
            <div class="text-center">
                <h4 class="font-weight-bold mb-1">LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                <h5 class="mb-0">TANGGAL {{ $lpj->tanggal_pengajuan?->format('d-M-Y') ?? $lpj->tanggal_pengajuan }}</h5>
            </div>
            <div></div>
        </div>

        <div class="card-header d-print-none">
            <div class="card-tools">
                <a href="{{ route('it.lpj.index') }}" class="btn btn-sm btn-secondary mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                @if ($lpj->status_approve == 'lpj-admin')
                    <a href="#" onclick="bukti_pengembalian({{ $lpj->id }})" class="btn btn-sm btn-info mr-1">
                        <i class="fas fa-upload mr-1"></i> Upload Bukti Pengembalian
                    </a>
                    <form id="form-ajukan-lpj" action="{{ route('it.ajukan_lpj', $lpj->id) }}" method="POST"
                        class="d-inline">
                        @csrf
                        <button type="button" id="btn-ajukan-lpj" class="btn btn-sm btn-success font-weight-bold">
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan LPJ
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($lpj->nominal_pengembalian)
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-check-circle mr-1"></i> <strong>Bukti Pengembalian</strong> sudah diunggah (Nominal: Rp
                    {{ number_format($lpj->nominal_pengembalian, 0, ',', '.') }}).
                </div>
            @endif

            <div class="row mb-3 p-3 bg-light rounded border mx-0">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Nomor Surat</td>
                            <td style="width: 15px;">:</td>
                            <td class="font-weight-bold">{{ $lpj->nomor }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Kepada</td>
                            <td>:</td>
                            <td>Ketua dan Pengurus KSPPS Nur Insani</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width: 130px;" class="text-muted font-weight-bold">Dari (Pemohon)</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $lpj->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ $lpj->tanggal_pengajuan?->format('d-M-Y') ?? $lpj->tanggal_pengajuan }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>AP / Cabang</th>
                            <th>COA</th>
                            <th>Nama Jenis Peralatan</th>
                            <th>Merk / Tipe</th>
                            <th class="text-right">Nominal Pengajuan</th>
                            <th class="text-right">Realisasi Pembelian</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lpj->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}
                                </td>
                                <td class="align-middle">{{ $item->merk }} {{ $item->type }}</td>
                                <td class="text-right align-middle">Rp
                                    {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp
                                    {{ number_format($item->harga_pembelian ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->status_lpj == 'belum')
                                        <button type="button"
                                            onclick="buat_lpj({{ $item->id }}, {{ $item->harga_pembelian ? $item->harga_pembelian : 'null' }})"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit mr-1"></i> Buat LPJ
                                        </button>
                                    @else
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Uang Muka (Subtotal)</strong></td>
                            <td colspan="2" class="text-right"><strong>Rp
                                    {{ number_format($lpj->subtotal(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Realisasi Pembelian</strong></td>
                            <td colspan="2" class="text-right"><strong>Rp
                                    {{ number_format($lpj->total_pembelian(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Selisih (Sisa / Kurang)</strong></td>
                            <td colspan="2" class="text-right">
                                <strong>Rp
                                    {{ number_format($lpj->subtotal() - $lpj->total_pembelian(), 0, ',', '.') }}</strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modal Upload LPJ Item -->
            <div class="modal fade" id="modal_lpj">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-file-upload mr-1"></i> Input Realisasi LPJ</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('it.lpj.update', $lpj->id) }}" method="post"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="pengajuan_detail_id" name="pengajuan_detail_id">

                                <div class="form-group">
                                    <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control @error('tanggal_perolehan') is-invalid @enderror"
                                        name="tanggal_perolehan" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}"
                                        required>
                                    @error('tanggal_perolehan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Harga Realisasi Pembelian (Rp) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number"
                                            class="form-control @error('harga_pembelian') is-invalid @enderror"
                                            id="harga_pembelian" name="harga_pembelian"
                                            value="{{ old('harga_pembelian') }}"
                                            placeholder="Masukkan total harga pembelian" required>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge badge-light border text-dark" id="preview_harga_pembelian">Rp
                                            0</span>
                                    </div>
                                    @error('harga_pembelian')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label class="form-label">Struk Pembelian <span class="text-danger">*</span></label>
                                    <input type="file"
                                        class="form-control-file border rounded p-1 @error('struk_pembelian') is-invalid @enderror"
                                        id="struk_pembelian"
                                        name="struk_pembelian"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i>Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                    @error('struk_pembelian')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan LPJ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Bukti Pengembalian -->
            <div class="modal fade" id="bukti_pengembalian">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="fas fa-upload mr-1"></i> Upload Bukti Pengembalian</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if ($lpj->nominal_pengembalian == false)
                                <form action="{{ url('it/lpj/upload-bukti-pengembalian', $lpj->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Nominal Pengembalian (Rp) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number"
                                                class="form-control @error('nominal_pengembalian') is-invalid @enderror"
                                                id="nominal_pengembalian" name="nominal_pengembalian"
                                                value="{{ old('nominal_pengembalian') }}"
                                                placeholder="Masukkan nominal pengembalian" required>
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge badge-light border text-dark"
                                                id="preview_nominal_pengembalian">Rp 0</span>
                                        </div>
                                        @error('nominal_pengembalian')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Bukti Pengembalian <span class="text-danger">*</span></label>
                                        <input type="file"
                                            class="form-control-file border rounded p-1 @error('bukti_pengembalian') is-invalid @enderror"
                                            id="bukti_pengembalian_file"
                                            name="bukti_pengembalian"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required>
                                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i>Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                        @error('bukti_pengembalian')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text"
                                            class="form-control @error('keterangan_lpj') is-invalid @enderror"
                                            name="keterangan_lpj" value="{{ old('keterangan_lpj') }}">
                                        @error('keterangan_lpj')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="modal-footer px-0 pb-0 mt-3">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-info">Simpan</button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle mr-1"></i> Bukti Pengembalian sudah diunggah!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function updateHargaPembelianPreview() {
            var val = parseFloat($('#harga_pembelian').val()) || 0;
            if (val > 0) {
                $('#preview_harga_pembelian').text('Rp ' + val.toLocaleString('id-ID'));
            } else {
                $('#preview_harga_pembelian').text('Rp 0');
            }
        }

        $('#harga_pembelian').on('input keyup change', updateHargaPembelianPreview);

        function buat_lpj(pengajuan_detail_id, harga_pembelian) {
            let modal = $('#modal_lpj').modal('show');
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id);
            if (harga_pembelian) {
                modal.find('#harga_pembelian').val(harga_pembelian);
            }
            updateHargaPembelianPreview();
        }

        function updateNominalPengembalianPreview() {
            var val = parseFloat($('#nominal_pengembalian').val()) || 0;
            if (val > 0) {
                $('#preview_nominal_pengembalian').text('Rp ' + val.toLocaleString('id-ID'));
            } else {
                $('#preview_nominal_pengembalian').text('Rp 0');
            }
        }

        $('#nominal_pengembalian').on('input keyup change', updateNominalPengembalianPreview);

        function bukti_pengembalian() {
            $('#bukti_pengembalian').modal('show');
            updateNominalPengembalianPreview();
        }

        function validasiUkuranFile(input, namaField) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    var fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran File Terlalu Besar',
                            html: 'Ukuran ' + namaField + ' adalah <b>' + fileSizeMB + ' MB</b>.<br>Batas maksimal ukuran file yang diperbolehkan adalah <b>2 MB</b>.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Pilih File Lain'
                        });
                    } else {
                        alert('Ukuran ' + namaField + ' (' + fileSizeMB + ' MB) melebihi batas maksimal 2 MB.');
                    }
                    $(input).val('');
                    return false;
                }
            }
            return true;
        }

        $('#struk_pembelian').on('change', function() {
            validasiUkuranFile(this, 'Struk Pembelian');
        });

        $('#bukti_pengembalian_file').on('change', function() {
            validasiUkuranFile(this, 'Bukti Pengembalian');
        });

        $(document).ready(function() {
            updateHargaPembelianPreview();
            updateNominalPengembalianPreview();
        });

        @php
            $itemBelumCount = $lpj->pengajuan_details->where('status_lpj', '!=', 'sudah')->count();
            $selisihLebih = $lpj->subtotal() - $lpj->total_pembelian();
            $butuhPengembalian = $selisihLebih > 0 && (empty($lpj->bukti_pengembalian) || empty($lpj->nominal_pengembalian));
        @endphp

        $('#btn-ajukan-lpj').on('click', function(e) {
            e.preventDefault();

            var itemBelum = {{ $itemBelumCount }};
            var butuhPengembalian = {{ $butuhPengembalian ? 'true' : 'false' }};
            var sisaNominal = '{{ number_format($selisihLebih > 0 ? $selisihLebih : 0, 0, ',', '.') }}';

            if (itemBelum > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        html: 'Masih ada <b>' + itemBelum +
                            ' item</b> yang belum dibuatkan LPJ.<br>Silakan klik tombol <b>"Buat LPJ"</b> pada setiap item terlebih dahulu.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Masih ada ' + itemBelum +
                        ' item yang belum dibuatkan LPJ. Silakan selesaikan LPJ per item terlebih dahulu.');
                }
                return false;
            }

            if (butuhPengembalian) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bukti Pengembalian Belum Ada',
                        html: 'Terdapat sisa uang muka sebesar <b>Rp ' + sisaNominal +
                            '</b>.<br>Silakan klik tombol <b>"Upload Bukti Pengembalian"</b> terlebih dahulu sebelum mengajukan LPJ.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Terdapat sisa uang muka sebesar Rp ' + sisaNominal +
                        '. Harap upload bukti pengembalian terlebih dahulu.');
                }
                return false;
            }

            // Jika semua validasi lolos, tampilkan konfirmasi SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Pengajuan LPJ',
                    text: 'Apakah Anda yakin ingin mengajukan LPJ ini? Pastikan seluruh data realisasi dan bukti struk sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-paper-plane mr-1"></i> Ya, Ajukan Sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#form-ajukan-lpj').submit();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin mengajukan LPJ ini?')) {
                    $('#form-ajukan-lpj').submit();
                }
            }
        });
    </script>
@endpush
