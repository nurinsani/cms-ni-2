@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route($rolePrefix . '.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-id-badge mr-1"></i> Data Parameter Jabatan Pembebanan
            </h3>
            <div class="card-tools ml-auto">
                <button type="button" class="btn btn-sm btn-primary shadow-sm" id="btnTambahJabatan">
                    <i class="fas fa-plus mr-1"></i> Tambah Jabatan Baru
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0" id="tableParamJabatan" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Nama Jabatan / Posisi</th>
                            <th class="text-center" style="width: 130px;">Kode Pembebanan</th>
                            <th>Kategori / Group Wilayah</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH & EDIT JABATAN --}}
    <div class="modal fade" id="modalJabatan" tabindex="-1" role="dialog" aria-labelledby="modalJabatanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalJabatanLabel">Form Parameter Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formJabatan">
                    @csrf
                    <input type="hidden" name="jabatan_id" id="jabatan_id">

                    <div class="modal-body">
                        <div id="modalAlertError" class="alert alert-danger d-none shadow-sm"></div>

                        <div class="form-group">
                            <label for="nama_jabatan" class="form-label font-weight-bold">Nama Jabatan / Posisi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_jabatan" name="nama_jabatan"
                                placeholder="Contoh: AL Jabar 1 / MM Genuk 2" required>
                        </div>

                        <div class="form-group">
                            <label for="kode_pembebanan" class="form-label font-weight-bold">Kode Pembebanan (Status Branch) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_pembebanan" name="kode_pembebanan"
                                placeholder="Contoh: 1101 / 2200 / 9999" required>
                            <small class="form-text text-muted">Kode cabang/area/region penanggung beban penyusutan (misal: 9999 = Pusat, 1101 = Jabar 1).</small>
                        </div>

                        <div class="form-group">
                            <label for="kategori" class="form-label font-weight-bold">Kategori / Group Wilayah</label>
                            <input type="text" class="form-control" id="kategori" name="kategori" list="kategoriOptions"
                                placeholder="Contoh: Area Leader (AL) - Jawa Barat">
                            <datalist id="kategoriOptions">
                                <option value="Kantor Pusat / Regional">
                                <option value="Branch Manager (BM) & Area Manager (AM)">
                                <option value="Area Leader (AL) - Jawa Barat">
                                <option value="Area Leader (AL) - Jawa Tengah">
                                <option value="Marketing Manager (MM) & Unit">
                            </datalist>
                        </div>

                        <div class="form-group">
                            <label for="status_aktif" class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                            <select name="status_aktif" id="status_aktif" class="form-control" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="keterangan" class="form-label font-weight-bold">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanJabatan">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var baseUrl = "{{ url($rolePrefix . '/param-jabatan') }}";

            var table = $('#tableParamJabatan').DataTable({
                processing: true,
                serverSide: true,
                ajax: baseUrl,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center align-middle', orderable: false, searchable: false },
                    { data: 'nama_jabatan', name: 'nama_jabatan', className: 'align-middle font-weight-bold' },
                    { data: 'kode_pembebanan', name: 'kode_pembebanan', className: 'text-center align-middle' },
                    { data: 'kategori', name: 'kategori', className: 'align-middle' },
                    { data: 'status_aktif', name: 'status_aktif', className: 'text-center align-middle' },
                    { data: 'keterangan', name: 'keterangan', className: 'align-middle' },
                    { data: 'action', name: 'action', className: 'text-center align-middle', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']]
            });

            // Klik Tombol Tambah
            $('#btnTambahJabatan').on('click', function() {
                $('#formJabatan')[0].reset();
                $('#jabatan_id').val('');
                $('#modalAlertError').addClass('d-none').empty();
                $('#modalJabatanLabel').text('Tambah Parameter Jabatan');
                $('#status_aktif').val('aktif');
                $('#modalJabatan').modal('show');
            });

            // Submit Form via AJAX
            $('#formJabatan').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#btnSimpanJabatan');
                var originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
                $('#modalAlertError').addClass('d-none').empty();

                $.ajax({
                    url: baseUrl,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalJabatan').modal('hide');
                        table.ajax.reload(null, false);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        var errorList = '<ul class="mb-0 pl-3">';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, val) {
                                $.each(val, function(i, msg) {
                                    errorList += '<li>' + msg + '</li>';
                                });
                            });
                        } else {
                            errorList += '<li>Terjadi kesalahan saat menyimpan data.</li>';
                        }
                        errorList += '</ul>';
                        $('#modalAlertError').html(errorList).removeClass('d-none');
                    },
                    complete: function(xhr, status) {
                        if (status === 'success') {
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    }
                });
            });

            // Klik Edit
            $('body').on('click', '.editJabatan', function() {
                var id = $(this).data('id');
                $('#formJabatan')[0].reset();
                $('#modalAlertError').addClass('d-none').empty();
                $('#modalJabatanLabel').text('Edit Parameter Jabatan');

                $.get(baseUrl + '/' + id + '/edit', function(data) {
                    $('#jabatan_id').val(data.id);
                    $('#nama_jabatan').val(data.nama_jabatan);
                    $('#kode_pembebanan').val(data.kode_pembebanan);
                    $('#kategori').val(data.kategori);
                    $('#status_aktif').val(data.status_aktif);
                    $('#keterangan').val(data.keterangan);
                    $('#modalJabatan').modal('show');
                }).fail(function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Gagal memuat data jabatan.', 'error');
                    } else {
                        alert('Gagal memuat data jabatan.');
                    }
                });
            });

            // Klik Hapus
            $('body').on('click', '.hapusJabatan', function() {
                var id = $(this).data('id');

                var doDelete = function() {
                    $.ajax({
                        url: baseUrl + '/' + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    text: response.message || 'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', 'Gagal menghapus data.', 'error');
                            } else {
                                alert('Gagal menghapus data.');
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Apakah Anda yakin ingin menghapus parameter jabatan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDelete();
                        }
                    });
                } else {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        doDelete();
                    }
                }
            });
        });
    </script>
@endpush

