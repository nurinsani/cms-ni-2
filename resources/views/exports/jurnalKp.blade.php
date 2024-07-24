<table id="table" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Kode Transaksi</th>
            <th>Kode Rekening</th>
            <th>Jenis Transaksi</th>
            <th>Debet</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jurnal_kp as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->tanggal_transaksi }}</td>
                <td>{{ $item->kode_transaksi }}</td>
                <td>{{ $item->kode_rekening }}</td>
                <td>{{ $item->jenis_transaksi }}</td>
                <td>Rp. {{ number_format($item->debet) }}</td>
                <td>Rp. {{ number_format($item->kredit) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
