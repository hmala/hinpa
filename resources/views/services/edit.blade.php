@extends('layouts.master')
@section('title')
تعديل الخدمة
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

<!-- Custom Select2 CSS -->
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-right: 30px;
        color: #4a5568;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4e73df;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .select2-results__option {
        padding: 8px 12px;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #edf2ff;
    }
</style>
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
                                <i class="fas fa-edit fa-fw me-2"></i>
                                تعديل الخدمة
                            </h3>
                            <p class="text-secondary mb-0">تعديل بيانات الخدمة: {{ $service->sername }}</p>
                        </div>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-2"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('services.update', $service) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="sercode" class="form-label fw-bold">كود الخدمة</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                        <input type="number" 
                                               class="form-control @error('sercode') is-invalid @enderror" 
                                               id="sercode" 
                                               name="sercode" 
                                               value="{{ old('sercode', $service->sercode) }}" 
                                               required>
                                    </div>
                                    @error('sercode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="sername" class="form-label fw-bold">اسم الخدمة</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-pen"></i></span>
                                        <input type="text" 
                                               class="form-control @error('sername') is-invalid @enderror" 
                                               id="sername" 
                                               name="sername" 
                                               value="{{ old('sername', $service->sername) }}" 
                                               required>
                                    </div>
                                    @error('sername')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5">
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
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 15px;
    }
    .card-header {
        background: none;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .input-group-text {
        border: 1px solid #e2e8f0;
        background-color: #f8f9fa;
        color: #4e73df;
    }
    .form-label {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    .invalid-feedback {
        font-size: 0.875em;
    }
    .btn-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.3);
    }
    .btn-primary:hover {
        background: linear-gradient(45deg, #224abe, #4e73df);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.4);
    }
    .text-primary {
        color: #4e73df !important;
    }
</style>
@endpush
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#type_specialization_id').select2({
            placeholder: 'اختر التخصص',
            width: '100%',
            dir: 'rtl',
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                }
            }
        });
    });
</script>
@endsection
