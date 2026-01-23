<div class="modal fade" id="modal-peralatan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Pengajuan sss</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
    
                <form action="{{ url('admin/temp-pengajuan') }}" method="post" enctype="multipart/form-data">
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
                                    <label for="" class="form-label">Status Branch</label>
                                    <select name="status_branch" class="form-control">
                                        <option value="" hidden>-- Pilih Status --</option>
                                        <option value="AP">AP</option>
                                        <option value="{{ $area->code_area }}">Wilayah</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Pilih Branch Penempatan</label>
                                    <select id="select-option" class="form-control">
                                        <option value="" hidden>-- Pilih Branch --</option>
                                        <option value="ap">AP</option>
                                        <option value="cabang">Wilayah</option>
                                    </select>
                                </div>
                                
                                <div id="select-ap" class="form-group d-none">
                                    <label for="" class="form-label">Pilih AP</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control select2bs4">
                                        <option value="">-- Pilih AP --</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->kode_branch }}">{{ $item->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div id="select-cabang" class="form-group d-none">
                                    <label for="" class="form-label">Pilih Wilayah</label>
                                    <select name="kode_area" id="kode_area" class="form-control">
                                        <option value="">-- Pilih Wilayah --</option>
                                        <option value="{{ $area->code_area }}">{{ $area->area }}</option>
                                    </select>
                                </div>
                                
                                <div id="select-region" class="form-group d-none">
                                    <label for="" class="form-label">Pilih Region</label>
                                    <select name="kode_region" id="kode_region" class="form-control">
                                        <option value="" hidden>-- Pilih Region --</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->code_region }}">{{ $item->region }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">* pilih salah satu AP atau Cabang, jangan pilih dua2 nya. <br> 
                                        jika ingin pengajuan untuk Wilayah pilih ini!
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Merk</label>
                                    <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" placeholder="Masukan Merk" required>
                                    @error('merk')
                                    <div class="invalid-feedback">
                                        merk maksimal 50 karakter
                                    </div>
                                    @enderror
                                </div>

                            
                                <div class="form-group">
                                    <label for="" class="form-label">Type</label>
                                    <input type="text" name="type" id="type" class="form-control @error('type') is-invalid @enderror" placeholder="Masukan Type" required>
                                    @error('type')
                                    <div class="invalid-feedback">
                                        tipe maksimal 50 karakter
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Kuantitas</label>
                                    <input type="number" name="kuantitas" id="kuantitas" class="form-control @error('kuantitas') is-invalid @enderror" placeholder="Masukan Kuantitas" required>
                                    @error('kuantitas')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="harga" class="form-label">Harga</label>
                                    <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" placeholder="Masukkan harga" required>
                                    @error('harga')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
    
                                <div class="form-group">
                                    <label for="" class="form-label">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="nama_toko" class="form-control @error('nama_toko') is-invalid @enderror" placeholder="Masukan Nama Toko" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">No Telp</label>
                                    <input type="number" name="no_telp" id="no_telp" class="form-control @error('no_telp') is-invalid @enderror" placeholder="Masukan No. Telp"
                                        required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Penguna</label>
                                    <input type="text" name="pengguna" id="pengguna" class="form-control @error('pengguna') is-invalid @enderror"
                                        placeholder="Masukan Nama Pengguna" required>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Upload MOA</label>
                                    <input type="file" name="moa" id="moa" class="form-control" placeholder="Masukan MOA">
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Rekening</label>
                                    <select name="rekening_id" id="" class="form-control select2bs4">
                                        <option value="">-- Pilih Rekening Tujuan --</option>
                                        @foreach ($rekening as $item)
                                        <option value="{{ $item->id }}">{{ $item->no_rek }} - {{ $item->nama_bank }} - {{ $item->atas_nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-red">*isi jika ingin menggunakan rekening lain, jika tidak diisi maka akan menggunakan rekening pembuat</small>
                                </div>

                                <div class="form-group">
                                    <label for="" class="form-label">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" id="keterangan" cols="5" rows="3" placeholder="UM Pembelian ..." required></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn-sm btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>