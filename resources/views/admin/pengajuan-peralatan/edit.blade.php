@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.pengajuan.index') }}">Pengjuan</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pengajuan.update', $pengajuan->id) }}" method="post">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Cabang</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2bs4 @error('kode_branch') is-invalid @enderror" name="kode_branch">
                            <option value="" hidden>-- Pilih Nama Ap --</option>
                            @foreach ($branch as $data)
                            <option value="{{ $data->kode_branch }}" {{ old('kode_branch', $pengajuan->kode_branch) == $data->kode_branch ? 'selected' : '' }}>{{ $data->unit }}</option>
                            @endforeach
                        </select>
                        @error('kode_branch') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
    
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Keterangan</label>
                    </div>
                    <div class="col-md-9">
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" cols="10" rows="5" placeholder="Masukan Keterangan">{{old('keterangan', $pengajuan->keterangan)}}</textarea>
                        @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nama Jenis</th>
                                <th scope="col">Kuantitas</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Nama Toko</th>
                                <th scope="col">No Telp</th>
                                <th scope="col">Pengguna</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="base_peralatan">
                            <tr id="item_peralatan" class="d-none">
                                <td id="number"></td>
                                <td>
                                    <p id="label_nama_jenis">Lorem</p>
                                    <input id="nama_jenis" type="hidden" name="nama_jenis[]">
                                </td>
                                <td>
                                    <p id="label_kuantitas">Lorem</p>
                                    <input id="kuantitas" type="hidden" name="kuantitas[]">
                                </td>
                                <td>
                                    <p id="label_harga">Lorem</p>
                                    <input id="harga" type="hidden" name="harga[]">
                                </td>
                                <td>
                                    <p id="label_nama_toko">Lorem</p>
                                    <input id="nama_toko" type="hidden" name="nama_toko[]">
                                </td>
                                <td>
                                    <p id="label_no_telp">Lorem</p>
                                    <input id="no_telp" type="hidden" name="no_telp[]">
                                </td>
                                <td>
                                    <p id="label_pengguna">Lorem</p>
                                    <input id="pengguna" type="hidden" name="pengguna[]">
                                </td>
                                <td>
                                    <button id="remove_btn" type="button" class="btn btn-sm btn-danger">Hapus</button>
                                </td>
                            </tr>
                            @php
                                $last_number = 0;    
                            @endphp
                            @foreach ($pengajuan->pengajuan_details as $item)
                            @php
                                $last_number = $loop->iteration;  
                            @endphp
                            <tr id="data_tr{{$item->id}}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <p id="label_nama_jenis">{{ $item->nama_jenis }}</p>
                                    <input id="nama_jenis" type="hidden" name="nama_jenis[]" value="{{ $item->nama_jenis }}">
                                </td>
                                <td>
                                    <p id="label_kuantitas">{{ $item->kuantitas }}</p>
                                    <input id="kuantitas" type="hidden" name="kuantitas[]" value="{{ $item->kuantitas }}">
                                </td>
                                <td>
                                    <p id="label_harga">Rp. {{ number_format($item->harga) }}</p>
                                    <input id="harga" type="hidden" name="harga[]" value="{{ $item->harga }}">
                                </td>
                                <td>
                                    <p id="label_nama_toko">{{ $item->nama_toko }}</p>
                                    <input id="nama_toko" type="hidden" name="nama_toko[]" value="{{ $item->nama_toko }}">
                                </td>
                                <td>
                                    <p id="label_no_telp">{{ $item->no_telp }}</p>
                                    <input id="no_telp" type="hidden" name="no_telp[]" value="{{ $item->no_telp }}">
                                </td>
                                <td>
                                    <p id="label_pengguna">{{ $item->pengguna }}</p>
                                    <input id="pengguna" type="hidden" name="pengguna[]" value="{{ $item->pengguna }}">
                                </td>
                                <td>
                                    <button id="remove_btn" type="button" onclick="remove_item('#data_tr{{$item->id}}')" class="btn btn-sm btn-danger">Hapus</button>
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
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-default">
                        Tambah Peralatan
                      </button>
                </div>
            </div>
        </form>
    </div>
  </div>
  
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Peralatan</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" id="input_peralatan">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Peralatan</label>
                </div>
                <div class="col-md-8">
                    <select id="nama_peralatan" class="form-control select2bs4" required>
                        <option value="" hidden>-- Pilih Peralatan --</option>
                        @foreach ($param_peralatan as $data)
                        <option value="{{ $data->id }}">{{ $data->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Jenis Peralatan</label>
                </div>
                <div class="col-md-8">
                    <select name="jenis_peralatan_id" class="form-control jenis_peralatan" id="nama_jenis" class="form-control">
                    </select>
                </div>
            </div>
        
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Kuantitas</label>
                </div>
                <div class="col-md-8">
                    <input type="number" id="kuantitas" class="form-control" placeholder="Masukan Kuantitas">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Harga</label>
                </div>
                <div class="col-md-8">
                    <input type="number" id="harga" class="form-control" placeholder="Masukan harga">
                </div>
            </div>
        
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Nama Toko</label>
                </div>
                <div class="col-md-8">
                    <input type="text" id="nama_toko" class="form-control" placeholder="Masukan Nama Toko">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">No Telp</label>
                </div>
                <div class="col-md-8">
                    <input type="text" id="no_telp" class="form-control" placeholder="Masukan No Telp">
                </div>
            </div>
        
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="" class="form-label">Penguna</label>
                </div>
                <div class="col-md-8">
                    <input type="text" id="pengguna" class="form-control" placeholder="Masukan Nama Pengguna">
                </div>
            </div>
        </div>
        
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Kembali</button>
          <button type="button" class="btn btn-primary" id="tambah_peralatan" data-dismiss="modal">Tambah</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

@push('js')
<script type="text/javascript">
    var id = '{{ $last_number }}';
    $('#tambah_peralatan').on('click', function(){
        id++;
        var input = $('#input_peralatan');
        var elements = ['#nama_jenis', '#kuantitas','#harga', '#nama_toko', '#no_telp', '#pengguna']
        var item_peralatan = $('#item_peralatan').clone().attr('id', 'tr' + id).removeClass('d-none')
        $.each(elements, function(index, element) {
            var field = input.find(element).val()
            if (!field) {
                alert('Semua input harus diisi')
            }
            item_peralatan.find('#number').text(id)
            item_peralatan.find('#label_' + element.replace('#', '')).text(field)
            item_peralatan.find('#' + element.replace('#', '')).val(field)
        });
        
        // console.log(item_peralatan)
        item_peralatan.find('#remove_btn').attr('onclick', 'remove_item("#tr'+ id +'")')
        $('#base_peralatan').append(item_peralatan)
        input.find('#kuantitas').val('')
        input.find('#harga').val('')
        input.find('#nama_toko').val('')
        input.find('#no_telp').val('')
        input.find('#pengguna').val('')
    });

    $(document).ready(function() {
    $('#nama_peralatan').on('change', function() {
      var jenis_peralatan_id = $(this).val()
      if(jenis_peralatan_id) {
        $.ajax({
          url: '/jenis-peralatan-dropdown/'+jenis_peralatan_id,
          type: "GET",
          data: {"_token":"{{ csrf_token() }}"},
          dataType: "json",
          success:function(data)
          {
            // console.log(data)
            if(data) {
              $('.jenis_peralatan').empty()
              $('.jenis_peralatan').append('<option hidden>-- Pilih Jenis Peralatan --</option>')
              $.each(data, function(key, jenis_peralatan){
                // console.log()
                $('.jenis_peralatan').append('<option value="'+ jenis_peralatan.nama_jenis +'">' + jenis_peralatan.nama_jenis+ '</option>')
                // console.log(data)
              })
            } else{
              $('.jenis_peralatan').empty()
            }
          }
        })
      } else{
        $('.jenis_peralatan').empty()
      }
    })
  })

    function remove_item(tr_id) {
        $(tr_id).remove()
    }
</script>
@endpush

@endsection