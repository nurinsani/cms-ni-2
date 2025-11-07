@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('#') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 col-6">
            <!-- small box -->
            <a href="{{ url('/akunting/approval-peralatan') }}">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $approval_um->count() }}</h3>
    
                        <p>PENGAJUAN UM</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </a>
        </div>
        <!-- ./col -->
        <div class="col-lg-6 col-6">
            <!-- small box -->
            <a href="{{ url('/akunting/approval-lpj') }}">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $approval_lpj->count() }}</h3>
    
                        <p>PENGAJUAN LPJ</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </a>
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
    @endpush
@endsection
