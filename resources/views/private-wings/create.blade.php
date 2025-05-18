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
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">                <div class="card-header bg-gradient-purple text-center">
                    <h2 class="mb-0"><i class="fas fa-plus-circle"></i> إضافة حساب جناح خاص جديد</h2>
                </div>

                <div class="card-body" style="background-color: #f8f9fa;">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('private-wings.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- معلومات المريض -->
                        <div class="card mb-4">                            <div class="card-header bg-gradient-blue text-center">
                                <h4 class="mb-0"><i class="fas fa-user-injured"></i> معلومات المريض</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="hospital" class="form-label fw-bold">المستشفى</label>
                                        <input type="text" class="form-control shadow-sm" id="hospital" name="hospital" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="health_department" class="form-label fw-bold">دائرة صحة</label>
                                        <input type="text" class="form-control shadow-sm" id="health_department" name="health_department" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_name" class="form-label fw-bold">اسم المريض</label>
                                        <input type="text" class="form-control shadow-sm" id="patient_name" name="patient_name" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="file_number" class="form-label fw-bold">رقم الملف</label>
                                        <input type="text" class="form-control shadow-sm" id="file_number" name="file_number" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="statistical_number" class="form-label fw-bold">الرقم الإحصائي</label>
                                        <input type="text" class="form-control shadow-sm" id="statistical_number" name="statistical_number" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="days_count" class="form-label fw-bold">عدد الأيام</label>
                                        <input type="number" class="form-control shadow-sm" id="days_count" name="days_count" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="entry_date" class="form-label fw-bold">تاريخ الدخول</label>
                                        <input type="date" class="form-control shadow-sm" id="entry_date" name="entry_date" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="exit_date" class="form-label fw-bold">تاريخ الخروج</label>
                                        <input type="date" class="form-control shadow-sm" id="exit_date" name="exit_date">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الرسوم والأجور -->
                        <div class="card mb-4">                            <div class="card-header bg-gradient-green text-center">
                                <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> الرسوم والأجور</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_bed_fee" class="form-label fw-bold">أجور رقود المريض</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="patient_bed_fee" name="patient_bed_fee" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="companion_bed_fee" class="form-label fw-bold">أجور رقود المرافق</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="companion_bed_fee" name="companion_bed_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nutrition_fee" class="form-label fw-bold">أجور التغذية</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="nutrition_fee" name="nutrition_fee">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="medicine_supplies_fee" class="form-label fw-bold">أجور الأدوية والمستلزمات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="medicine_supplies_fee" name="medicine_supplies_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="laboratory_tests_fee" class="form-label fw-bold">أجور الفحوص المختبرية</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="laboratory_tests_fee" name="laboratory_tests_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="xray_fees" class="form-label fw-bold">أجور الفحوصات الشعاعية</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="xray_fees" name="xray_fees">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sonar_fees" class="form-label fw-bold">أجور فحوصات السونار</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="sonar_fees" name="sonar_fees">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات التأمينات -->
                        <div class="card mb-4">                            <div class="card-header bg-gradient-orange text-center">
                                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> معلومات التأمينات</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="deposit_amount" class="form-label fw-bold">مبلغ التأمينات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm" id="deposit_amount" name="deposit_amount" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="receipt_number" class="form-label fw-bold">رقم الوصل</label>
                                        <input type="text" class="form-control shadow-sm" id="receipt_number" name="receipt_number" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="receipt_date" class="form-label fw-bold">تاريخ الوصل</label>
                                        <input type="date" class="form-control shadow-sm" id="receipt_date" name="receipt_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg mx-2">
                                <i class="fas fa-save"></i> حفظ
                            </button>
                            <a href="{{ route('private-wings.index') }}" class="btn btn-secondary btn-lg mx-2">
                                <i class="fas fa-arrow-right"></i> رجوع
                            </a>
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
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
        padding: 1rem;
    }    .bg-gradient-purple {
        background: #fff;
        border-bottom: 2px solid #6f42c1;
    }
    .bg-gradient-blue {
        background: #fff;
        border-bottom: 2px solid #0d6efd;
    }
    .bg-gradient-green {
        background: #fff;
        border-bottom: 2px solid #198754;
    }
    .bg-gradient-orange {
        background: #fff;
        border-bottom: 2px solid #fd7e14;
    }
    .card-header h2, .card-header h4 {
        color: #000;
        font-weight: bold;
    }
    .card-header i {
        color: #666;
    }
    .form-control {
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .input-group-text {
        border-radius: 10px 0 0 10px;
        background: #f8f9fa;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        transform: translateY(-2px);
    }
    .btn {
        border-radius: 10px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    .card-header i {
        margin-left: 10px;
        font-size: 1.2em;
    }
    h4.mb-0 {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // تفعيل التحقق من الحقول
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endpush

@endsection
