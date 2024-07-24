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
        </tr>
    </thead>
    <tbody>
        @foreach ($inventaris as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->kode_peralatan }}</td>
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
                <td>Rp.
                    {{ number_format($item->akumulasi_penyusutan_awal() + $item->total_penyusutan_awal) }}
                </td>
                <td>Rp.
                    {{ number_format($item->total_penyusutan_berjalan + $item->akumulasi_penyusutan()) }}
                </td>

                {{-- akumulasi penyusutan --}}
                <td>Rp.
                    {{ number_format(($item->harga_perolehan / 48) * $item->susut->count() + $item->akumulasi_penyusutan) }}
                </td>
                {{-- end akumulasi penyusutan --}}

                {{-- nilai buku terakhir --}}
                <td>Rp.
                    @if ($item->nilai_buku_terakhir == 0)
                        {{ number_format($item->harga_perolehan - $item->akumulasi_penyusutan()) }}
                    @else
                        {{ number_format($item->nilai_buku_terakhir - $item->akumulasi_penyusutan()) }}
                    @endif
                </td>
                {{-- end nilai buku terakhir --}}

                <td>
                    {{ count($item->susut) + ($item->penyusutan_ke ?? 0) }}
                </td>
                <td>
                    @if ($item->pengguna == null)
                        -
                    @else
                        {{ $item->pengguna }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="8" class="text-center"><strong>TOTAL</strong></td>
            <td>Rp. {{ number_format($harga_perolehan) }}</td>
            <td></td>
            <td>Rp. {{ number_format($peny) }}</td>
            <td>Rp. {{ number_format($peny_awal) }}</td>
            <td>Rp. {{ number_format($peny_jalan) }}</td>
            <td>Rp. {{ number_format($penyusutan) }}</td>
            <td>Rp. {{ number_format($total_nilai_buku) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
