@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
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
    <div class="card">
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @error('error')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror
            <form action="{{ url('/update-password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="old_password">Password lama</label>
                    <input type="password" id="old_password" name="old_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" class="form-control" name="password" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Konfirmasi Kata Sandi</label>
                    <input type="password" id="new_password" class="form-control" name="new_password" autocomplete="off"
                        required>
                </div>
                <div class="form-group">
                    <button class="btn btn-success float-right">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
