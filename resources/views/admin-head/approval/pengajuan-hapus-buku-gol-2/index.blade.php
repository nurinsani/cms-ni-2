@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin-head.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title font-weight-bold">
                Daftar Persetujuan Hapus Buku Inventaris Gol 2
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Kode Pengajuan</th>
                            <th>KP/Cabang/AP</th>
                            <th>Bulan Hapus Buku</th>
                            <th>Pengaju</th>
                            <th class="text-center">Jml Barang</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan_hapus_buku as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $item->kode_pengajuan }}</td>
                                <td>{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                <td>{{ $item->bulan_hapus_buku }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td class="text-center font-weight-bold">{{ $item->details->count() }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin-head.approval-hapus-buku-gol-2.show', $item->id) }}"
                                        class="btn btn-info btn-xs mr-1">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                    <!-- Tombol Modal Approve -->
                                    <button type="button" class="btn btn-success btn-xs mr-1" data-toggle="modal"
                                        data-target="#modalApprove{{ $item->id }}">
                                        <i class="fas fa-check"></i> Approve
                                    </button>

                                    <!-- Tombol Modal Tolak -->
                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal"
                                        data-target="#modalTolak{{ $item->id }}">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>

                                    <!-- Modal Approve -->
                                    <div class="modal fade text-left" id="modalApprove{{ $item->id }}"
                                        tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('admin-head.approval-hapus-buku-gol-2.approve', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title font-weight-bold">Konfirmasi Persetujuan
                                                            Admin Head</h5>
                                                        <button type="button" class="close text-white"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0">Apakah Anda yakin ingin menyetujui pengajuan hapus
                                                            buku Gol 2 <strong>{{ $item->kode_pengajuan }}</strong>?</p>
                                                        <div class="alert alert-info small mt-3 mb-0">
                                                            <i class="fas fa-info-circle mr-1"></i> Pengajuan ini akan
                                                            diteruskan ke divisi <strong>Akunting</strong> untuk
                                                            persetujuan akhir.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui &
                                                            Teruskan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Tolak -->
                                    <div class="modal fade text-left" id="modalTolak{{ $item->id }}" tabindex="-1"
                                        role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('admin-head.approval-hapus-buku-gol-2.tolak', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold">Tolak Pengajuan Hapus Buku Gol 2
                                                        </h5>
                                                        <button type="button" class="close text-white"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tuliskan alasan penolakan untuk pengajuan
                                                            <strong>{{ $item->kode_pengajuan }}</strong>:
                                                        </p>
                                                        <div class="form-group mb-0">
                                                            <textarea name="catatan" class="form-control" rows="3" placeholder="Alasan penolakan..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak
                                                            Pengajuan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada pengajuan hapus buku Gol 2 yang menunggu persetujuan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
