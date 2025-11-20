<!-- Modal Tambah Stok Opname -->
<div class="modal fade" id="modalTambahStokOpname" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok Opname</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTambahStokOpname">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Inventaris</label>
                        <select name="inventaris_id" class="form-control" required>
                            <option value="">-- Pilih Inventaris --</option>
                            @foreach($inventaris as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->kode_peralatan }} - {{ $inv->jenis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Keadaan</label>
                        <select name="keadaan" class="form-control" required>
                            <option value="">-- Pilih Keadaan --</option>
                            <option value="ada">Ada</option>
                            <option value="tidak">Tidak Ada</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-control" required>
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="beroprasi">Beroprasi</option>
                            <option value="tidak_berfungsi">Tidak Berfungsi</option>
                            <option value="rusak">Rusak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>