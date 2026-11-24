    <div class="modal fade" id="modal-inventaris">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Inventaris</h4>
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
                                    <label for="" class="form-label">Pilih Branch</label>
                                    <select id="select-option" class="form-control">
                                        <option value="" hidden>-- Pilih Branch --</option>
                                        <option value="ap">Unit</option>
                                        <option value="cabang">Area</option>
                                    </select>
                                </div>
                                
                                <div id="select-ap" class="form-group d-none">
                                    <label for="" class="form-label">Pilih Unit</label>
                                    <select name="kode_branch" id="kode_branch" class="form-control select2bs4">
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->kode_branch }}">{{ $item->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div id="select-cabang" class="form-group d-none">
                                    <label for="" class="form-label">Pilih Area</label>
                                    <select name="kode_area" id="kode_area" class="form-control">
                                        <option value="">-- Pilih Area --</option>
                                        @foreach ($branch as $item)
                                        @endforeach
                                        <option value="{{ $item->code_area }}">{{ $item->area }}</option>
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
                                    <label for="kode_user" class="form-label">Jabatan / Posisi Pembebanan</label>
                                    <select name="kode_user" id="kode_user" class="form-control select2bs4">
                                        <option value="">-- Pilih Jabatan / Posisi --</option>
                                        @php
                                            $listJabatan = isset($param_jabatan) && $param_jabatan->isNotEmpty() 
                                                ? $param_jabatan 
                                                : \App\Models\ParamJabatan::aktif()->orderBy('kategori')->orderBy('nama_jabatan')->get();
                                            $groupedList = $listJabatan->groupBy('kategori');
                                        @endphp
                                        @foreach ($groupedList as $kategori => $items)
                                            <optgroup label="{{ $kategori ?: 'Lainnya' }}">
                                                @foreach ($items as $jbt)
                                                    <option value="{{ $jbt->kode_pembebanan }}">{{ $jbt->nama_jabatan }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Merk</label>
                                    <input type="text" name="merk" id="merk" class="form-control" placeholder="Contoh: Lenovo" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Type</label>
                                    <input type="text" name="type" id="type" class="form-control" placeholder="Contoh: IdeaPad Slim 3" required>
                                </div>
    
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Kuantitas</label>
                                            <input type="number" name="kuantitas" id="kuantitas" class="form-control" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="harga" class="form-label">Harga</label>
        
                                            <!-- input tampilan -->
                                            <input type="text" id="harga_view" class="form-control" placeholder="Rp 0">
        
                                            <!-- nilai asli yang dikirim -->
                                            <input type="hidden" name="harga" id="harga">
        
                                            @error('harga')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="nama_toko" class="form-control" placeholder="" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">No Telp</label>
                                    <input type="number" name="no_telp" id="no_telp" class="form-control" placeholder=""
                                        required>
                                </div>
    
                                <div class="form-group">
                                    <label for="pengguna" class="form-label">Nama Pengguna (PIC / Pemakai)</label>
                                    <input type="text" name="pengguna" id="pengguna" class="form-control"
                                        placeholder="Contoh: Nama staf / penanggung jawab" required>
                                </div>
    
                                <div class="form-group">
                                    <label for="" class="form-label">Upload MOA</label>
                                    <input type="file" name="moa" id="moa" class="form-control" placeholder="">
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
                                    <textarea name="keterangan" class="form-control" id="keterangan" cols="5" rows="3" placeholder="Contoh: Pembelian peralatan kantor" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>