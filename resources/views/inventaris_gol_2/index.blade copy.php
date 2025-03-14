@extends('layouts.main')

@section('head-content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.2/css/buttons.dataTables.min.css">
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
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Inventaris dan Penyusutan</h3>
        </div>
        <div class="card-body">
            <form id="filter-form" class="form-inline mb-4">
                <div class="form-group">
                    <label for="kode_branch" class="mr-2">Pilih Branch:</label>
                    <select name="kode_branch" id="kode_branch" class="form-control">
                        <option value="">-- Semua Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->kode_branch }}">{{ $branch->kode_branch }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            
            <div class="table-responsive">
                <table id="inventaris-table" class="table table-xs table-bordered table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th>Kode Branch</th>
                            <th>Kode Peralatan</th>
                            <th>Jenis</th>
                            <th>Type</th>
                            <th>Keterangan</th>
                            <th>Tanggal Perolehan</th>
                            <th>Jumlah</th>
                            <th>Harga Perolehan</th>
                            <th>Umur</th>
                            <th>Penyusutan per Bulan</th>
                            <th>Total Penyusutan Awal</th>
                            <th>Januari</th>
                            <th>Februari</th>
                            <th>Maret</th>
                            <th>April</th>
                            <th>Mei</th>
                            <th>Juni</th>
                            <th>Juli</th>
                            <th>Agustus</th>
                            <th>September</th>
                            <th>Oktober</th>
                            <th>November</th>
                            <th>Desember</th>
                            <th>Total Penyusutan Berjalan</th>
                            <th>Akumulasi Penyusutan</th>
                            <th>Nilai Buku Terakhir</th>
                            <th>Penyusutan Ke</th>
                            <th>Pengguna</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="7">Total</th>
                            <th id="total-harga">0</th>
                            <th id="total-umur">0</th>
                            <th id="total-penyusutan-perbulan">0</th>
                            <th id="total-penyusutan-awal">0</th>
                            <th id="total-januari">0</th>
                            <th id="total-februari">0</th>
                            <th id="total-maret">0</th>
                            <th id="total-april">0</th>
                            <th id="total-mei">0</th>
                            <th id="total-juni">0</th>
                            <th id="total-juli">0</th>
                            <th id="total-agustus">0</th>
                            <th id="total-september">0</th>
                            <th id="total-oktober">0</th>
                            <th id="total-november">0</th>
                            <th id="total-desember">0</th>
                            <th id="total-penyusutan-berjalan">0</th>
                            <th id="total-akumulasi-penyusutan">0</th>
                            <th id="total-nilai-buku-terakhir">0</th>
                            <th id="total-penyusutan-ke">0</th>
                            <th id="total-pengguna">0</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div><!-- /.card-body -->
    </div><!-- /.card -->
