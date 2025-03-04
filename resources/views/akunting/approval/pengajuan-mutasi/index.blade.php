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
                            <th>No Pengajuan</th>
                            <th>Dari AP</th>
                            <th>Ke AP</th>
                            <th>Bulan Mutasi</th>
                            <th>Jumalah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mutasi as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->kode_mutasi }}</td>
                                <td>{{ $item->dari_ap->unit }}</td>
                                <td>{{ $item->branch->unit }}</td>
                                <td>{{ $item->bulan_mutasi }}</td>
                                <td>{{ $item->inventaris->count() }}</td>
                                <td>    
                                    <button type="submit" onclick="approve('#approve{{ $item->id }}')"
                                        class="btn btn-sm btn-success">Approve</button>
                                    <form class="d-inline" id="approve{{ $item->id }}"
                                        action="{{ url('akunting/approval-mutasi/approve', $item->id) }}" method="post">
                                        @csrf
                                    </form>

                                    <button type="submit" onclick="tolak('#tolak{{ $item->id }}')"
                                        class="btn btn-sm btn-danger">Tolak</button>
                                    <form class="d-inline" id="tolak{{ $item->id }}"
                                        action="{{ url('akunting/approval-mutasi/tolak', $item->id) }}" method="post">
                                        @csrf
                                    </form>
                                    <a href="{{ url('akunting/approval-mutasi/detail', $item->id) }}"
                                        class="btn btn-info btn-sm">
                                        Lihat Detail
                                    </a>
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
