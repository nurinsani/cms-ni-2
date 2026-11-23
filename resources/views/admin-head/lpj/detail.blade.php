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
    @push('css')
        <style>
            @media print {
                .d-print-none {
                    display: none;
                }

                .d-print-flex {
                    display: flex;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin-left: 0.5in;
                    margin-right: 0.5in;
                    margin-top: 0;
                    margin-bottom: 0;
                }
            }
        </style>
    @endpush

    <div class="row row-cols-1">
        <div class="col">
            <div class="card">
                <div class="card-header mt-5 d-print-flex justify-content-between d-none">
                    <img src="https://picsum.photos/200" width="150px" alt="">
                    <div class="text-center">
                        <h4>LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                        <h4>WILAYAH JATUT</h4>
                        <h4>PERIODE AGUSTUS 2023</h4>
                        <h4>TANGGAL 25-8-2023</h4>
                    </div>
                    <div class=""></div>
                </div>
                <div class="card-body">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <ul class="p-0" style="list-style: none">
                        <li>Nomor : {{ $lpj->nomor }}</li>
                        <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
                        <li>Dari : {{ $lpj->user->name }}</li>
                        <li>Tanggal : {{ $lpj->tanggal_pengajuan }}</li>
                    </ul>

                    <div class="table-responsive">
                        <form id="form-ajukan-lpj" action="{{ url('admin-head/ajukan_lpj', $lpj->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" id="btn-ajukan-lpj" class="btn btn-sm btn-success my-3 font-weight-bold">
                                <i class="fas fa-paper-plane mr-1"></i> Ajukan LPJ
                            </button>
                        </form>
                        @php
                            $tes = $lpj->subtotal() - $lpj->total_pembelian();
                        @endphp
                        <a href="#" onclick="bukti_pengembalian({{ $lpj->id }} )"
                            class="btn btn-sm btn-primary">Upload Bukti Pengembalian
                        </a>

                        <div class="">
                            @if ($lpj->nominal_pengembalian == true)
                                <div class="text-danger">*Bukti Pengembalian sudah di upload.</div>
                            @endif
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">AP</th>
                                    <th scope="col">COA</th>
                                    <th scope="col">Nama jenis Peralatan</th>
                                    <th scope="col">Merk/Type</th>
                                    <th scope="col">Pengajuan</th>
                                    <th scope="col">Harga Pembelian</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lpj->pengajuan_details as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->ap->unit ?? $item->area->area}}</td>
                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                        <td>{{ $item->merk }}</td>
                                        <td>Rp. {{ number_format($item->harga * $item->kuantitas) }}</td>
                                        <td>Rp. {{ number_format($item->harga_pembelian) }}</td>
                                        <td>
                                            @if ($item->status_lpj == 'belum')
                                                <a href="#" onclick="buat_lpj({{ $item->id }}, {{ $item->harga_pembelian ? $item->harga_pembelian : 'null' }})"
                                                    class="btn btn-sm btn-primary">Buat LPJ</a>
                                            @else
                                                <a href="#" onclick="buat_lpj({{ $item->id }}, {{ $item->harga_pembelian ? $item->harga_pembelian : 'null' }})"
                                                    class="btn btn-sm btn-success">
                                                    <i class="far fa-check-square"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><strong>Subtotal</strong></td>
                                    <td colspan="4" class="text-right"><strong>Rp.
                                            {{ number_format($lpj->subtotal()) }}</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <strong>Total pembelian</strong>
                                    </td>
                                    <td colspan="4" class="text-right">
                                        <strong>
                                            Rp. {{ number_format($lpj->total_pembelian()) }}
                                        </strong>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <strong>Total yang dibayarkan</strong>
                                    </td>
                                    <td colspan="4" class="text-right">
                                        <strong>
                                            Rp. {{ number_format($lpj->total_pembelian()) }}
                                        </strong>
                                    </td>
                                    <td></td>
                                </tr>
                                
                                @php
                                    $selisih = $lpj->subtotal() - $lpj->total_pembelian();
                                @endphp
                                @if ($selisih < 0)
                                    <tr>
                                        <td colspan="3" class="text-danger"><strong>Kekurangan (Klaim)</strong></td>
                                        <td colspan="4" class="text-right text-danger">
                                            <strong>Rp. {{ number_format(abs($selisih), 0, ',', '.') }}</strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3"><strong>Sisa / Pengembalian</strong></td>
                                        <td colspan="4" class="text-right">
                                            <strong>Rp. {{ number_format($selisih, 0, ',', '.') }}</strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal fade" id="modal_lpj">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Upload LPJ</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin-head.lpj.update', $lpj->id) }}" method="post"
                                        enctype="multipart/form-data">
                                        @method('PUT')
                                        @csrf
                                        <input type="hidden" id="pengajuan_detail_id" name="pengajuan_detail_id">

                                        <div class="mb-3">
                                            <label for="" class="form-label">Tanggal Pembelian</label>
                                            <input type="date"
                                                class="form-control @error('tanggal_perolehan') is-invalid @enderror"
                                                name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}"
                                                placeholder="">
                                            @error('tanggal_pembelian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Harga Pembelian <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number"
                                                    class="form-control @error('harga_pembelian') is-invalid @enderror"
                                                    id="harga_pembelian"
                                                    name="harga_pembelian" value="{{ old('harga_pembelian') }}" placeholder="Masukkan total harga pembelian" required>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge badge-light border text-dark" id="preview_harga_pembelian">Rp 0</span>
                                            </div>
                                            @error('harga_pembelian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Struk Pembelian <span class="text-danger">*</span></label>
                                            <input type="file"
                                                class="form-control @error('struk_pembelian') is-invalid @enderror"
                                                id="struk_pembelian"
                                                name="struk_pembelian"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i>Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                            @error('struk_pembelian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Lampiran (Optional)</label>
                                            <input type="file"
                                                class="form-control @error('lampiran') is-invalid @enderror"
                                                id="lampiran_file"
                                                name="lampiran"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i>Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                            @error('lampiran')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" placeholder="LPJ Pembelian ...">{{ old('keterangan') }}</textarea>
                                            @error('keterangan')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->

                    {{-- modal pengembalian   --}}
                    <div class="modal fade" id="bukti_pengembalian">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <span class="modal-title">Upload Bukti Pengembalian</span>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @if ($lpj->nominal_pengembalian == false)
                                        <form action="{{ url('admin-head/lpj/upload-bukti-pengembalian', $lpj->id) }}"
                                            method="post" enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf
                                            <div class="mb-3">
                                                <label for="" class="form-label">Nominal Pengembalian <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number"
                                                        class="form-control @error('nominal_pengembalian') is-invalid @enderror"
                                                        id="nominal_pengembalian"
                                                        name="nominal_pengembalian" value="{{ old('nominal_pengembalian') }}"
                                                        placeholder="Masukkan nominal pengembalian" required>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="badge badge-light border text-dark" id="preview_nominal_pengembalian">Rp 0</span>
                                                </div>
                                                @error('nominal_pengembalian')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">Bukti Pengembalian <span class="text-danger">*</span></label>
                                                <input type="file"
                                                    class="form-control @error('bukti_pengembalian') is-invalid @enderror"
                                                    id="bukti_pengembalian_file"
                                                    name="bukti_pengembalian"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    required>
                                                <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i>Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                                @error('bukti_pengembalian')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">Keterangan</label>
                                                <input type="text"
                                                    class="form-control @error('keterangan_lpj') is-invalid @enderror"
                                                    name="keterangan_lpj" value="{{ old('keterangan_lpj') }}" placeholder="">
                                                @error('keterangan_lpj')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    @else
                                        <h4>*Bukti Pengembalian Sudah di Upload!</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal pengembalian --}}

                    {{-- modal pembelian kurang --}}
                    <div class="modal fade" id="bukti_kurang">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <span class="modal-title">Upload Bukti Pembelian Kurang</span>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @if ($lpj->nominal_pengembalian == false)
                                        <form action="{{ url('admin/lpj/upload-bukti-kurang', $lpj->id) }}"
                                            method="post" enctype="multipart/form-data">
                                            @method('PUT')
                                            @csrf
                                            <div class="mb-3">
                                                <label for="" class="form-label">Nominal Kurang</label>
                                                <input type="number"
                                                    class="form-control @error('nominal_kurang') is-invalid @enderror"
                                                    name="nominal_kurang" value="{{ old('nominal_kurang') }}"
                                                    placeholder="">
                                                @error('nominal_kurang')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">Bukti Kekurangan</label>
                                                <input type="file"
                                                    class="form-control @error('bukti_kurang') is-invalid @enderror"
                                                    name="bukti_kurang" value="{{ old('bukti_kurang') }}"
                                                    placeholder="">
                                                @error('bukti_kurang')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">Keterangan</label>
                                                <input type="text"
                                                    class="form-control @error('keterangan_lpj') is-invalid @enderror"
                                                    name="keterangan_lpj" value="{{ old('keterangan_lpj') }}" placeholder="">
                                                @error('keterangan_lpj')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    @else
                                        <h4>*Bukti Pengembalian Sudah di Upload!</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal pembelian kurang --}}
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
            let modal = $('#bukti_pengembalian').modal('show');
            updateNominalPengembalianPreview();
        }

        function bukti_kurang() {
            let modal = $('#bukti_kurang').modal('show');
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

        $('#lampiran_file').on('change', function() {
            validasiUkuranFile(this, 'Lampiran');
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
            $butuhPengembalian = ($selisihLebih > 0 && (empty($lpj->bukti_pengembalian) || empty($lpj->nominal_pengembalian)));
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
                        html: 'Masih ada <b>' + itemBelum + ' item</b> yang belum dibuatkan LPJ.<br>Silakan klik tombol <b>"Buat LPJ"</b> pada setiap item terlebih dahulu.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Masih ada ' + itemBelum + ' item yang belum dibuatkan LPJ. Silakan selesaikan LPJ per item terlebih dahulu.');
                }
                return false;
            }

            if (butuhPengembalian) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bukti Pengembalian Belum Ada',
                        html: 'Terdapat sisa uang muka sebesar <b>Rp ' + sisaNominal + '</b>.<br>Silakan klik tombol <b>"Upload Bukti Pengembalian"</b> terlebih dahulu sebelum mengajukan LPJ.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Terdapat sisa uang muka sebesar Rp ' + sisaNominal + '. Harap upload bukti pengembalian terlebih dahulu.');
                }
                return false;
            }

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
