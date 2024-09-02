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
    <div class="card">
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor</th>
                        <th>Cabang</th>
                        <th>AP</th>
                        <th>COA</th>
                        <th>Nama Jenis Peralatan</th>
                        <th>Tanggal Pengajuan</th>
                        {{-- <th>Keterangan</th> --}}
                        <th>Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->area->area }}</td>
                            <td>
                                <ul class="p-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li> {{ $data->ap->unit ?? $item->area->area}}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="p-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li> {{ $data->jenis_peralatan->param_peralatan->coa }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="p-0" style="list-style: none">
                                    @foreach ($item->pengajuan_details as $data)
                                        <li> {{ $data->jenis_peralatan->nama_jenis }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $item->tanggal_pengajuan->format('d-M-Y') }}</td>
                            <td>
                                <a href="{{ url('admin-head/lpj', $item->id) }}" class="btn btn-sm btn-primary">Lihat</a>
                                {{-- <a href="{{ route('admin.ajukan_lpj', $item->id) }}" class="btn btn-sm btn-success">Ajukan
                                    LPJ</a> --}}
                                {{-- <a href="{{ route('admin.lpj.edit', $item->id) }}" class="btn btn-sm btn-success">Edit</a> --}}
                            </td>
                        </tr>
                    @endforeach

                    </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
        <div class="modal fade" id="modal_lpjs" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Pengajuan LPJ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.lpj.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="pengajuan_id" name="pengajuan_id">
                            <div class="mb-3">
                                <label for="" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                    name="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}" placeholder="">
                                @error('tanggal_pembelian')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Harga Pembelian</label>
                                <input type="number" class="form-control @error('harga_pembelian') is-invalid @enderror"
                                    name="harga_pembelian" value="{{ old('harga_pembelian') }}" placeholder="">
                                @error('harga_pembelian')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Struk Pembelian</label>
                                <input type="file" class="form-control @error('struk_pembelian') is-invalid @enderror"
                                    name="struk_pembelian" value="{{ old('struk_pembelian') }}" placeholder="">
                                @error('struk_pembelian')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Bukti Pengembalian</label>
                                <input type="file" class="form-control @error('bukti_pengembalian') is-invalid @enderror"
                                    name="bukti_pengembalian" value="{{ old('bukti_pengembalian') }}" placeholder="">
                                @error('bukti_pengembalian')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" id="" cols="30"
                                    rows="10">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_id) {
            let modal = $('#modal_lpj').modal('show')
            // modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
            modal.find('#pengajuan_id').val(pengajuan_id)
        }
    </script>
@endpush
