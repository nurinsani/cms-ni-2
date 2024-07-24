@extends('layouts.main')

@section('head-content')
<div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0">{{ $title }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ url("#") }}">Home</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
      </ol>
    </div><!-- /.col -->
  </div><!-- /.row -->
@endsection

@section('content')
<div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>No</th>
            <th>Jenis BDD</th>
            <th>Cabang/Ap</th>
            <th>Sisa Bulan Amortisasi</th>
            <th>Tanggal Pemutusan</th>
            <th>Aksi</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
    <!-- /.card-body -->
  </div>

  @push('js')
    <script>
      function approve (form_id){
        if(confirm('Approve data ini?')) {
          $(form_id).submit()
        }
      }
    </script>

    <script>
      function tolak (form_id){
        if(confirm('Tolak data ini?')) {
          $(form_id).submit()
        }
      }
    </script>
  @endpush
@endsection