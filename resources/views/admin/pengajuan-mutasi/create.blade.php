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

<form action="{{ route('admin.pengajuan-mutasi.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih AP</label>
                        <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="dari_cabang" id="kode_branch">
                            <option value="" hidden>-- Pilih Nama Ap --</option>
                            @foreach ($pilih_ap as $data)
                                <option value="{{ $data->kode_branch }}">{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('dari_cabang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Mutasi AP</label>
                        <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="ke_cabang">
                            <option value="" hidden>-- Pilih Mutasi Ap --</option>
                            @foreach ($mutasi_ke as $data)
                                <option value="{{ $data->kode_branch }}">{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('ke_cabang')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Bulan Mutasi</label>
                        <select class="form-control select2bs4 @error('bulan') is-invalid @enderror" name="bulan_mutasi">
                            <option value="" hidden>-- Pilih Bulan --</option>
                            @foreach (["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] as $bulan)
                                <option value="{{ $bulan }}">{{ $bulan }}</option>
                            @endforeach
                        </select>
                        @error('bulan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pilih Inventaris</h3>
                </div>
                <div class="card-body">
                    <div id="inventarisList"></div>
                </div>
            </div>
        </div>
    </div>
</form>
        
    <script>
        $(document).ready(function() {
            $('#kode_branch').change(function() {
                var kodeBranch = $(this).val();
                if (kodeBranch) {
                    $.ajax({
                        url: "{{ url('get-inventaris') }}/" + kodeBranch,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.length > 0) {
                                $('#inventarisCard').removeClass('d-none');
                                $('#inventarisList').empty();
                                $.each(data, function(key, value) {
                                    $('#inventarisList').append(
                                        '<div class="form-check">' +
                                        '<input class="form-check-input" type="checkbox" name="peralatan_id[]" value="' + value.id + '">' +
                                        '<label class="form-check-label">' + value.kode_peralatan + ' - ' + value.jenis + ' - ' + value.keterangan + ' - ' + 'Rp' + value.harga_perolehan + '</label>' +
                                        '</div>'
                                    );
                                });
                            } else {
                                $('#inventarisCard').addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#inventarisCard').addClass('d-none');
                }
            });
        });
    </script>
@endsection