</div><!-- /.container-fluid -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        function formatRupiah(number) {
            return parseFloat(number).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // Initialize DataTable
        var table = $('#inventaris-table').DataTable({
            responsive: true
        });

        function fetchData(kodeBranch) {
            $.ajax({
                url: '{{ route('inventaris.fetchData') }}',
                method: 'GET',
                data: { kode_branch: kodeBranch },
                success: function(response) {
                    table.clear().draw(); // Clear existing data

                    var totalJumlah = 0;
                    var totalHarga = 0;
                    var totalUmur = 0;
                    var totalPenyusutanPerbulan = 0;
                    var totalPenyusutanAwal = 0;
                    var totalJanuari = 0;
                    var totalFebruari = 0;
                    var totalMaret = 0;
                    var totalApril = 0;
                    var totalMei = 0;
                    var totalJuni = 0;
                    var totalJuli = 0;
                    var totalAgustus = 0;
                    var totalSeptember = 0;
                    var totalOktober = 0;
                    var totalNovember = 0;
                    var totalDesember = 0;
                    var totalPenyusutanBerjalan = 0;
                    var totalAkumulasiPenyusutan = 0;
                    var totalNilaiBukuTerakhir = 0;
                    var totalPenyusutanKe = 0;
                    var totalPengguna = 0;

                    // Populate table rows
                    response.pivotData.forEach(function(row) {
                        var rowJumlah = parseInt(row.jumlah || 0);
                        var rowHarga = parseFloat(row.harga_perolehan || 0);
                        var rowUmur = parseFloat(row.umur || 0);
                        var rowPenyusutanPerbulan = parseFloat(row.penyusutan_perbulan || 0);
                        var rowPenyusutanAwal = parseFloat(row.total_penyusutan_awal || 0);
                        var rowJanuari = parseFloat(row['2024-01'] || 0);
                        var rowFebruari = parseFloat(row['2024-02'] || 0);
                        var rowMaret = parseFloat(row['2024-03'] || 0);
                        var rowApril = parseFloat(row['2024-04'] || 0);
                        var rowMei = parseFloat(row['2024-05'] || 0);
                        var rowJuni = parseFloat(row['2024-06'] || 0);
                        var rowJuli = parseFloat(row['2024-07'] || 0);
                        var rowAgustus = parseFloat(row['2024-08'] || 0);
                        var rowSeptember = parseFloat(row['2024-09'] || 0);
                        var rowOktober = parseFloat(row['2024-10'] || 0);
                        var rowNovember = parseFloat(row['2024-11'] || 0);
                        var rowDesember = parseFloat(row['2024-12'] || 0);
                        var rowPenyusutanBerjalan = parseFloat(row.total_penyusutan_berjalan || 0);
                        var rowAkumulasiPenyusutan = parseFloat(row.akumulasi_penyusutan || 0);
                        var rowNilaiBukuTerakhir = parseFloat(row.nilai_buku_terakhir || 0);
                        var rowPenyusutanKe = parseInt(row.penyusutan_ke || 0);
                        var rowPengguna = row.pengguna || 0;

                        totalJumlah += rowJumlah;
                        totalHarga += rowHarga;
                        totalUmur += rowUmur;
                        totalPenyusutanPerbulan += rowPenyusutanPerbulan;
                        totalPenyusutanAwal += rowPenyusutanAwal;
                        totalJanuari += rowJanuari;
                        totalFebruari += rowFebruari;
                        totalMaret += rowMaret;
                        totalApril += rowApril;
                        totalMei += rowMei;
                        totalJuni += rowJuni;
                        totalJuli += rowJuli;
                        totalAgustus += rowAgustus;
                        totalSeptember += rowSeptember;
                        totalOktober += rowOktober;
                        totalNovember += rowNovember;
                        totalDesember += rowDesember;
                        totalPenyusutanBerjalan += rowPenyusutanBerjalan;
                        totalAkumulasiPenyusutan += rowAkumulasiPenyusutan;
                        totalNilaiBukuTerakhir += rowNilaiBukuTerakhir;
                        totalPenyusutanKe += rowPenyusutanKe;
                        totalPengguna += rowPengguna;

                        var tr = '<tr>';
                        tr += '<td>' + row.kode_branch + '</td>';
                        tr += '<td>' + row.kode_peralatan + '</td>';
                        tr += '<td>' + row.jenis + '</td>';
                        tr += '<td>' + row.type + '</td>';
                        tr += '<td>' + row.keterangan + '</td>';
                        tr += '<td>' + row.tanggal_perolehan + '</td>';
                        tr += '<td>' + rowJumlah + '</td>';
                        tr += '<td>' + formatRupiah(rowHarga) + '</td>';
                        tr += '<td>' + rowUmur + '</td>';
                        tr += '<td>' + formatRupiah(rowPenyusutanPerbulan) + '</td>';
                        tr += '<td>' + formatRupiah(rowPenyusutanAwal) + '</td>';
                        tr += '<td>' + formatRupiah(rowJanuari) + '</td>';
                        tr += '<td>' + formatRupiah(rowFebruari) + '</td>';
                        tr += '<td>' + formatRupiah(rowMaret) + '</td>';
                        tr += '<td>' + formatRupiah(rowApril) + '</td>';
                        tr += '<td>' + formatRupiah(rowMei) + '</td>';
                        tr += '<td>' + formatRupiah(rowJuni) + '</td>';
                        tr += '<td>' + formatRupiah(rowJuli) + '</td>';
                        tr += '<td>' + formatRupiah(rowAgustus) + '</td>';
                        tr += '<td>' + formatRupiah(rowSeptember) + '</td>';
                        tr += '<td>' + formatRupiah(rowOktober) + '</td>';
                        tr += '<td>' + formatRupiah(rowNovember) + '</td>';
                        tr += '<td>' + formatRupiah(rowDesember) + '</td>';
                        tr += '<td>' + formatRupiah(rowPenyusutanBerjalan) + '</td>';
                        tr += '<td>' + formatRupiah(rowAkumulasiPenyusutan) + '</td>';
                        tr += '<td>' + formatRupiah(rowNilaiBukuTerakhir) + '</td>';
                        tr += '<td>' + rowPenyusutanKe + '</td>';
                        tr += '<td>' + rowPengguna + '</td>';
                        tr += '</tr>';

                        table.row.add($(tr)).draw();
                    });

                    // Update total in tfoot
                    $('#total-harga').text(formatRupiah(totalHarga));
                    $('#total-umur').text(totalUmur);
                    $('#total-penyusutan-perbulan').text(formatRupiah(totalPenyusutanPerbulan));
                    $('#total-penyusutan-awal').text(formatRupiah(totalPenyusutanAwal));
                    $('#total-januari').text(formatRupiah(totalJanuari));
                    $('#total-februari').text(formatRupiah(totalFebruari));
                    $('#total-maret').text(formatRupiah(totalMaret));
                    $('#total-april').text(formatRupiah(totalApril));
                    $('#total-mei').text(formatRupiah(totalMei));
                    $('#total-juni').text(formatRupiah(totalJuni));
                    $('#total-juli').text(formatRupiah(totalJuli));
                    $('#total-agustus').text(formatRupiah(totalAgustus));
                    $('#total-september').text(formatRupiah(totalSeptember));
                    $('#total-oktober').text(formatRupiah(totalOktober));
                    $('#total-november').text(formatRupiah(totalNovember));
                    $('#total-desember').text(formatRupiah(totalDesember));
                    $('#total-penyusutan-berjalan').text(formatRupiah(totalPenyusutanBerjalan));
                    $('#total-akumulasi-penyusutan').text(formatRupiah(totalAkumulasiPenyusutan));
                    $('#total-nilai-buku-terakhir').text(formatRupiah(totalNilaiBukuTerakhir));
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching data:', error);
                }
            });
        }

        // Trigger fetch data on page load
        var kodeBranch = $('#kode_branch').val();
        fetchData(kodeBranch);

        // Trigger fetch data on branch change
        $('#kode_branch').on('change', function() {
            kodeBranch = $(this).val();
            fetchData(kodeBranch);
        });
    });
</script>
@endsection
