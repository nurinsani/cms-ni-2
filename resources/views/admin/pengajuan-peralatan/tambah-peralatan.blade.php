@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan.index') }}">Pengajuan</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.pengajuan.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center">
                    <a href="{{ url('admin/pengajuan') }}" class="btn btn-danger mb-2 mr-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#modal-peralatan">
                        <i class="fas fa-plus"></i> Tambah Pengajuan
                    </button>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="col-12 col-md-12">
                    <div class="table-sm table-responsive">
                        <table id="example2" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Nama Jenis</th>
                                    <th scope="col">AP</th>
                                    <th scope="col">Merk</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Nama Toko</th>
                                    <th scope="col">No Telp</th>
                                    <th scope="col">Pengguna</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="base_peralatan">
                                @foreach ($temp_pengajuan as $item)
                                <tr>
                                    <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                    <td>{{ $item->ap->unit ?? $item->area->area ?? '(AP/Area tidak ada)'}}</td>
                                    <td>{{ $item->merk }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->kuantitas }}</td>
                                    <td>Rp. {{ number_format($item->harga) }}</td>
                                    <td>{{ $item->nama_toko }}</td>
                                    <td>{{ $item->no_telp }}</td>
                                    <td>{{ $item->pengguna }}</td>
                                    <td>
                                        <a href="{{ url('admin/temp-pengajuan/'. $item->id) }}" onclick="hapus_data('#delete_form{{ $item->id }}')" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="col-md">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Ajukan
                    </button>
                </div>
            </div>
        </div>
    </form>

    @include('admin.pengajuan-peralatan.form_input')

    @push('js')

        <script>
            document.getElementById('select-option').addEventListener('change', function() {
                var selectedValue = this.value;
        
                // Hide semua dropdown
                document.getElementById('select-ap').classList.add('d-none');
                document.getElementById('select-cabang').classList.add('d-none');
        
                if (selectedValue === 'ap') {
                    document.getElementById('select-ap').classList.remove('d-none');
                } else if (selectedValue === 'cabang') {
                    document.getElementById('select-cabang').classList.remove('d-none');
                }
            });
        </script>

        <script type="text/javascript">

            $(document).ready(function() {
                $('#nama_peralatan').on('change', function() {
                    var jenis_peralatan_id = $(this).val()
                    if (jenis_peralatan_id) {
                        $.ajax({
                            // url: '/jenis-peralatan-dropdown/' + jenis_peralatan_id,
                            url: "{{ url('jenis-peralatan-dropdown') }}" + "/" + jenis_peralatan_id,
                            type: "GET",
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            dataType: "json",
                            success: function(data) {
                                // console.log(data)
                                if (data) {
                                    $('.jenis_peralatan').empty()
                                    $('.jenis_peralatan').append(
                                        '<option hidden>-- Pilih Jenis Peralatan --</option>')
                                    $.each(data, function(key, jenis_peralatan) {
                                        // console.log()
                                        $('.jenis_peralatan').append('<option value="' +
                                            jenis_peralatan.id + '">' +
                                            jenis_peralatan.nama_jenis + '</option>')
                                        // console.log(data)
                                    })
                                } else {
                                    $('.jenis_peralatan').empty()
                                }
                            }
                        })
                    } else {
                        $('.jenis_peralatan').empty()
                    }
                })
            })

            function remove_item(tr_id) {
                $(tr_id).remove()
            }
        </script>
    @endpush

@include('sweetalert::alert')
@endsection