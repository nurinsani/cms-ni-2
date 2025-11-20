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
    <div class="card">
        <div class="card-body">
            <a href="{{ url('kartu-stok-all') }}" 
            target="_blank" 
            class="btn btn-success btn-sm mr-2">
                <i class="fas fa-print mr-2"></i>
                Cetak Kartu Stok
            </a>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahStokOpname">
                <i class="fas fa-plus mr-2"></i>
                Tambah Stok Opname
            </button>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example1" class="table table-sm table-bordered table-hover text-nowrap" style="font-size: 14px; width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Jenis</th>
                            <th>Tgl Perolehan</th>
                            <th>Qty</th>
                            <th>Harga Perolehan</th>
                            <th>Umur</th>
                            <th>Penyusutan Bulan</th>
                            <th>Akm Penyusutan Berjalan</th>
                            <th>Akm Penyusutan</th>
                            <th>Nilai Buku</th>
                            <th>Peny Ke</th>
                            <th>Keadaan</th>
                            <th>Kondisi</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach ($so as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->inventaris->kode_peralatan }}</td>
                                <td>{{ $item->inventaris->jenis }}</td>
                                <td>{{ $item->inventaris->tanggal_perolehan }}</td>
                                <td align="center">{{ $item->inventaris->jumlah }}</td>
                                <td>Rp. {{ number_format($item->inventaris->harga_perolehan, 0, ',', '.') }}</td>
                                <td align="center">{{ $item->inventaris->umur }}</td>
                                <td>Rp. {{ number_format($item->penyusutan_bulan(), 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($item->akumulasi_penyusutan(), 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($item->akumulasi_penyusutan_berjalan(), 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($item->nilai_buku(), 0, ',', '.') }}</td>
                                <td>{{ $item->penyusutan_ke() }}</td>
                                <td>
                                    @if($item->keadaan == 'ada')
                                        <span class="badge badge-success">Ada</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Ada</span>
                                    @endif
                                </td>
                                <td>{{ strtolower($item->kondisi) }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" 
                                            data-id="{{ $item->id }}"
                                            data-inventaris="{{ $item->inventaris_id }}"
                                            data-tanggal="{{ $item->tanggal_opname }}"
                                            data-kondisi="{{ $item->kondisi }}"
                                            data-keadaan="{{ $item->keadaan }}"
                                            data-keterangan="{{ $item->keterangan }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('stok_opname.form_tambah')

    @include('stok_opname.form_edit')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Handle form submit
        $('#formTambahStokOpname').on('submit', function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            let submitBtn = $(this).find('button[type="submit"]');
            
            // Disable submit button
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
            
            $.ajax({
                url: "{{ route('stok-opname.store') }}", // Sesuaikan dengan route Anda
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Close modal
                    $('#modalTambahStokOpname').modal('hide');
                    
                    // Reset form
                    $('#formTambahStokOpname')[0].reset();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload table atau tambah row baru
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan!';
                    
                    if (xhr.status === 422) {
                        // Validation error
                        let errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMessage
                    });
                },
                complete: function() {
                    // Enable submit button
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan');
                }
            });
        });

        // Handle Edit Button Click
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var inventaris = $(this).data('inventaris');
            var kondisi = $(this).data('kondisi');
            var keadaan = $(this).data('keadaan');
            var keterangan = $(this).data('keterangan');
            
            // Fill form
            $('#edit_id').val(id);
            $('#edit_inventaris_id').val(inventaris);
            $('#edit_kondisi').val(kondisi);
            $('#edit_keadaan').val(keadaan);
            $('#edit_keterangan').val(keterangan);
            
            // Show modal
            $('#modalEditStokOpname').modal('show');
        });
        
        // Handle Edit Form Submit
        $('#formEditStokOpname').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            var id = $('#edit_id').val();
            var formData = $(this).serialize();
            var submitBtn = $(this).find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Mengupdate...');
            
            $.ajax({
                url: '{{ url("stok-opname") }}/' + id,
                type: 'POST',
                data: formData + '&_method=PUT',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#modalEditStokOpname').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Data berhasil diupdate',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'Terjadi kesalahan!';
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).map(function(err) {
                            return err.join('<br>');
                        }).join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMessage
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Update');
                }
            });
        });
        
        // Reset form when modal is closed
        $('#modalTambahStokOpname').on('hidden.bs.modal', function() {
            $('#formTambahStokOpname')[0].reset();
        });

        $('#modalEditStokOpname').on('hidden.bs.modal', function() {
            $('#formEditStokOpname')[0].reset();
        });
    });
    </script>
@endsection

