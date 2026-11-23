<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE/dist/css/adminlte.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
{{-- 
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css"> --}}



    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <style>
        .main-sidebar .nav-sidebar .nav-link {
            border-radius: 6px;
            margin-bottom: 2px;
        }
        .card {
            border-radius: 8px;
        }
        .small-box {
            border-radius: 10px;
            overflow: hidden;
        }
        @media (max-width: 767.98px) {
            .small-box,
            .small-box .inner {
                text-align: left !important;
            }
        }
        .badge-purple {
            background-color: #6f42c1;
            color: #fff;
        }
        .table th {
            vertical-align: middle !important;
        }
        .table td {
            vertical-align: middle !important;
        }
    </style>
    @stack('css')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm border-bottom-0">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="{{ url('#') }}" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item mr-2 d-none d-sm-inline-block">
                    <span class="text-muted small">
                        <i class="far fa-user-circle mr-1"></i> {{ Auth::user()->name }}
                        <span class="badge badge-primary ml-1 text-uppercase">{{ Auth::user()->getRoleNames()[0] ?? '' }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-sm btn-outline-danger" onclick="logout('#logout_form')" href="#" role="button">
                        <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                    </a>
                    <form action="{{ route('logout') }}" id="logout_form" method="post" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="#" class="brand-link">
                <img src="{{ asset('/images/logo-ni-white.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-bold">CMS NI</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="img-circle elevation-2"
                            alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
                        @if (auth()->user()->hasRole('admin'))
                            @include('layouts.partials.admin-menu')
                        @endif

                        @if (auth()->user()->hasRole('akunting'))
                            @include('layouts.partials.akunting-menu')
                        @endif

                        @if (auth()->user()->hasRole('admin-head'))
                            @include('layouts.partials.supervisor-menu')
                        @endif

                        @if (auth()->user()->hasRole('it'))
                            @include('layouts.partials.it-menu')
                        @endif

                        @if (auth()->user()->hasRole('finance'))
                            @include('layouts.partials.finance-menu')
                        @endif

                        @if (auth()->user()->hasRole('pengurus'))
                            @include('layouts.partials.pengurus-menu')
                        @endif

                        @if (auth()->user()->hasRole('atasan-akunting'))
                            @include('layouts.partials.atasan-akunting-menu')
                        @endif

                        @if (auth()->user()->hasRole('atasan-it'))
                            @include('layouts.partials.atasan-it-menu')
                        @endif

                    </ul>
                </nav>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    @yield('head-content')
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">

                    @include('sweetalert::alert')

                    @yield('content')
                </div>

                <!-- /.container-fluid -->
            </div>
            {{-- {{ $inventaris->links() }} --}}
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; NI 2023</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('/AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE -->
    <script src="{{ asset('/AdminLTE/dist/js/adminlte.js') }}"></script>

    <!-- DataTables  & Plugins -->
    <script src="{{ asset('/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/AdminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- JSZip untuk Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Select2 -->
    <script src="{{ asset('/AdminLTE/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('/AdminLTE/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Page specific script -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            var defaultExportOptions = {
                columns: function(idx, data, node) {
                    var thText = $(node).text().trim().toLowerCase();
                    return thText !== 'aksi' && !$(node).hasClass('no-export');
                }
            };

            if ($.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.buttons) {
                if ($.fn.dataTable.ext.buttons.excelHtml5) {
                    $.fn.dataTable.ext.buttons.excelHtml5.exportOptions = defaultExportOptions;
                }
                if ($.fn.dataTable.ext.buttons.csvHtml5) {
                    $.fn.dataTable.ext.buttons.csvHtml5.exportOptions = defaultExportOptions;
                }
                if ($.fn.dataTable.ext.buttons.pdfHtml5) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.exportOptions = defaultExportOptions;
                }
                if ($.fn.dataTable.ext.buttons.print) {
                    $.fn.dataTable.ext.buttons.print.exportOptions = defaultExportOptions;
                }
            }

            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "ordering": false,
                "destroy" : true,
                "buttons": [
                    { extend: "csv", exportOptions: defaultExportOptions },
                    { extend: "excel", exportOptions: defaultExportOptions },
                    { extend: "pdf", exportOptions: defaultExportOptions }
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "autoWidth": false,
                "buttons": [
                    { extend: "copy", exportOptions: defaultExportOptions },
                    { extend: "csv", exportOptions: defaultExportOptions },
                    { extend: "excel", exportOptions: defaultExportOptions },
                    { extend: "pdf", exportOptions: defaultExportOptions },
                    { extend: "print", exportOptions: defaultExportOptions }
                ]
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

            $('.myTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
            });
        });

        function hapus_data(form_id) {
            if (confirm('Yakin mau menghapus data ini?')) {
                $(form_id).submit()
            }
        }

        function logout(form_id) {
            if (confirm('Yakin ingin keluar?')) {
                $(form_id).submit()
            }
        }

        $('.select2').select2()

        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
    </script>

    @stack('js')

</body>

</html>

<!-- comment -->