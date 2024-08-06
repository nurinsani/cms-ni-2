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
    <form action="{{ url('/export-excel-inventaris') }}" method="post">
        @csrf
        <input type="hidden" name="harga_perolehan" id="input_harga_perolehan">
        <input type="hidden" name="peny" id="input_peny">
        <input type="hidden" name="peny_awal" id="input_peny_awal">
        <input type="hidden" name="peny_jalan" id="input_peny_jalan">
        <input type="hidden" name="penyusutan" id="input_penyusutan">
        <input type="hidden" name="total_nilai_buku" id="input_total_nilai_buku">
        <button class="btn btn-sm btn-success my-2">Export</button>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="" class="form-label">Pilih Wilayah</label>
                            <select name="area" id="area" class="form-control">
                                <option value="">-- Pilih wilayah --</option>
                                @foreach ($area as $row)
                                    <option value="{{ $row }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-sm table-responsive">
                <table id="table" class="table table-bordered table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Jenis</th>
                            <th>Merk</th>
                            <th>Type</th>
                            <th>Keterangan</th>
                            <th>Tgl Perolehan</th>
                            <th>Qty</th>
                            <th>Harga Perolehan</th>
                            <th>Umur</th>
                            <th>Peny. Bulan</th>
                            <th>Akum. Peny. Awal</th>
                            <th>Akum. Peny. Berjalan</th>
                            <th>Akum. Penyusutan</th>
                            <th>Nilai Buku</th>
                            <th>Peny. Ke</th>
                            <th>Pengguna</th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                            <th class="hidden-column"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventaris as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->kode_peralatan }}</td>
                                {{-- <td>{!! DNS2D::getBarcodeHTML("$item->kode_peralatan", 'QRCODE', 2, 2) !!}</td> --}}
                                <td>{{ $item->jenis }}</td>
                                <td>{{ $item->merk }}</td>
                                <td>{{ $item->type }}</td>
                                <td>
                                    @if ($item->keterangan == null)
                                        -
                                    @else
                                        {{ $item->keterangan }}
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_perolehan }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp. {{ number_format($item->harga_perolehan) }}</td>
                                <td>48</td>
                                <td>Rp. {{ number_format($item->harga_perolehan / 48) }}</td>
                                <td>Rp. {{ number_format($item->akumulasi_penyusutan_awal()) }}</td>
                                <td>Rp. {{ number_format($item->akumulasi_penyusutan()) }}</td>
                                <td>Rp. {{ number_format(($item->harga_perolehan / 48) * $item->susut->count()) }}</td>
                                <td>Rp.
                                    {{ number_format($item->akumulasi_penyusutan_awal() + $item->total_penyusutan_awal) }}
                                </td>
                                <td>
                                    {{ count($item->susut) + $item->penyusutan_ke }}
                                </td>
                                <td>
                                    @if ($item->pengguna == null)
                                        -
                                    @else
                                        {{ $item->pengguna }}
                                    @endif
                                </td>
                                <td>{{ $item->area }}</td>
                                <td class="hidden-column">{{ ($item->harga_perolehan) }}</td>
                                <td class="hidden-column">{{ ($item->harga_perolehan / 48) }}</td>
                                <td class="hidden-column">
                                    {{ $item->akumulasi_penyusutan_awal() }}</td>
                                <td class="hidden-column">
                                    {{ $item->akumulasi_penyusutan() }}</td>
                                <td class="hidden-column">
                                    {{ intval(($item->harga_perolehan / 48) * $item->susut->count()) }}
                                </td>
                                <td class="hidden-column">
                                    {{ intval($item->harga_perolehan - ($item->harga_perolehan / 48) * $item->susut->count()) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <td colspan="8" class="text-center"><strong>TOTAL</strong></td>
                        <td id="total_harga_perolehan"></td>
                        <td></td>
                        <td id="total_peny"></td>
                        <td id="total_peny_awal"></td>
                        <td id="total_peny_berjalan"></td>
                        <td id="total_penyusutan"></td>
                        <td id="total_nilai_buku"></td>
                        <td colspan="9"></td>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            var table = $('#table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "columnDefs": [{
                    "targets": [17, 18, 19, 20, 21, 22, 23],
                    "visible": false,
                    "searchable": true
                }]
            });

            function updateFooter() {
                let harga_perolehan = 0;
                let peny = 0;
                let peny_awal = 0;
                let peny_berjalan = 0;
                let penyusutan = 0;
                let buku = 0;

                table.rows({
                    search: 'applied'
                }).every(function() {
                    var data = this.data();

                    harga_perolehan += parseFloat(data[18]) || 0;
                    peny += parseFloat(data[19]) || 0;
                    peny_awal += parseFloat(data[20]) || 0;
                    peny_berjalan += parseFloat(data[21]) || 0;
                    penyusutan += parseFloat(data[22]) || 0;
                    buku += parseFloat(data[23]) || 0;
                });

                $('#total_harga_perolehan').html('<strong> Rp.' + harga_perolehan.toLocaleString() + '<total>');
                $('#total_peny').html('<strong> Rp.' + peny.toLocaleString() + '<total>');
                $('#total_peny_awal').html('<strong> Rp.' + peny_awal.toLocaleString() + '<total>');
                $('#total_peny_berjalan').html('<strong> Rp.' + peny_berjalan.toLocaleString() + '<total>');
                $('#total_penyusutan').html('<strong> Rp.' + penyusutan.toLocaleString() + '<total>');
                $('#total_nilai_buku').html('<strong> Rp.' + buku.toLocaleString() + '<total>');

                $('#input_harga_perolehan').val(harga_perolehan)
                $('#input_peny').val(peny)
                $('#input_peny_awal').val(peny_awal)
                $('#input_peny_jalan').val(peny_berjalan)
                $('#input_penyusutan').val(penyusutan)
                $('#input_total_nilai_buku').val(buku)
            }

            updateFooter();

            $('#area').on('change', function() {
                var searchTerm = $(this).val();

                table.column(17).search(searchTerm).draw()

                updateFooter();
            });

            table.on('draw', function() {
                updateFooter();
            });
        });
    </script>
@endpush
