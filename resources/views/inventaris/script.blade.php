<script>
    $(document).ready(function() {

        function formatRupiah(number) {
            return parseFloat(number).toLocaleString('id-ID', {
                useGrouping: true,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function fetchData() {
            $('#loading-overlay').css('display', 'flex').fadeIn(150);
            $.ajax({
                url: '{{ route('inventaris.fetchData') }}',
                method: 'GET',
                data: {
                    region: $('#region').val(),
                    area: $('#area').val(),
                    kode_branch: $('#kode_branch').val(),
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val()
                },
                success: function(response) {
                    var currentMonth = $('#bulan').val();
                    $('.month-header').removeClass('active-month');
                    if (currentMonth) {
                        $('.month-header[data-month="' + currentMonth + '"]').addClass('active-month');
                    }

                    var table = $('#inventaris-table').DataTable();

                    if (response.areas.length > 0) {
                        $('#area').append(`
                            <option hidden="" value="">-- Pilih Area --</option>
                        `);

                        $.each(response.areas, function(i, area) {
                            $('#area').append(`
                                <option value="${area}">${area}</option>
                            `);
                        });
                    } else if (response.branches.length > 0) {
                        $('#kode_branch').append(`
                            <option hidden="" value="">-- Pilih Wilayah --</option>
                        `);

                        $.each(response.branches, function(i, branches) {
                            console.log('Index:', i, 'Value:', branches); // Cetak data ke konsol
                            $('#kode_branch').append(`
                                <option value="${branches}">${branches}</option>
                            `);
                        });
                    }

                    table.clear();

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

                    var selectedYear = $('#tahun').val() || response.selectedYear || new Date().getFullYear();

                    // Update badge tahun
                    $('#selected-year-badge').text('Tahun ' + selectedYear);

                    var rowsToDraw = [];

                    // Populate table rows
                    response.pivotData.forEach(function(row) {
                        var rowHarga = parseFloat(row.harga_perolehan || 0);
                        var rowPenyusutanPerbulan = parseFloat(row.penyusutan_perbulan || 0);
                        var rowPenyusutanAwal = parseFloat(row.total_penyusutan_awal || 0);
                        var rowJanuari = parseFloat(row[selectedYear + '-01'] || 0);
                        var rowFebruari = parseFloat(row[selectedYear + '-02'] || 0);
                        var rowMaret = parseFloat(row[selectedYear + '-03'] || 0);
                        var rowApril = parseFloat(row[selectedYear + '-04'] || 0);
                        var rowMei = parseFloat(row[selectedYear + '-05'] || 0);
                        var rowJuni = parseFloat(row[selectedYear + '-06'] || 0);
                        var rowJuli = parseFloat(row[selectedYear + '-07'] || 0);
                        var rowAgustus = parseFloat(row[selectedYear + '-08'] || 0);
                        var rowSeptember = parseFloat(row[selectedYear + '-09'] || 0);
                        var rowOktober = parseFloat(row[selectedYear + '-10'] || 0);
                        var rowNovember = parseFloat(row[selectedYear + '-11'] || 0);
                        var rowDesember = parseFloat(row[selectedYear + '-12'] || 0);
                        var rowPenyusutanBerjalan = parseFloat(row
                            .total_penyusutan_berjalan || 0);
                        var rowAkumulasiPenyusutan = parseFloat(row.akumulasi_penyusutan || 0);
                        var rowNilaiBukuTerakhir = parseFloat(row.nilai_buku_terakhir || 0);
                        var rowPenyusutanKe = parseFloat(row.penyusutan_ke || 0);
                        var rowPengguna = parseFloat(row.pengguna || 0);

                        if (isNaN(rowPengguna)) rowPengguna = 0;

                        totalHarga += rowHarga;
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
                        totalPengguna += rowPengguna;

                        var tr = '<tr>';
                        tr += '<td>' + row.kode_branch + '</td>';
                        tr += '<td>' + row.unit + '</td>';
                        tr += '<td>' + row.kode_peralatan + '</td>';
                        tr += '<td>' + row.jenis + '</td>';
                        tr += '<td>' + row.type + '</td>';
                        tr += '<td>' + row.keterangan + '</td>';
                        tr += '<td>' + row.tanggal_perolehan + '</td>';
                        tr += '<td>' + row.jumlah + '</td>';
                        tr += '<td>' + formatRupiah(rowHarga) + '</td>';
                        tr += '<td>' + row.umur + '</td>';
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

                        rowsToDraw.push(tr);
                    });

                    if (rowsToDraw.length > 0) {
                        table.rows.add($(rowsToDraw.join(''))).draw();
                    } else {
                        table.draw();
                    }

                    // Update total in tfoot
                    $('#total-harga').text(formatRupiah(
                        totalHarga)); // Rupiah format for total harga
                    $('#total-umur').text(totalUmur); // Format for total umur
                    $('#total-penyusutan-perbulan').text(formatRupiah(
                        totalPenyusutanPerbulan)); // Rupiah format for total penyusutan_perbulan
                    $('#total-penyusutan-awal').text(formatRupiah(
                        totalPenyusutanAwal)); // Rupiah format for total total_penyusutan_awal
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
                    $('#total-penyusutan-berjalan').text(formatRupiah(
                        totalPenyusutanBerjalan)); // Rupiah format for total penyusutan_berjalan
                    $('#total-akumulasi-penyusutan').text(formatRupiah(
                        totalAkumulasiPenyusutan)); // Rupiah format for total akumulasi_penyusutan
                    $('#total-nilai-buku-terakhir').text(formatRupiah(
                        totalNilaiBukuTerakhir)); // Rupiah format for total nilai_buku_terakhir
                    $('#total-penyusutan-ke').text(formatRupiah(
                        totalPenyusutanKe)); // Rupiah format for total penyusutan_ke
                    $('#total-pengguna').text(totalPengguna); // Rupiah format for total pengguna
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                },
                complete: function() {
                    $('#loading-overlay').fadeOut(200);
                }
            });
        }

        // Initialize DataTable with Bootstrap 4 styling
        var table = $('#inventaris-table').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            responsive: false,
            pageLength: 25,
            destroy: true,
            buttons: [
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-info',
                    text: '<i class="fas fa-file-csv mr-1"></i> CSV',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                return data.replace(/Rp\s?|,|\./g, '');
                            }
                        }
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    footer: true, // Untuk menampilkan footer (di mana total akan ditambahkan)
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                return data.replace(/Rp\s?|,|\./g, '');
                            },
                            footer: function (data, row, column, node) {
                                // Menghapus format Rupiah dari footer juga
                                return data.replace(/Rp\s?|,|\./g, '');
                            }
                        }
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // Menghitung total dari kolom yang Anda inginkan (misalnya kolom ke-3)
                        var total = 0;
                        $('#inventaris-table tbody tr').each(function () {
                            var value = parseFloat($(this).find('td:eq(2)').text().replace(/Rp\s?|,|\./g, ''));
                            total += !isNaN(value) ? value : 0;
                        });

                        // Menambahkan total ke baris terakhir di Excel
                        var row = $('row:last', sheet);
                        row.after('<row><c t="inlineStr" r="A"><is><t>Total</t></is></c><c t="inlineStr" r="C"><is><t>' + total + '</t></is></c></row>');
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-danger',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                return data.replace(/Rp\s?|,|\./g, '');
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-print mr-1"></i> Print'
                }
            ]
        });

        table.buttons().container().appendTo('#inventaris-table_wrapper .col-md-6:eq(0)');

        // Initial fetch
        fetchData();

        // Fetch data when the selected branch changes
        $(document).on('change', '#region', function() {
            $('#kode_branch').html('').val('');
            $('#area').html('').val('');
            fetchData();
        });
        
        $(document).on('change', '#area', function() {
            $('#kode_branch').html('').val('');
            fetchData();
        });

        $(document).on('change', '#kode_branch', function() {
            fetchData();
        });

        // Trigger fetchData ketika filter tahun berubah
        $(document).on('change', '#tahun', function() {
            fetchData();
        });

        // Trigger fetchData ketika filter bulan berubah
        $(document).on('change', '#bulan', function() {
            fetchData();
        });

        // Trigger filter bulan ketika th header bulan di-klik
        $(document).on('click', '.month-header', function() {
            var monthVal = $(this).attr('data-month');
            if ($('#bulan').val() === monthVal) {
                // Jika bulan yang sama di-klik lagi, reset (tampilkan semua bulan)
                $('#bulan').val('').trigger('change');
            } else {
                $('#bulan').val(monthVal).trigger('change');
            }
        });
    });
</script>
