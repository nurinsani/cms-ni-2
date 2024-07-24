@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('it.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('it.pengajuan.index') }}">Pengajuan</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ url('admin-head/pengajuan') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-12">
                        <a href="{{ url('atasan-akunting/pengajuan') }}" class="btn btn-sm btn-danger mb-3 mr-1">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-sm btn-success mb-3" data-toggle="modal"
                            data-target="#modal-peralatan">
                            <i class="fas fa-plus"></i> Tambah Pengajuan
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
                                        <td>{{ $item->ap->unit ?? $item->area->area}}</td>
                                        <td>{{ $item->merk }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->kuantitas }}</td>
                                        <td>Rp. {{ number_format($item->harga) }}</td>
                                        <td>{{ $item->nama_toko }}</td>
                                        <td>{{ $item->no_telp }}</td>
                                        <td>{{ $item->pengguna }}</td>
                                        <td>
                                            <a href="{{ url('it/temp-pengajuan/'. $item->id) }}" onclick="hapus_data('#delete_form{{ $item->id }}')" class="btn btn-sm btn-danger">
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
                <div class="row mb-2">
                    <div class="col-md">
                        <button type="submit" class="btn btn-primary">Ajukan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- tambah modal --}}
    <div class="modal fade" id="modal-peralatan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Peralatan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
    
                <form action="{{ url('admin-head/temp-pengajuan') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Peralatan</label>
                                    <select id="nama_peralatan" class="form-control select2bs4" required>
                                        <option value="" hidden>-- Pilih Peralatan --</option>
                                        @foreach ($param_peralatan as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Jenis Peralatan</label>
                                    <select name="jenis_peralatan_id" class="form-control jenis_peralatan" id="nama_jenis"
                                        class="form-control">
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Pilih AP</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control">
                                        @foreach ($branch as $item)
                                            <option value="" hidden>-- Pilih AP --</option>
                                            <option value="{{ $item->kode_branch }}">{{ $item->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Pilih Cabang</label>
                                    <select name="kode_area" class="form-control">
                                        @foreach ($branch as $item)
                                            <option value="" hidden>-- Pilih Cabangs --</option>
                                            @endforeach
                                        <option value="{{ $item->code_area }}">{{ $item->area }}</option>
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Merk</label>
                                    <input type="text" name="merk" class="form-control" placeholder="Masukan Merk" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Type</label>
                                    <input type="text" name="type" id="type" class="form-control" placeholder="Masukan Type" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Kuantitas</label>
                                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" placeholder="Masukan Kuantitas" required>
                                </div>
                
                                <div class="form-group">
                                    <label for="" class="form-label">Harga</label>
                                    <input type="number" name="harga" id="harga" class="form-control" placeholder="Masukan harga" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="nama_toko" class="form-control" placeholder="Masukan Nama Toko" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">No Telp</label>
                                    <input type="number" name="no_telp" id="no_telp" class="form-control" placeholder="Masukan No. Telp"
                                        required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Penguna</label>
                                    <input type="text" name="pengguna" id="pengguna" class="form-control"
                                        placeholder="Masukan Nama Pengguna" required>
                                </div>

                            </div>
                        </div>
                    </div>
    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- edit modal --}}
    @foreach ($temp_pengajuan as $item => $value)
    <div class="modal fade" id="modal-peralatan-hapus-{{ $value->id }}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Peralatan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
    
                <form action="{{ url('admin/temp-pengajuan') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Peralatan</label>
                                    <select id="nama_peralatan" class="form-control select2bs4" required>
                                        <option value="" hidden>-- Pilih Peralatan --</option>
                                        @foreach ($param_peralatan as $data)
                                            <option value="{{ $data->id }}"
                                                @if ($data->id == $value->jenis_peralatan->param_peralatan->id)
                                                selected
                                                @endif
                                                >{{ $data->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Jenis Peralatan</label>
                                    <select name="jenis_peralatan_id" class="form-control jenis_peralatan" id="nama_jenis"
                                        class="form-control">   
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Nama AP</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control">
                                        @foreach ($branch as $item)
                                            <option value="" hidden>-- Pilih AP --</option>
                                            <option value="{{ $item->kode_branch }}">{{ $item->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Merk</label>
                                    <input type="text" name="merk" class="form-control" value="{{ $value->merk }}" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Type</label>
                                    <input type="text" name="type" id="type" class="form-control" placeholder="Masukan Type" value="{{ $value->type }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Kuantitas</label>
                                    <input type="number" name="kuantitas" id="kuantitas" class="form-control" placeholder="Masukan Kuantitas" value="{{ $value->kuantitas }}" required>
                                </div>
                
                                <div class="form-group">
                                    <label for="" class="form-label">Harga</label>
                                    <input type="number" name="harga" id="harga" class="form-control" placeholder="Masukan harga" value="{{ $value->harga }}" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="nama_toko" class="form-control" placeholder="Masukan Nama Toko" value="{{ $value->nama_toko }}" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">No Telp</label>
                                    <input type="number" name="no_telp" id="no_telp" class="form-control" placeholder="Masukan No. Telp" value="{{ $value->no_telp }}"
                                        required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Penguna</label>
                                    <input type="text" name="pengguna" id="pengguna" class="form-control"
                                        placeholder="Masukan Nama Pengguna" required>
                                </div>

                            </div>
                        </div>
                    </div>
    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        
    @endforeach

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