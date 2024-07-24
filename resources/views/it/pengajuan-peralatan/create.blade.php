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
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.pengajuan.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-12">
                        <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                            data-target="#modal-peralatan">
                            + Tambah Pengajuan Rutin
                        </button>
                        
                        <button type="button" class="btn btn-warning mb-3" data-toggle="modal"
                            data-target="#modal-inventaris">
                            + Tambah Pengajuan Non Rutin
                        </button>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Nama Jenis</th>
                                        <th scope="col">AP</th>
                                        <th scope="col">Merk</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Kuantitas</th>
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
                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->merk }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->kuantitas }}</td>
                                        <td>Rp. {{ number_format($item->harga) }}</td>
                                        <td>{{ $item->nama_toko }}</td>
                                        <td>{{ $item->no_telp }}</td>
                                        <td>{{ $item->pengguna }}</td>
                                        <td>
                                            <a href="" class="btn btn-sm btn-primary">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <a href="" class="btn btn-sm btn-danger">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <div class="row mb-2">
                    <div class="col-md">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('admin.pengajuan-peralatan.tambah-inventaris')
    @include('admin.pengajuan-peralatan.tambah-peralatan')

    @push('js')
        <script type="text/javascript">
            var id = 0;
            $('#tambah_peralatan').on('click', function() {
                id++;
                var input = $('#input_peralatan');
                var elements = ['#nama_jenis', '#kode_branch', '#merk', '#type', '#kuantitas', '#harga', '#nama_toko',
                    '#no_telp', '#pengguna', '#moa'
                ]
                var item_peralatan = $('#item_peralatan').clone().attr('id', 'tr' + id).removeClass('d-none')
                $.each(elements, function(index, element) {
                    var field = input.find(element).val()
                    // if (!field) {
                    //     alert('Semua input harus diisi')
                    //     return required;
                    // }
                    item_peralatan.find('#id').text(id)
                    item_peralatan.find('#label_' + element.replace('#', '')).text(field)
                    item_peralatan.find('#' + element.replace('#', '')).val(field)
                });

                // console.log(item_peralatan)
                item_peralatan.find('#remove_btn').attr('onclick', 'remove_item("#tr' + id + '")')
                $('#base_peralatan').append(item_peralatan)
                input.find('#kode_branch').val('')
                input.find('#merk').val('')
                input.find('#type').val('')
                input.find('#kuantitas').val('')
                input.find('#harga').val('')
                input.find('#nama_toko').val('')
                input.find('#no_telp').val('')
                input.find('#pengguna').val('')
                input.find('#moa').val('')

            });

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
