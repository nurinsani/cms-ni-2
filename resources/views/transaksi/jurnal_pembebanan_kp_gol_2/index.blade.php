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
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ url('jurnal-pembebanan-kp-gol-2') }}" method="GET">
                        @csrf
                        <div class="row">
                            <div class="col-4 mb-3">Pilih Tanggal Awal</div>
                            <div class="col-4 mb-3">Pilih Tanggal Akhir</div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-filter"></i> FILTER
                                </button>
                                <a class="btn btn-warning" href="{{ url('jurnal-pembebanan-kp-gol-2') }}">
                                    <i class="fas fa-sync-alt"></i> RESET
                                </a>
                            </div>

                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Kode Branch</th>
                        <th>Kode Transaksi</th>
                        <th>Kode Rekening</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th>Keterangan</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jurnal_pembebanan_kp_gol_2 as $item)
                        <tr>
                            <td>{{ $item->kode_branch }}</td>
                            <td>{{ $item->kode_transaksi }}</td>
                            <td>{{ $item->kode_rekening }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m/d/Y') }}</td>
                            <td>{{ $item->jenis_transaksi }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->debet }}</td>
                            <td>{{ $item->kredit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
@endsection
