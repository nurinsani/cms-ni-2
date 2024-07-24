@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('#') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Pembelian Peralatan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor</th>
                            <th>Cabang</th>
                            <th>COA</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengajuan as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->nomor }}</td>
                                <td>{{ $data->area->area }}</td>
                                <td>
                                    <ul class="p-0" style="list-style: none">
                                        @foreach ($data->pengajuan_details as $item)
                                            <li> {{ $item->jenis_peralatan->param_peralatan->coa }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul class="p-0" style="list-style: none">
                                        Pembelian
                                        @foreach ($data->pengajuan_details as $item)
                                            <li> {{ $item->jenis_peralatan->nama_jenis }} {{ $item->merk }} {{ $item->type }} </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>Rp. {{ number_format($data->subtotal()) }}</td>
                                <td>{{ $data->tanggal_pengajuan }}</td>
                                <td>
                                    <a href="{{ url('atasan-akunting/approval-peralatan/detail', $data->id) }}"
                                        class="btn btn-sm btn-primary">Detail</a>
                                    @if ($data->status_approve == 'lpj-finance')
                                    <button type="submit" onclick="approve('#approve{{ $data->id }}')"
                                        class="btn btn-sm btn-success">Approve</button>
                                    <form class="d-inline" id="approve{{ $data->id }}"
                                        action="{{ url('finance/approval-peralatan/approve', $data->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="submit" onclick="tolak('#tolak{{ $data->id }}')"
                                        class="btn btn-sm btn-danger">Tolak</button>
                                    <form class="d-inline" id="tolak{{ $data->id }}"
                                        action="{{ url('finance/approval-peralatan/tolak', $data->id) }}" method="post">
                                        @csrf
                                    </form>
                                    @endif
                                    @if ($data->status_approve == 'atasan-akunting')
                                    <button type="submit" onclick="approve('#approve{{ $data->id }}')"
                                        class="btn btn-sm btn-success">Approve</button>
                                    <form class="d-inline" id="approve{{ $data->id }}"
                                        action="{{ url('atasan-akunting/approval-peralatan/approve', $data->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="submit" onclick="tolak('#tolak{{ $data->id }}')"
                                        class="btn btn-sm btn-danger">Tolak</button>
                                    <form class="d-inline" id="tolak{{ $data->id }}"
                                        action="{{ url('atasan-akunting/approval-peralatan/tolak', $data->id) }}" method="post">
                                        @csrf
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tfoot>
                </table>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            function approve(form_id) {
                if (confirm('Approve data ini?')) {
                    $(form_id).submit()
                }
            }
        </script>

        <script>
            function tolak(form_id) {
                if (confirm('Tolak data ini?')) {
                    $(form_id).submit()
                }
            }
        </script>
    @endpush
@endsection
