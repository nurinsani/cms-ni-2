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
            <img src="https://images.glints.com/unsafe/glints-dashboard.s3.amazonaws.com/company-photos/4ee7c569b5fdc3017f63a6c2779474d6.jpg" width="150px" alt="Logo">
            <div class="text-center">
                <h4 class="font-weight-bold mb-1">LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                <h5 class="mb-0">TANGGAL {{ $lpj->tanggal_pengajuan?->format('d-M-Y') ?? $lpj->tanggal_pengajuan }}</h5>
            </div>
            <div></div>
        </div>

        <div class="card-header d-print-none">
            <h3 class="card-title font-weight-bold">
                Nomor LPJ: <span class="text-primary">{{ $lpj->nomor }}</span>
            </h3>
            <div class="card-tools">
                <a href="{{ route('it.lpj.index') }}" class="btn btn-sm btn-secondary mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="#" onclick="bukti_pengembalian({{ $lpj->id }})" class="btn btn-sm btn-info mr-1">
                    <i class="fas fa-upload mr-1"></i> Upload Bukti Pengembalian
                </a>
                <a href="{{ route('it.ajukan_lpj', $lpj->id) }}" class="btn btn-sm btn-success font-weight-bold">
                    <i class="fas fa-paper-plane mr-1"></i> Ajukan LPJ
                </a>
            </div>
        </div>

        <div class="card-body">
            @if ($lpj->nominal_pengembalian)
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-check-circle mr-1"></i> <strong>Bukti Pengembalian</strong> sudah diunggah (Nominal: Rp {{ number_format($lpj->nominal_pengembalian, 0, ',', '.') }}).
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
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lpj->pengajuan_details as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $item->ap?->unit ?? ($item->area?->area ?? '-') }}</td>
                                <td class="align-middle">{{ $item->jenis_peralatan?->param_peralatan?->coa ?? '-' }}</td>
                                <td class="align-middle font-weight-bold">{{ $item->jenis_peralatan?->nama_jenis ?? '-' }}</td>
                                <td class="align-middle">{{ $item->merk }} {{ $item->type }}</td>
                                <td class="text-right align-middle">Rp {{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($item->harga_pembelian ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center align-middle">
                                    @if ($item->status_lpj == 'belum')
                                        <button type="button" onclick="buat_lpj({{ $item->id }})" class="btn btn-sm btn-primary">
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
                            <td colspan="2" class="text-right"><strong>Rp {{ number_format($lpj->subtotal(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Realisasi Pembelian</strong></td>
                            <td colspan="2" class="text-right text-primary"><strong>Rp {{ number_format($lpj->total_pembelian(), 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Selisih (Sisa / Kurang)</strong></td>
                            <td colspan="2" class="text-right text-success">
                                <strong>Rp {{ number_format($lpj->subtotal() - $lpj->total_pembelian(), 0, ',', '.') }}</strong>
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
                        <form action="{{ route('it.lpj.update', $lpj->id) }}" method="post" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="pengajuan_detail_id" name="pengajuan_detail_id">

                                <div class="form-group">
                                    <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_perolehan') is-invalid @enderror"
                                        name="tanggal_perolehan" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                                    @error('tanggal_pembelian')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Harga Realisasi Pembelian (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('harga_pembelian') is-invalid @enderror"
                                        name="harga_pembelian" value="{{ old('harga_pembelian') }}" placeholder="Masukkan total harga pembelian" required>
                                    @error('harga_pembelian')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label class="form-label">Struk Pembelian <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border rounded p-1 @error('struk_pembelian') is-invalid @enderror"
                                        name="struk_pembelian" required>
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
                                <form action="{{ url('it/lpj/upload-bukti-pengembalian', $lpj->id) }}" method="post" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Nominal Pengembalian (Rp)</label>
                                        <input type="number" class="form-control @error('nominal_pengembalian') is-invalid @enderror"
                                            name="nominal_pengembalian" value="{{ old('nominal_pengembalian') }}" required>
                                        @error('nominal_pengembalian')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Bukti Pengembalian</label>
                                        <input type="file" class="form-control-file border rounded p-1 @error('bukti_pengembalian') is-invalid @enderror"
                                            name="bukti_pengembalian" required>
                                        @error('bukti_pengembalian')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" class="form-control @error('keterangan_lpj') is-invalid @enderror"
                                            name="keterangan_lpj" value="{{ old('keterangan_lpj') }}">
                                        @error('keterangan_lpj')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="modal-footer px-0 pb-0 mt-3">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
        function buat_lpj(pengajuan_detail_id) {
            let modal = $('#modal_lpj').modal('show');
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id);
        }

        function bukti_pengembalian() {
            $('#bukti_pengembalian').modal('show');
        }
    </script>
@endpush
