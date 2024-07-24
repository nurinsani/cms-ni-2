@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <a class="btn btn-success mb-3" href="javascript:void(0)" id="tambahDataRekening"> Tambah Data Baru</a>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Rekening</th>
                            <th>Nama Bank</th>
                            <th>Atas Nama</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="RekeningForm" name="RekeningForm" class="form-horizontal">
                        <input type="hidden" name="rekening_id" id="rekening_id">
                        <div class="form-group">
                            <label for="name" class="col-sm control-label">No Rekening</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="no_rek" name="no_rek"
                                    placeholder="Masukkan No Rekening" value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm control-label">Nama Bank</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank" required=""
                                    placeholder="Nama Bank">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm control-label">Atas Nama</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="atas_nama" name="atas_nama" required=""
                                    placeholder="Atas Nama">
                            </div>
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="simpan" value="create">Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // render data rekening
            var table = $('.data-table').DataTable({
                processing: true,
                serverside: true,
                ajax: "{{ route('admin.rekening.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'no_rek',
                        name: 'no_rek'
                    },
                    {
                        data: 'nama_bank',
                        name: 'nama_bank'
                    },
                    {
                        data: 'atas_nama',
                        name: 'atas_nama'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            })

            // modal tambah
            $('#tambahDataRekening').click(function() {
                $('#simpan').val("create-product");
                $('#rekening_id').val('');
                $('#RekeningForm').trigger("reset");
                $('#modelHeading').html("Tambah Data Rekening");
                $('#ajaxModel').modal('show');
            });

            // edit
            $('body').on('click', '.editProduct', function() {
                var rekening_id = $(this).data('id');
                $.get("{{ route('admin.rekening.index') }}" + '/' + rekening_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit Data Rekening");
                    $('#simpan').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#rekening_id').val(data.id);
                    $('#no_rek').val(data.no_rek);
                    $('#nama_bank').val(data.nama_bank);
                    $('#atas_nama').val(data.atas_nama);
                })
            });

            $('#simpan').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');

                $.ajax({
                    data: $('#RekeningForm').serialize(),
                    url: "{{ route('admin.rekening.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {

                        $('#RekeningForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        location.reload();
                        table.draw();

                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#simpan').html('Simpan Data');
                    }
                });
            });

            $('body').on('click', '.deleteProduct', function() {

                var rekening_id = $(this).data("id");
                confirm("Kamu yakin mau hapus data ini?");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.rekening.store') }}" + '/' + rekening_id,
                    success: function(data) {
                        location.reload();
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>
@endsection
