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
                <div class="card-body">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <ul class="p-0" style="list-style: none">
                        <li>Nomor : {{ $lpj->nomor }}</li>
                        <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
                        <li>Dari : {{ $lpj->user->name }}</li>
                        <li>Tanggal : {{ $lpj->tanggal_pengajuan->format('d-M-Y') }}</li>
                    </ul>

                    <a href="{{ route('admin.ajukan_lpj', $lpj->id) }}" class="btn btn-sm btn-success my-3">AJUKAN LPJ</a>

                    <a href="#" onclick="bukti_pengembalian({{ $lpj->id }} )"
                        class="btn btn-sm btn-primary">UNGGAH BUKTI PENGEMBALIAN
                    </a>

                    <div>
                        @if ($lpj->nominal_pengembalian == true)
                            <div class="text-danger">*Bukti Pengembalian sudah di upload.</div>
                        @endif
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">NO</th>
                                    <th scope="col">AP</th>
                                    <th scope="col">COA</th>
                                    <th scope="col">NAMA JENIS PERALATAN</th>
                                    <th scope="col">MERK</th>
                                    <th scope="col">TOTAL PENGAJUAN</th>
                                    <th scope="col">HARGA BELI</th>
                                    <th scope="col">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lpj->pengajuan_details as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->ap->unit ?? $item->area->area }}</td>
                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                        <td>{{ $item->merk }}</td>
                                        <td>Rp. {{ number_format($item->harga * $item->kuantitas) }}</td>
                                        <td>Rp. {{ number_format($item->harga_pembelian) }}</td>
                                        <td>
                                            @if ($item->status_lpj == 'belum')
                                                <a href="#" onclick="buat_lpj({{ $item->id }} )"
                                                    class="btn btn-sm btn-primary">Buat LPJ</a>
                                            @else
                                                <a href="#" onclick="buat_lpj({{ $item->id }} )"
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

                                {{ number_format($lpj->subtotal() - $lpj->total_pembelian()) }}</strong>
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
                                    <form action="{{ route('admin.lpj.update', $lpj->id) }}" method="post"
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
                                            <label for="" class="form-label">Harga Pembelian</label>
                                            <input type="number"
                                                class="form-control @error('harga_pembelian') is-invalid @enderror"
                                                name="harga_pembelian" value="{{ old('harga_pembelian') }}" placeholder="">
                                            @error('harga_pembelian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Struk Pembelian</label>
                                            <input type="file"
                                                class="form-control @error('struk_pembelian') is-invalid @enderror"
                                                name="struk_pembelian" value="{{ old('struk_pembelian') }}" placeholder="">
                                            @error('struk_pembelian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Keterangan</label>
                                            <textarea name="keterangan_lpj" class="form-control" cols="5" placeholder="LPJ Pembelian ..." required></textarea>
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
                                    <form action="{{ url('admin/lpj/upload-bukti-pengembalian', $lpj->id) }}"
                                        method="post" enctype="multipart/form-data">
                                        @method('PUT')
                                        @csrf
                                        <div class="mb-3">
                                            <label for="" class="form-label">Nominal Pengembalian</label>
                                            <input type="number"
                                                class="form-control @error('nominal_pengembalian') is-invalid @enderror"
                                                name="nominal_pengembalian" value="{{ old('nominal_pengembalian') }}"
                                                placeholder="">
                                            @error('nominal_pengembalian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Bukti Pengembalian</label>
                                            <input type="file"
                                                class="form-control @error('bukti_pengembalian') is-invalid @enderror"
                                                name="bukti_pengembalian" value="{{ old('bukti_pengembalian') }}"
                                                placeholder="">
                                            @error('bukti_pengembalian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="" class="form-label">Keterangan</label>
                                            <input type="text"
                                                class="form-control @error('keterangan_lpj') is-invalid @enderror"
                                                name="keterangan_lpj" value="{{ old('keterangan_lpj') }}"
                                                placeholder="">
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
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end modal pengembalian --}}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_detail_id) {
            let modal = $('#modal_lpj').modal('show')
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
        }
    </script>

    <script>
        function bukti_pengembalian() {
            let modal = $('#bukti_pengembalian').modal('show')
        }
    </script>
@endpush
