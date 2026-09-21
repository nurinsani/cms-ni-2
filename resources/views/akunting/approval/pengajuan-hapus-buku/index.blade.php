@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('akunting.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title font-weight-bold">
                Data Pengajuan Hapus Buku Inventaris Gol 1
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
                            <th>Jenis Aktiva</th>
                            <th>Bulan Hapus Buku</th>
                            <th>Admin Pengaju</th>
                            <th>Disetujui Admin Head</th>
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
                                <td><span class="badge badge-secondary">{{ $item->jenis_aktiva }}</span></td>
                                <td>{{ $item->bulan_hapus_buku }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    <span class="text-success small font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $item->admin_head->name ?? 'Admin Head' }}
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold">{{ $item->details->count() }}</td>
                                <td class="text-center">
                                    <a href="{{ route('akunting.approval-hapus-buku.show', $item->id) }}"
                                        class="btn btn-info btn-xs mr-1">
                                        <i class="fas fa-eye"></i> Detail & Perhitungan
                                    </a>

                                    <!-- Tombol Modal Approve -->
                                    <button type="button" class="btn btn-success btn-xs mr-1" data-toggle="modal"
                                        data-target="#modalApproveAkunting{{ $item->id }}">
                                        <i class="fas fa-check-double"></i> Approve
                                    </button>

                                    <!-- Tombol Modal Tolak -->
                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal"
                                        data-target="#modalTolakAkunting{{ $item->id }}">
                                        <i class="fas fa-times"></i> Tolak
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
                                                        <h5 class="modal-title font-weight-bold">Final Approval Hapus Buku
                                                            (Akunting)
                                                        </h5>
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
                                                        <button type="submit" class="btn btn-success">Setujui
                                                            Penghapusan</button>
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
                                                <form action="{{ route('akunting.approval-hapus-buku.tolak', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold">Tolak Pengajuan Hapus Buku
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0">Apakah Anda yakin ingin menolak pengajuan
                                                            <strong>{{ $item->kode_pengajuan }}</strong>?
                                                        </p>
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
                                <td colspan="9" class="text-center text-muted py-4">Tidak ada pengajuan hapus buku yang
                                    menunggu approval Akunting.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
