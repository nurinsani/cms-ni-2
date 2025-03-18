@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('#') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Pembelian Peralatan
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor</th>
                            <th>Cabang</th>
                            <th>Tanggal Pembelian</th>
                            <th>Nama Peralatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lpj as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->nomor }}</td>
                                <td>{{ $data->area->area }}</td>
                                <td>{{ $data->tanggal_pengajuan->format('d-M-Y'); }}</td>
                                <td>
                                    <ul class="p-0" style="list-style: none">
                                        @foreach ($data->pengajuan_details as $item)
                                            <li> {{ $item->jenis_peralatan->nama_jenis }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <a href="{{ url('pengurus/approval-lpj/detail', $data->id) }}"
                                        class="btn btn-sm btn-primary">Detail</a>

                                    <button type="submit" onclick="approve('#approve{{ $data->id }}')"
                                        class="btn btn-sm btn-success">Approve</button>
                                    <form class="d-inline" id="approve{{ $data->id }}"
                                        action="{{ url('pengurus/approval-lpj/approve', $data->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="submit" onclick="tolak('#tolak{{ $data->id }}')"
                                        class="btn btn-sm btn-danger">Tolak</button>
                                    <form class="d-inline" id="tolak{{ $data->id }}"
                                        action="{{ url('pengurus/tolak-lpj/tolak', $data->id) }}" method="post">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tfoot>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
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
