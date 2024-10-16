<table id="table" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>kode_branch</th>
            <th>kode_transaksi</th>
            <th>kode_rekening</th>
            <th>tanggal_transaksi</th>
            <th>jenis_transaksi</th>
            <th>keterangan</th>
            <th>debet</th>
            <th>kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jurnal_kp as $item)
            <tr>
                <td>{{ $item->kode_branch }}</td>
                <td>{{ $item->kode_transaksi }}</td>
                <td>{{ $item->kode_rekening }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}</td>
                <td>{{ $item->jenis_transaksi }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->debet }}</td>
                <td>{{ $item->kredit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
