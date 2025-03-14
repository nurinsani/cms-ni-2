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

                <form action="{{ route('jalankanJurnalPembebananGol2') }}" method="POST" id="form-step3" class="step-form">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100">Jalankan Jurnal Pembebanan Gol 2</button>
                </form>
            </div>

            <!-- Stepper Navigation Buttons -->
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-secondary" id="prevStep" disabled>Previous</button>
                <button class="btn btn-primary" id="nextStep">Next</button>
            </div>
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
    </script>
@endsection
