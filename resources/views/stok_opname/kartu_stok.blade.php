<!DOCTYPE html>
<html>
<head>
    <title>List Barcode Inventaris</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 14px;
        }

        h2 {
            margin-bottom: 10px;
            font-size: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
        }

        th {
            background: #f2f2f2;
        }

        @media print {
            body { margin: 0; }
        }
    </style>
</head>

<body onload="window.print()">

<h2>LIST BARCODE INVENTARIS</h2>

<table>
    <thead>
        <tr>
            <th style="width: 200px;">Barcode</th>
            <th>Jenis</th>
            <th>Akm Penyusutan Berjalan</th>
            <th>Akm Penyusutan</th>
            <th>Nilai Buku</th>
            <th>Peny Ke</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($items as $item)
        <tr>
            <td>{{ $item->kode_peralatan }}</td>
            <td>{{ $item->jenis }}</td>
            <td>Rp. 2000.000</td>
            <td>Rp. 2000.000</td>
            <td>Rp. 2000.000</td>
            <td>Rp. 2000.000</td>
            
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
