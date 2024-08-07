<!DOCTYPE html>
<html>
<head>
    <title>Inventaris dan Penyusutan</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Inventaris dan Penyusutan</h1>

    <form id="filter-form">
        <label for="kode_branch">Pilih Branch:</label>
        <select name="kode_branch" id="kode_branch">
            <option value="">-- Semua Branch --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->kode_branch }}">{{ $branch->kode_branch }}</option>
            @endforeach
        </select>
    </form>

    <br/>

    <table id="inventaris-table">
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
                <!-- Columns for months will be added dynamically -->
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th id="total-harga">0</th>
                <th id="total-umur">0</th>
                <th id="total-penyusutan-perbulan">0</th>
                <th id="total-penyusutan-awal">0</th>
                <!-- Total for months will be added dynamically -->
            </tr>
        </tfoot>
        <tbody>
            <!-- Data will be populated dynamically -->
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            function formatRupiah(number) {
                return parseFloat(number).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }
    
            function monthName(month) {
                const months = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                return months[parseInt(month.split('-')[1], 10) - 1];
            }
    
            function fetchData(kodeBranch) {
                $.ajax({
                    url: '{{ route('inventaris.fetchData') }}',
                    method: 'GET',
                    data: { kode_branch: kodeBranch },
                    success: function(response) {
                        var table = $('#inventaris-table');
                        var thead = table.find('thead');
                        var tbody = table.find('tbody');
                        var tfoot = table.find('tfoot');
                        
                        // Clear existing table data
                        tbody.empty();
                        tfoot.find('th:gt(10)').remove(); // Remove old month total columns
    
                        // Store existing month columns to prevent duplication
                        var existingMonths = thead.find('th').map(function() {
                            return $(this).text();
                        }).get();
    
                        // Add new month columns
                        response.months.forEach(function(month) {
                            const monthNameFormatted = monthName(month);
                            if (existingMonths.indexOf(monthNameFormatted) === -1) {
                                thead.find('tr').append('<th>' + monthNameFormatted + '</th>');
                                tfoot.find('tr').append('<th id="total-' + month + '">0</th>');
                                existingMonths.push(monthNameFormatted); // Add new month to existing list
                            }
                        });
    
                        var totalJumlah = 0;
                        var totalHarga = 0;
                        var totalUmur = 0;
                        var totalPenyusutanPerbulan = 0;
                        var totalPenyusutanAwal = 0;
                        var monthTotals = {};
    
                        response.months.forEach(function(month) {
                            monthTotals[month] = 0;
                        });
    
                        // Populate table rows
                        response.pivotData.forEach(function(row) {
                            var rowHarga = parseFloat(row.harga_perolehan || 0);
                            var rowPenyusutanPerbulan = parseFloat(row.penyusutan_perbulan || 0);
                            var rowPenyusutanAwal = parseFloat(row.total_penyusutan_awal || 0);
    
                            totalHarga += rowHarga;
                            totalPenyusutanPerbulan += rowPenyusutanPerbulan;
                            totalPenyusutanAwal += rowPenyusutanAwal;
                            var tr = '<tr>';
                            tr += '<td>' + row.kode_branch + '</td>';
                            tr += '<td>' + row.kode_peralatan + '</td>';
                            tr += '<td>' + row.jenis + '</td>';
                            tr += '<td>' + row.type + '</td>';
                            tr += '<td>' + row.keterangan + '</td>';
                            tr += '<td>' + row.tanggal_perolehan + '</td>';
                            tr += '<td>' + row.jumlah + '</td>';
                            tr += '<td>' + formatRupiah(rowHarga) + '</td>'; // Rupiah format for harga_perolehan
                            tr += '<td>' + row.umur + '</td>'; // Format for umur
                            tr += '<td>' + formatRupiah(rowPenyusutanPerbulan) + '</td>'; // Rupiah format for penyusutan_perbulan
                            tr += '<td>' + formatRupiah(rowPenyusutanAwal) + '</td>'; // Rupiah format for total_penyusutan_awal
    
                            response.months.forEach(function(month) {
                                var penyusutan = parseFloat(row[month] || 0);
                                tr += '<td>' + formatRupiah(penyusutan) + '</td>';
                                monthTotals[month] += penyusutan;
                            });
    
                            tr += '</tr>';
                            tbody.append(tr);
                        });
    
                        // Update total in tfoot
                        tfoot.find('#total-harga').text(formatRupiah(totalHarga)); // Rupiah format for total harga
                        tfoot.find('#total-umur').text(totalUmur); // Format for total umur
                        tfoot.find('#total-penyusutan-perbulan').text(formatRupiah(totalPenyusutanPerbulan)); // Rupiah format for total penyusutan_perbulan
                        tfoot.find('#total-penyusutan-awal').text(formatRupiah(totalPenyusutanAwal)); // Rupiah format for total total_penyusutan_awal
    
                        response.months.forEach(function(month) {
                            tfoot.find('#total-' + month).text(formatRupiah(monthTotals[month]));
                        });
                    }
                });
            }
    
            // Initial fetch
            fetchData($('#kode_branch').val());
    
            // Fetch data when the selected branch changes
            $('#kode_branch').change(function() {
                fetchData($(this).val());
            });
        });
    </script>
    
    
</body>
</html>
