@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<a href="{{ route('pembebanan-gol-2.proses') }}" 
   class="btn btn-primary"
   onclick="return confirm('Jalankan proses pembebanan penyusutan bulan ini?')">
   Proses Pembebanan Penyusutan
</a>
    <div class="card shadow-sm">
        {{-- <div class="card-header bg-primary text-white">
            <h5 class="card-title">Proses Penyusutan dan Jurnal Pembebanan</h5>
        </div> --}}
        <div class="card-body">
            <!-- Stepper Progress -->
            <div class="stepper d-flex justify-content-center">
                <div class="step active" id="step1" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-title">Jalankan Penyusutan</span>
                </div>
                <div class="step" id="step2" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-title">Jalankan Jurnal Penyusutan</span>
                </div>
                <div class="step" id="step3" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-title">Jalankan Jurnal Pembebanan</span>
                </div>
            </div>

            <!-- Stepper Content -->
            <div class="step-content mt-4">
                <form action="{{ route('jalankanPenyusutanGol2') }}" method="POST" id="form-step1" class="step-form active">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">Jalankan Penyusutan Gol 2</button>
                </form>

                <form action="{{ route('jalankanJurnalPenyusutanGol2') }}" method="POST" id="form-step2" class="step-form">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">Jalankan Jurnal Penyusutan Gol 2</button>
                </form>

                {{-- <form action="{{ route('jalankanJurnalPembebananGol2') }}" method="POST" id="form-step3" class="step-form">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100">Jalankan Jurnal Pembebanan Gol 2</button>
                </form> --}}

                <a href="{{ route('pembebanan-gol-2.proses') }}" class="btn btn-warning w-100 step-form" id="form-step3"
                onclick="return confirm('Jalankan jurnal pembebanan bulan ini?')">
                Jalankan Jurnal Pembebanan
                </a>
            </div>

            <!-- Stepper Navigation Buttons -->
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-secondary" id="prevStep" disabled>Previous</button>
                <button class="btn btn-primary" id="nextStep">Next</button>
            </div>
        </div>
    </div>

    <div class="card card-shadow-sm mt-3">
        <div class="card-header text-center">
            Upload Jurnal Transaksi ke Sistem NI
        </div>
        <div class="card-body">

            <button type="button" class="btn btn-primary w-100 mb-3" id="penyusutanUploadGol2">
                Upload Jurnal Penyusutan Gol 2
            </button>

            <button type="button" class="btn btn-primary w-100 mb-3" id="pembebananKpGol2">
                Upload Jurnal Pembebanan KP Gol 2
            </button>

            <button type="button" class="btn btn-primary w-100 mb-3" id="pembebananApGol2">
                Upload Jurnal Pembebanan AP Gol 2
            </button>

            {{-- @if ($transaksiAp->status == "uploaded")
                <button type="button" class="btn btn-primary w-100 mb-3" id="" disabled>
                    Data Pembebanan KP Sudah diupload.
                </button>
            @else
            @endif --}}

        </div>
    </div>

    <style>
        /* Stepper Styling */
        .stepper {
            display: flex;
            gap: 30px;
        }
        .step {
            text-align: center;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #ddd;
            border-radius: 50%;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .step.active .step-number {
            background-color: #007bff;
            color: #fff;
        }
        .step-title {
            font-size: 14px;
            font-weight: 600;
        }
        .step-form {
            display: none;
        }
        .step-form.active {
            display: block;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // JavaScript for Stepper Functionality
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 1;
            const totalSteps = 3;

            const showStep = (step) => {
                // Update step number and content
                document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
                document.getElementById(`step${step}`).classList.add('active');

                document.querySelectorAll('.step-form').forEach(f => f.classList.remove('active'));
                document.getElementById(`form-step${step}`).classList.add('active');

                // Update buttons
                document.getElementById('prevStep').disabled = (step === 1);
                document.getElementById('nextStep').disabled = (step === totalSteps);
            };

            // Event listeners for Next and Previous buttons
            document.getElementById('nextStep').addEventListener('click', function () {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            document.getElementById('prevStep').addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            // Initialize the first step
            showStep(currentStep);
        });

        $('#penyusutanUploadGol2').click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Proses upload sedang berjalan",
                text: "Jangan tutup halaman ini...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            

            $.ajax({
                url: "{{ route('penyusutan.upload-api-gol-2') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Upload jurnal selesai!'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseText
                    });
                }
            });
        });

        $('#pembebananKpGol2').click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Proses upload sedang berjalan",
                text: "Jangan tutup halaman ini...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            

            $.ajax({
                url: "{{ route('pembebanan.upload-api-kp-gol-2') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Upload jurnal selesai!'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseText
                    });
                }
            });
        });

        $('#pembebananApGol2').click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Proses upload sedang berjalan",
                text: "Jangan tutup halaman ini...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            

            $.ajax({
                url: "{{ route('pembebanan.upload-api-ap-gol-2') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Upload jurnal selesai!'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseText
                    });
                }
            });
        });

    </script>
@endsection
