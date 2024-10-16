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
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peralatan</th>
                            <th>Dari AP</th>
                            <th>Ke AP</th>
                            <th>Bulan Mutasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mutasi as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $data->peralatan->jenis }}</td>
                                <td>{{ $data->peralatan->ap->unit }}</td>

                                <td>{{ $data->branch->unit }}</td>
                                <td>{{ $data->bulan_mutasi }}</td>
                                <td>    
                                    <button type="submit" onclick="approve('#approve{{ $data->id }}')"
                                        class="btn btn-sm btn-success">Approve</button>
                                    <form class="d-inline" id="approve{{ $data->id }}"
                                        action="{{ url('akunting/approval-mutasi/approve', $data->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="submit" onclick="tolak('#tolak{{ $data->id }}')"
                                        class="btn btn-sm btn-danger">Tolak</button>
                                    <form class="d-inline" id="tolak{{ $data->id }}"
                                        action="{{ url('akunting/approval-mutasi/tolak', $data->id) }}" method="post">
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
