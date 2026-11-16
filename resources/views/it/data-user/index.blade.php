@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                Daftar Pengguna Terdaftar
            </h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('it.data-user.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="fas fa-user-plus mr-1"></i> Tambah User
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
            <table id="example2" class="table table-bordered table-hover table-striped align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nama & Username</th>
                        <th>NIK</th>
                        <th>Email</th>
                        <th>Info Rekening</th>
                        <th>Cabang / Unit</th>
                        <th class="text-center">Level</th>
                        <th width="130" class="text-center no-export">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        @php
                            $roleName = $user->roles->first()?->name ?? ($user->getRoleNames()[0] ?? '-');
                            $badgeClass = match ($roleName) {
                                'it', 'atasan-it' => 'badge-info',
                                'admin', 'admin-head' => 'badge-primary',
                                'finance' => 'badge-success',
                                'akunting', 'atasan-akunting', 'atasan_akunting' => 'badge-warning',
                                'pengurus' => 'badge-purple',
                                default => 'badge-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                <small class="text-muted"><i class="fas fa-at mr-1"></i>{{ $user->username ?? '-' }}</small>
                            </td>
                            <td><span class="text-monospace">{{ $user->nik ?? '-' }}</span></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $user->nama_bank ?? '-' }} - {{ $user->no_rek ?? '-' }}
                                </div>
                                @if (!empty($user->atas_nama))
                                    <small class="text-muted">a.n. {{ $user->atas_nama }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($user->branch)
                                    <div class="font-weight-bold">{{ $user->branch->unit ?? $user->branch->area }}</div>
                                    <small class="text-muted">{{ $user->branch->area }} ({{ $user->code_area }})</small>
                                @elseif ($user->area)
                                    <div>{{ $user->area->region }}</div>
                                @else
                                    <span class="text-muted">{{ $user->code_area ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase">
                                    {{ str_replace(['-', '_'], ' ', $roleName) }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('it.data-user.edit', $user->id) }}"
                                    class="btn btn-sm btn-outline-primary mr-1" title="Edit User">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if (auth()->id() !== $user->id)
                                    <button type="button" onclick="hapus_data('#delete_form{{ $user->id }}')"
                                        class="btn btn-sm btn-outline-danger" title="Hapus User">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                    <form class="d-inline" id="delete_form{{ $user->id }}"
                                        action="{{ route('it.data-user.destroy', $user->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
@endsection
