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
    <form action="{{ route('admin.pengajuan.store') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-body">
                <a href="{{ url('admin/pengajuan') }}" class="btn btn-danger mr-1">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-inventaris">
                    <i class="fas fa-plus"></i> Tambah Pengajuan
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="table-sm table-responsive">
                            <table id="example2" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">NAMA JENIS</th>
                                        <th scope="col">AP</th>
                                        <th scope="col">MERK</th>
                                        <th scope="col">TYPE</th>
                                        <th scope="col">QTY</th>
                                        <th scope="col">HARGA</th>
                                        <th scope="col">PENGGUNA</th>
                                        <th scope="col">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="base_peralatan">
                                    @foreach ($temp_pengajuan as $item)
                                        <tr>
                                            <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                            <td>{{ $item->ap->unit ?? $item->area->area }}</td>
                                            <td>{{ $item->merk }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->kuantitas }}</td>
                                            <td>Rp. {{ number_format($item->harga) }}</td>
                                            <td>{{ $item->pengguna }}</td>
                                            <td>
                                                <a href="{{ url('admin/temp-pengajuan/' . $item->id) }}"
                                                    onclick="hapus_data('#delete_form{{ $item->id }}')"
                                                    class="btn btn-sm btn-danger">
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
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('admin.pengajuan-peralatan.form_input_inventaris')

    @push('js')
        <script>
            document.getElementById('select-option').addEventListener('change', function() {
                var selectedValue = this.value;

                // Hide semua dropdown
                document.getElementById('select-ap').classList.add('d-none');
                document.getElementById('select-cabang').classList.add('d-none');
                // document.getElementById('select-region').classList.add('d-none');

                // Show the corresponding dropdown based on selection
                if (selectedValue === 'ap') {
                    document.getElementById('select-ap').classList.remove('d-none');
                } else if (selectedValue === 'cabang') {
                    document.getElementById('select-cabang').classList.remove('d-none');
                }
            });

            $(document).ready(function() {
                $('#nama_peralatan').on('change', function() {
                    var jenis_peralatan_id = $(this).val()
                    if (jenis_peralatan_id) {
                        $.ajax({
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

            document.getElementById('harga_view').addEventListener('input', function(e) {

                let angka = e.target.value.replace(/[^0-9]/g, '');

                document.getElementById('harga').value = angka;

                let rupiah = new Intl.NumberFormat('id-ID').format(angka);

                e.target.value = angka ? 'Rp ' + rupiah : '';
            });
        </script>
    @endpush

    @include('sweetalert::alert')
@endsection
