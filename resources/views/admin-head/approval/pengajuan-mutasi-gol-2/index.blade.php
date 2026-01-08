@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="card">
        {{-- <div class="card-header">
            <a href="{{ route('admin.pengajuan-mutasi.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> Tambah</a>
        </div> --}}
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
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
                    @foreach ($pengajuan_mutasi as $key => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_mutasi }}</td>
                            <td>{{ $item->dari_ap->unit }}</td>
                            <td>{{ $item->branch->unit }}</td>
                            <td>{{ $item->bulan_mutasi }}</td>
                            <td>{{ $item->inventaris_gol_2->count() }}</td>
                            <td>
                                <button type="submit" onclick="approve('#approve{{ $item->id }}')"
                                    class="btn btn-sm btn-success">Approve</button>
                                <form class="d-inline" id="approve{{ $item->id }}"
                                    action="{{ url('admin-head/approval-mutasi-gol-2/approve', $item->id) }}" method="post">
                                    @csrf
                                </form>

                                <button type="submit" onclick="tolak('#tolak{{ $item->id }}')"
                                    class="btn btn-sm btn-danger">Tolak</button>
                                <form class="d-inline" id="tolak{{ $item->id }}"
                                    action="{{ url('admin-head/approval-mutasi-gol-2/tolak', $item->id) }}" method="post">
                                    @csrf
                                </form>

                                @if ($item->status_approve === 'admin')
                                    <button type="submit" onclick="hapus_data('#delete_form{{ $item->id }}')"
                                        class="btn btn-sm btn-danger">Hapus</button>
                                    <form class="d-inline" id="delete_form{{ $item->id }}"
                                        action="{{ route('admin.pengajuan-mutasi.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
                                <a href="{{ url('admin-head/approval-mutasi-gol-2/detail', $item->id) }}"
                                    class="btn btn-info btn-sm">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
