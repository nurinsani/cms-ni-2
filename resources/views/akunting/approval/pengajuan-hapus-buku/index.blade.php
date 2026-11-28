@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">Daftar Approval Hapus Buku Inventaris Gol 1</h3>
            <span class="badge badge-light border ml-auto">{{ $pengajuan_hapus_buku->count() }} Antrean</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped table-hover text-nowrap mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="45" class="text-center">NO</th>
                            <th>KODE PENGAJUAN</th>
                            <th>KP/CABANG/AP</th>
                            <th>JENIS AKTIVA</th>
                            <th>BULAN HAPUS BUKU</th>
                            <th>ADMIN PENGAJU</th>
                            <th>STATUS ADMIN HEAD</th>
                            <th class="text-center">JUMLAH BARANG</th>
                            <th width="220" class="text-center no-export">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan_hapus_buku as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold align-middle">{{ $item->kode_pengajuan }}</td>
                                <td class="align-middle">{{ $item->branch->unit ?? $item->kode_branch }}</td>
                                <td class="align-middle"><span class="badge badge-info">{{ $item->jenis_aktiva }}</span></td>
                                <td class="align-middle">{{ strtoupper($item->bulan_hapus_buku) }}</td>
                                <td class="align-middle">{{ $item->user->name ?? '-' }}</td>
                                <td class="align-middle">
                                    <span class="text-success small font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $item->admin_head->name ?? 'Admin Head' }}
                                    </span>
                                </td>
                                <td class="text-center align-middle font-weight-bold">{{ $item->details->count() }}</td>
                                <td class="text-center align-middle text-nowrap">
                                    <a href="{{ route('akunting.approval-hapus-buku.show', $item->id) }}"
                                        class="btn btn-outline-info btn-sm mr-1">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>

                                    <!-- Tombol Modal Approve -->
                                    <button type="button" class="btn btn-outline-success btn-sm mr-1" data-toggle="modal"
                                        data-target="#modalApproveAkunting{{ $item->id }}">
                                        <i class="fas fa-check-double mr-1"></i> Approve
                                    </button>

                                    <!-- Tombol Modal Tolak -->
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal"
                                        data-target="#modalTolakAkunting{{ $item->id }}">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>

                                    <!-- Modal Approve Akunting -->
                                    <div class="modal fade text-left" id="modalApproveAkunting{{ $item->id }}"
                                        tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('akunting.approval-hapus-buku.approve', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title font-weight-bold">Final Approval Hapus Buku (Akunting)</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0">Apakah Anda yakin ingin menyetujui penghapusan
                                                            buku <strong>{{ $item->kode_pengajuan }}</strong>?</p>
                                                        <div class="alert alert-warning small mt-3 mb-0">
                                                            <i class="fas fa-info-circle mr-1"></i> Setelah disetujui,
                                                            inventaris terkait akan ditandai sebagai <strong>Hapus
                                                                Buku</strong> secara permanen.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Setujui &
                                                            Proses</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Tolak Akunting -->
                                    <div class="modal fade text-left" id="modalTolakAkunting{{ $item->id }}"
                                        tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('akunting.approval-hapus-buku.tolak', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold">Tolak Pengajuan Hapus Buku
                                                            (Akunting)</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
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
                                <td colspan="9" class="text-center text-muted py-4">
                                    Tidak ada pengajuan hapus buku yang menunggu approval Akunting.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
