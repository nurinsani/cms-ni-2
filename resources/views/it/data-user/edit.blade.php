@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('it.data-user.index') }}">Data User</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    @php
        $currentRole = old('role', $user->roles->first()?->name ?? ($user->getRoleNames()[0] ?? ''));
        $currentArea = old('code_area', $user->code_area);
    @endphp
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                Edit Data User
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('it.data-user.update', $user->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                            Informasi Pribadi & Rekening
                        </h6>

                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" name="name"
                                placeholder="Masukkan Nama Lengkap User" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" id="nik" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $user->nik) }}" name="nik" placeholder="Masukkan NIK Karyawan"
                                required>
                            @error('nik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" name="email" placeholder="Masukkan Email User"
                                required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="nama_bank" class="form-label">Nama Bank <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="nama_bank"
                                        class="form-control @error('nama_bank') is-invalid @enderror"
                                        value="{{ old('nama_bank', $user->nama_bank) }}" name="nama_bank"
                                        placeholder="Contoh: BSI / BCA" required>
                                    @error('nama_bank')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label for="no_rek" class="form-label">Nomor Rekening <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="no_rek"
                                        class="form-control @error('no_rek') is-invalid @enderror"
                                        value="{{ old('no_rek', $user->no_rek) }}" name="no_rek"
                                        placeholder="Masukkan Nomor Rekening" required>
                                    @error('no_rek')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="atas_nama" class="form-label">Atas Nama Rekening <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="atas_nama"
                                class="form-control @error('atas_nama') is-invalid @enderror"
                                value="{{ old('atas_nama', $user->atas_nama) }}" name="atas_nama"
                                placeholder="Masukkan Nama Pemilik Rekening" required>
                            @error('atas_nama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                            Hak Akses & Penempatan Cabang
                        </h6>

                        <div class="form-group">
                            <label for="role" class="form-label">Level / Hak Akses <span
                                    class="text-danger">*</span></label>
                            <select class="form-control select2bs4 @error('role') is-invalid @enderror" name="role"
                                id="role" required>
                                <option value="">-- Pilih Level --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $currentRole == $role->name ? 'selected' : '' }}>
                                        {{ strtoupper(str_replace(['-', '_'], ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code_area" class="form-label">Cabang / Unit <span
                                    class="text-danger">*</span></label>
                            <select id="code_area"
                                class="form-control select2bs4 @error('code_area') is-invalid @enderror" name="code_area"
                                required>
                                <option value="">-- Pilih Cabang / Unit --</option>
                                @foreach ($cabang as $data)
                                    <option value="{{ $data->code_area }}"
                                        {{ (string) $currentArea === (string) $data->code_area ? 'selected' : '' }}>
                                        {{ $data->unit }} {{ $data->area ? '- ' . $data->area : '' }}
                                        ({{ $data->code_area }})
                                    </option>
                                @endforeach
                            </select>
                            @error('code_area')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password Baru <small
                                    class="text-muted">(Opsional)</small></label>
                            <input type="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="form-text text-muted">Isi hanya jika Anda ingin mengganti password user ini
                                (minimal 6 karakter).</small>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('it.data-user.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
