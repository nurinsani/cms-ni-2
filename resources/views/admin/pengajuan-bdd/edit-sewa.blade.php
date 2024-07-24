@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            {{-- <h1 class="m-0">{{ $title }}</h1> --}}
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.data-user.index') }}">Data User</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            {{ $title }}
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pengajuan-bdd.update-sewa', $bdd_sewa->id) }}" method="post"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama AP</label>
                            <select class="form-control" name="kode_branch">
                                <option value="" hidden>-- Pilih Nama Ap --</option>
                                @foreach ($branch as $data)
                                    <option value="{{ $data->kode_branch }}"
                                        @if ($bdd_sewa->kode_branch == $data->kode_branch) selected @endif>{{ $data->unit }}</option>
                                @endforeach
                            </select>
                            @error('kode_branch')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- {{ $sewa }} --}}
                        <div class="form-group">
                            <label>Jenis Pengajuan</label>
                            <select class="form-control @error('jenis_bdd_id') is-invalid @enderror" name="jenis_bdd_id">
                                <option value="" hidden>-- Pilih Jenis BDD --</option>
                                @foreach ($sewa as $data)
                                    <option value="{{ $data->id }}" @if ($bdd_sewa->jenis_bdd_id == $data->id) selected @endif>
                                        {{ $data->nama_jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis_bdd_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Lokasi</label>
                            <input type="text" class="form-control @error('lokasi') is-invalid @enderror"
                                value="{{ old('lokasi', $bdd_sewa->lokasi) }}" name="lokasi"
                                placeholder="Masukkan Lokasi">
                            @error('lokasi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Jenis Sewa</label>
                            <input type="text" class="form-control @error('jenis_sewa') is-invalid @enderror"
                                value="{{ old('jenis_sewa', $bdd_sewa->jenis_sewa) }}" name="jenis_sewa"
                                placeholder="Masukkan Jeni Sewa">
                            @error('jenis_sewa')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tipe Bangunan</label>
                            <select class="form-control @error('type_bangunan') is-invalid @enderror" name="type_bangunan">
                                <option value="" hidden>-- Pilih Tipe Bangunan --</option>
                                <option value="ruko" @if ($bdd_sewa->type_bangunan == 'ruko') selected @endif>Ruko</option>
                                <option value="rumah" @if ($bdd_sewa->type_bangunan == 'rumah') selected @endif>Rumah</option>
                            </select>
                            @error('type_bangunan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Nama Pemilik</label>
                            <input type="text" class="form-control @error('nama_pemilik') is-invalid @enderror"
                                value="{{ old('nama_pemilik', $bdd_sewa->nama_pemilik) }}" name="nama_pemilik"
                                placeholder="Masukkan Nama Pemilik">
                            @error('nama_pemilik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Status Kepemilikan</label>
                            <input type="text" class="form-control @error('status_kepemilikan') is-invalid @enderror"
                                value="{{ old('status_kepemilikan', $bdd_sewa->status_kepemilikan) }}"
                                name="status_kepemilikan" placeholder="Masukkan Status Kepemilikan">
                            @error('status_kepemilikan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No SHM/HGB</label>
                            <input type="file" class="form-control @error('no_shm_hgb') is-invalid @enderror"
                                value="{{ old('no_shm_hgb', $bdd_sewa->no_shm_hgb) }}" name="no_shm_hgb"
                                placeholder="Masukkan No SHM/HGB">
                            @error('no_shm_hgb')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No Langganan Listrik</label>
                            <input type="number" class="form-control @error('no_langganan_listrik') is-invalid @enderror"
                                value="{{ old('no_langganan_listrik', $bdd_sewa->no_langganan_listrik) }}"
                                name="no_langganan_listrik" placeholder="Masukkan No Langganan Listrik">
                            @error('no_langganan_listrik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-label">No Langganan PAM</label>
                            <input type="number" class="form-control @error('no_langganan_pam') is-invalid @enderror"
                                value="{{ old('no_langganan_pam', $bdd_sewa->no_langganan_pam) }}" name="no_langganan_pam"
                                placeholder="Masukkan No Langganan PAM">
                            @error('no_langganan_pam')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No Langganan Telp</label>
                            <input type="number" class="form-control @error('no_langganan_telp') is-invalid @enderror"
                                value="{{ old('no_langganan_telp', $bdd_sewa->no_langganan_telp) }}"
                                name="no_langganan_telp" placeholder="Masukkan Langganan Telp">
                            @error('no_langganan_telp')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Harga Sewa</label>
                            <input type="text" id="rupiah"
                                class="form-control @error('harga_sewa') is-invalid @enderror"
                                value="{{ old('harga_sewa', $bdd_sewa->harga_sewa) }}" name="harga_sewa"
                                placeholder="Masukkan Harga Sewa">
                            @error('harga_sewa')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No Rekening Pemilik</label>
                            <input type="number" class="form-control @error('no_rek_bank_pemilik') is-invalid @enderror"
                                value="{{ old('no_rek_bank_pemilik', $bdd_sewa->no_rek_bank_pemilik) }}"
                                name="no_rek_bank_pemilik" placeholder="Masukkan No Rekening Pemilik">
                            @error('no_rek_bank_pemilik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Atas Nama</label>
                            <input type="text" class="form-control @error('atas_nama') is-invalid @enderror"
                                value="{{ old('atas_nama', $bdd_sewa->atas_nama) }}" name="atas_nama"
                                placeholder="Masukkan Atas Nama">
                            @error('atas_nama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">No HP Pemilik</label>
                            <input type="number" class="form-control @error('no_hp_pemilik') is-invalid @enderror"
                                value="{{ old('no_hp_pemilik', $bdd_sewa->no_hp_pemilik) }}" name="no_hp_pemilik"
                                placeholder="Masukkan No Hp Pemilik">
                            @error('no_hp_pemilik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">Jangka Waktu</label>
                            <input type="number"
                                class="form-control @error('jangka_waktu_amortisasi') is-invalid @enderror"
                                value="{{ old('jangka_waktu_amortisasi', $bdd_sewa->jangka_waktu_amortisasi) }}"
                                name="jangka_waktu_amortisasi" placeholder="Masukkan Jangka Waktu">
                            @error('jangka_waktu_amortisasi')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>

            </form>
        </div>
    </div>
@endsection
