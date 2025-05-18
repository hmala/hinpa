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
                            <h3 class="mb-0 text-primary">
                                <i class="fas fa-plus-circle fa-fw me-2"></i>
                                إضافة تخصص جديد
                            </h3>
                            <p class="text-secondary mb-0">أدخل بيانات التخصص الجديد</p>
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

                    <form action="{{ route('type-specializations.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="tscode" class="form-label fw-bold">
                                        <i class="fas fa-hashtag text-primary me-2"></i>
                                        كود التخصص
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('tscode') is-invalid @enderror" 
                                           id="tscode" 
                                           name="tscode" 
                                           value="{{ old('tscode') }}" 
                                           placeholder="أدخل كود التخصص"
                                           required>
                                    @error('tscode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="tsname" class="form-label fw-bold">
                                        <i class="fas fa-pen text-primary me-2"></i>
                                        اسم التخصص
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('tsname') is-invalid @enderror" 
                                           id="tsname" 
                                           name="tsname" 
                                           value="{{ old('tsname') }}" 
                                           placeholder="أدخل اسم التخصص"
                                           required>
                                    @error('tsname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 mb-0">
                                <i class="fas fa-save me-2"></i>
                                حفظ التخصص
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
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .btn-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.3);
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
        color: #4e73df;
        margin-bottom: 0.5rem;
    }
    .alert {
        border: none;
        border-radius: 10px;
    }
    .invalid-feedback {
        font-size: 0.875em;
    }
</style>
@endpush
@endsection
