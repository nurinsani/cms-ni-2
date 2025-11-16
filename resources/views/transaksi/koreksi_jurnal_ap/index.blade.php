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
        </div>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
            <label for="pembebananSelect">Pilih Data Pembebanan</label>
            <select class="form-control select2" id="pembebananSelect" style="width: 50%;">
                <option value="">-- Pilih Pembebanan --</option>
                <option value="{{ url('koreksi-jurnal/ap') }}">Koreksi Jurnal AP</option>
                <option value="{{ url('koreksi-jurnal/kp') }}">Koreksi Jurnal KP</option>
            </select>
        </div>
    </div>

    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Kode Branch</th>
                        <th>Kode Transaksi</th>
                        <th>Kode Rekening</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th>Keterangan</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($koreksi_jurnal_ap as $item)
                        <tr>
                            <td>{{ $item->kode_branch }}</td>
                            <td>{{ $item->kode_transaksi }}</td>
                            <td>{{ $item->kode_rekening }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m/d/Y') }}</td>
                            <td>{{ $item->jenis_transaksi }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->debet }}</td>
                            <td>{{ $item->kredit }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn"
                                    data-id="{{ $item->id }}"
                                    data-keterangan="{{ $item->keterangan }}"
                                    data-debet="{{ $item->debet }}"
                                    data-kredit="{{ $item->kredit }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Koreksi Jurnal</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit_id">

                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" id="edit_keterangan" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Debet</label>
                        <input type="number" id="edit_debet" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Kredit</label>
                        <input type="number" id="edit_kredit" class="form-control">
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEdit">Simpan</button>
            </div>

            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.editBtn', function () {
            let id = $(this).data('id');

            $('#edit_id').val(id);
            $('#edit_keterangan').val($(this).data('keterangan'));
            $('#edit_debet').val($(this).data('debet'));
            $('#edit_kredit').val($(this).data('kredit'));

            $('#editModal').modal('show');
        });

        $('#saveEdit').click(function () {

            let id = $('#edit_id').val();
            let formData = {
                _token: "{{ csrf_token() }}",
                keterangan: $('#edit_keterangan').val(),
                debet: $('#edit_debet').val(),
                kredit: $('#edit_kredit').val(),
            };

            $.ajax({
                url: "/koreksi-jurnal/ap/update/" + id,
                type: "POST",
                data: formData,
                success: function(res) {
                    $('#editModal').modal('hide');

                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data berhasil diupdate.",
                        icon: "success",
                        timer: 2000,  // Auto close setelah 2 detik
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(err) {
                    Swal.fire("Error", "Terjadi kesalahan.", "error");
                }
            });
        });

    </script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
@endsection

@push('js')
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih Pembebanan --"
        });
        
        $('#pembebananSelect').on('change', function() {
            var url = $(this).val();
            if (url) {
                window.location.href = url;
            }
        });
    });
    </script>
    @endpush

