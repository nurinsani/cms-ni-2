@extends('layouts.main')

@section('head-content')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold"><i class="fas fa-key text-primary mr-2"></i>{{ $title }}</h1>
            <small class="text-muted">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan</small>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('#') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-lock mr-1 text-primary"></i> Formulir Ganti Password
                    </h3>
                </div>
                <div class="card-body">
                    @error('error')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </div>
                    @enderror
                    <form action="{{ url('/update-password') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="old_password">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Masukkan password lama" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" id="password" class="form-control" name="password" placeholder="Minimal 8 karakter" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" id="new_password" class="form-control" name="new_password" placeholder="Ulangi password baru" autocomplete="off" required>
                        </div>
                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save mr-1"></i> Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
