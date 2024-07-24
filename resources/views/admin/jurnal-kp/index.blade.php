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
                    <form action="{{ url('export-excel/jurnal-kp') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="date" class="form-control date" id="start_date"name="start_date">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <input type="date" class="form-control date" id="end_date" name="end_date">
                                </div>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-sm btn-success my-2">Export</button>
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
        </div>
        <!-- /.card-body -->
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            const table = $('#table').DataTable();

            function filterEvents() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const eventDate = data[1]; // use data for the date column

                        if (
                            (startDate === '' || eventDate >= startDate) &&
                            (endDate === '' || eventDate <= endDate)
                        ) {
                            return true;
                        }
                        return false;
                    }
                );

                table.draw();
                $.fn.dataTable.ext.search.pop();
            }

            $('.date').on('change', filterEvents);
        });
    </script>
@endpush
