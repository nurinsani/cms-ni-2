@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
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
    <div class="card">
        <div class="card-body">
            <form action="{{ route('it.data-user.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" name="name" placeholder="Masukan Nama User">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">NIK</label>
                            <input type="number" class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik') }}" name="nik" placeholder="Masukan NIk">
                            @error('nik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" name="email" placeholder="Masukan Email User">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No Rekening</label>
                            <input type="number" class="form-control @error('no_rek') is-invalid @enderror"
                                value="{{ old('no_rek') }}" name="no_rek" placeholder="Masukan no_rek User">
                            @error('no_rek')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Nama Bank</label>
                            <input type="text" class="form-control @error('nama_bank') is-invalid @enderror"
                                value="{{ old('nama_bank') }}" name="nama_bank" placeholder="Masukan nama_bank User">
                            @error('nama_bank')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Atas Nama</label>
                            <input type="text" class="form-control @error('atas_nama') is-invalid @enderror"
                                value="{{ old('atas_nama') }}" name="atas_nama" placeholder="Masukan atas_nama User">
                            @error('atas_nama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role" class="form-label">Level</label>
                            <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>
                                <option value="" hidden>-- Pilih Level --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" 
                                        {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Cabang</label>
                            <select id="code_area" class="form-control select2bs4 @error('code_area') is-invalid @enderror"
                                name="code_area">
                                <option value="" hidden>-- Pilih Branch --</option>
                                @foreach ($branch as $b)
                                    <option value="{{ $b->code_area }}" {{ old('code_area') == $b->code_area ? 'selected' : '' }}>
                                        {{ $b->unit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('code_area')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                value="{{ old('password') }}" name="password" placeholder="Masukan Password">
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
@endsection
