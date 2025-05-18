@extends('layouts.master')
@section('title')
ألاسرة المهيئة 
@stop
@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<!--Internal   Notify -->
<link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-warning">
                                <i class="fas fa-edit fa-fw me-2"></i>
                                تعديل التخصص
                            </h3>
                            <p class="text-secondary mb-0">تعديل بيانات التخصص: {{ $specialization->tsname }}</p>
                        </div>
                        <a href="{{ route('type-specializations.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-2"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('type-specializations.update', $specialization) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="tscode" class="form-label fw-bold">
                                        <i class="fas fa-hashtag text-warning me-2"></i>
                                        كود التخصص
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('tscode') is-invalid @enderror" 
                                           id="tscode" 
                                           name="tscode" 
                                           value="{{ old('tscode', $specialization->tscode) }}" 
                                           required>
                                    @error('tscode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="tsname" class="form-label fw-bold">
                                        <i class="fas fa-pen text-warning me-2"></i>
                                        اسم التخصص
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('tsname') is-invalid @enderror" 
                                           id="tsname" 
                                           name="tsname" 
                                           value="{{ old('tsname', $specialization->tsname) }}" 
                                           required>
                                    @error('tsname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-warning btn-lg px-5 py-3 mb-0">
                                <i class="fas fa-save me-2"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        border-radius: 15px;
    }
    .card-header {
        background: none;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .form-control {
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
    .btn-warning {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        border: none;
        box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
        color: white;
    }
    .btn-warning:hover {
        background: linear-gradient(45deg, #ff9800, #ffc107);
        color: white;
    }
    .btn-outline-secondary {
        border: 2px solid #858796;
        color: #858796;
    }
    .btn-outline-secondary:hover {
        background: #858796;
        color: white;
    }
    .form-label {
        color: #ff9800;
        margin-bottom: 0.5rem;
    }
    .alert {
        border: none;
        border-radius: 10px;
    }
    .invalid-feedback {
        font-size: 0.875em;
    }
    .text-warning {
        color: #ff9800 !important;
    }
</style>
@endpush
@endsection
