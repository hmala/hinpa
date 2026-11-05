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
<!-- Custom CSS -->
<link href="{{ URL::asset('assets/css/custom/main.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/css/custom/private-wings.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/css/custom/print.css') }}" rel="stylesheet" />
<style>
    /* تصغير حجم الجداول والحقول للأناقة وحفظ المساحة */
    #services_table th, #services_table td,
    .receipt-row input, .receipt-row select {
        padding: 0.25rem 0.4rem;
        font-size: 0.82rem;
        line-height: 1.2;
        vertical-align: middle;
    }
    .form-control, .form-select, .input-group-text {
        padding: 0.25rem 0.45rem;
        font-size: 0.82rem;
        border-radius: 5px;
    }
    .form-label {
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 2px;
        color: #334155;
    }
    .btn {
        padding: 0.25rem 0.6rem;
        font-size: 0.82rem;
        border-radius: 5px;
    }
    .card {
        border-radius: 8px;
        margin-bottom: 0.75rem !important;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .bg-gradient-blue {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
        color: #ffffff !important;
    }
    .bg-gradient-green {
        background: linear-gradient(135deg, #064e3b 0%, #059669 100%) !important;
        color: #ffffff !important;
    }
    .bg-gradient-orange {
        background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%) !important;
        color: #ffffff !important;
    }
    .card-header-styled {
        padding: 0.55rem 0.95rem !important;
        border-top-left-radius: 7px !important;
        border-top-right-radius: 7px !important;
    }
    .card-main-title {
        font-size: 0.95rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.35) !important;
    }
    .card-main-title i {
        font-size: 1.05rem;
    }
    .card-header-badge {
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        border-radius: 20px !important;
        padding: 0.25rem 0.75rem !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
    }
    .card-header {
        padding: 0.4rem 0.8rem !important;
        border-bottom: 1px solid #e2e8f0;
    }
    .card-body {
        padding: 0.6rem 0.8rem !important;
    }

    /* خلايا الملخص المالي (Financial KPI Cells) */
    .kpi-cell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .kpi-cell .kpi-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .kpi-cell .kpi-input {
        background: transparent;
        border: none;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 0;
        box-shadow: none;
        color: #1e293b;
    }
    .kpi-cell.border-primary-subtle { border-right: 4px solid #3b82f6 !important; background: #f8fafc; }
    .kpi-cell.border-info-subtle    { border-right: 4px solid #06b6d4 !important; background: #f8fafc; }
    .kpi-cell.border-teal-subtle    { border-right: 4px solid #14b8a6 !important; background: #f8fafc; }
    .kpi-cell.border-warning-subtle { border-right: 4px solid #f59e0b !important; background: #fffdf5; }
    .kpi-cell.border-purple-subtle  { border-right: 4px solid #8b5cf6 !important; background: #faf5ff; }
    .kpi-cell.border-success-subtle { border-right: 4px solid #10b981 !important; background: #f0fdf4; }
    .kpi-cell.border-balance        { border-right: 4px solid #0284c7 !important; background: #f0f9ff; }

    /* خلايا الإدخال المصغرة والأنيقة الموحدة (بيانات المريض، الخدمات، الوصولات) */
    .patient-cell, .form-cell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 3px 6px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
        transition: all 0.2s ease;
    }
    .patient-cell:focus-within, .form-cell:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }
    .patient-cell .patient-cell-title, .form-cell .form-cell-title {
        font-size: 0.68rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 3px;
        white-space: nowrap;
    }
    .patient-cell .form-control, 
    .patient-cell .form-select,
    .form-cell .form-control, 
    .form-cell .form-select {
        height: 25px !important;
        padding: 1px 5px !important;
        font-size: 0.78rem !important;
        border-radius: 4px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
    }
    .patient-cell .form-control:focus, 
    .patient-cell .form-select:focus,
    .form-cell .form-control:focus, 
    .form-cell .form-select:focus {
        background: #ffffff;
        border-color: #3b82f6;
    }

    /* ضبط Select2 داخل الخلايا المصغرة */
    .form-cell .select2-container .select2-selection--single,
    .patient-cell .select2-container .select2-selection--single {
        height: 25px !important;
        padding: 0 4px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        background-color: #f8fafc !important;
        display: flex !important;
        align-items: center !important;
    }
    .form-cell .select2-container--default .select2-selection--single .select2-selection__rendered,
    .patient-cell .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 23px !important;
        font-size: 0.78rem !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        color: #334155 !important;
    }
    .form-cell .select2-container--default .select2-selection--single .select2-selection__arrow,
    .patient-cell .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 23px !important;
        top: 0 !important;
    }
    .form-cell .select2-container,
    .patient-cell .select2-container {
        width: 100% !important;
    }

    /* شريط إضافة الخدمة المدمج */
    .service-inline-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 6px;
        margin-bottom: 6px;
    }

    /* إخفاء أعمدة الضمان افتراضياً عند عدم وجود ضمان */
    @if(!$privateWing->is_insured)
    .insurance-header,
    .insurance-column {
        display: none;
    }
    @endif
</style>
@endsection
@section('content')
@php
    $services = $services ?? collect();
    $doctors = $doctors ?? collect();
@endphp
<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-2">
                <div class="card-header bg-gradient-purple text-white py-1 px-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fs-6"><i class="fas fa-edit me-1"></i> تعديل حساب جناح خاص</h5>
                    <span class="badge bg-white text-purple fw-bold">{{ $privateWing->annual_number }}</span>
                </div>
                <div class="card-body p-2" style="background-color: #f8f9fa;">
                    @if ($errors->any())
                        <div class="alert alert-danger py-1 px-2 mb-2 fs-7">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="private_wing_edit_form" action="{{ route('private-wings.update', $privateWing->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <!-- تنبيه استرجاع البيانات -->
                        <div id="restore-notification" class="alert alert-success alert-dismissible fade p-1 mb-1 fs-7" role="alert" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <strong>تم استرجاع البيانات المحفوظة</strong> من الجلسة السابقة
                            <button type="button" class="btn-close py-1" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <input type="hidden" name="fiscal_year" value="{{ $privateWing->fiscal_year }}">
                        
                        <!-- 1. بطاقة معلومات المريض (مضغوطة) -->
                        <!-- شريط الترويسة العلوي للبيانات التلقائية والأزرار في صف واحد مدمج -->
                        <div class="card mb-2 bg-light border shadow-sm">
                            <div class="card-body py-1.5 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-hospital text-primary me-1"></i> المستشفى: 
                                        <span class="badge bg-white text-primary border px-2 py-1">{{ $privateWing->hospital ?? (Auth::user()->fck ? Auth::user()->fck->Fckname : 'غير محدد') }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-landmark text-secondary me-1"></i> دائرة صحة / المقر: 
                                        <span class="badge bg-white text-secondary border px-2 py-1">{{ $privateWing->health_department ?? (Auth::user()->mohcode ? \App\Models\mohs::find(Auth::user()->mohcode)->mohname : 'مقر الوزارة') }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-calendar-check text-info me-1"></i> تاريخ التنظيم: 
                                        <span class="badge bg-white text-dark border px-2 py-1">{{ $privateWing->created_at ? $privateWing->created_at->format('Y-m-d') : date('Y-m-d') }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-hashtag text-warning me-1"></i> الترقيم السنوي ({{ $privateWing->fiscal_year }}): 
                                        <span class="badge bg-warning text-dark border px-2 py-1">{{ $privateWing->annual_number }}</span>
                                    </span>
                                    <input type="hidden" name="patient_type" id="patient_type" value="{{ $privateWing->patient_type ?? 'inpatient' }}">
                                    @if(($privateWing->patient_type ?? 'inpatient') == 'outpatient')
                                        <span class="badge bg-success text-white py-1 px-2 fs-7 shadow-sm"><i class="fas fa-user-clock me-1"></i> غير راقد</span>
                                    @else
                                        <span class="badge bg-primary text-white py-1 px-2 fs-7 shadow-sm"><i class="fas fa-bed me-1"></i> مريض راقد</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" form="private_wing_edit_form" class="btn btn-primary btn-sm px-3 py-1 fw-bold shadow-sm" id="submit_btn">
                                        <i class="fas fa-save me-1"></i> حفظ التعديلات والبيانات
                                    </button>
                                    <a href="{{ route('private-wings.index') }}" class="btn btn-secondary btn-sm px-3 py-1 shadow-sm">
                                        <i class="fas fa-arrow-right me-1"></i> رجوع
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- الحقول المخفية للبيانات التلقائية -->
                        <input type="hidden" id="hospital" name="hospital" value="{{ $privateWing->hospital }}">
                        <input type="hidden" id="health_department" name="health_department" value="{{ $privateWing->health_department }}">
                        <input type="hidden" id="list_date" name="created_at" value="{{ $privateWing->created_at ? $privateWing->created_at->format('Y-m-d') : date('Y-m-d') }}">
                        <input type="hidden" id="annual_number" name="annual_number" value="{{ $privateWing->annual_number }}">

                        <!-- 1. بطاقة إدخال بيانات المريض (نظام خلايا مصغرة وأنيقة) -->
                        <div class="card mb-2 shadow-sm">
                            <div class="card-header bg-gradient-blue text-white card-header-styled d-flex justify-content-between align-items-center">
                                <span class="card-main-title"><i class="fas fa-user-injured text-warning"></i> أولاً: بيانات ومعلومات المريض والإقامة</span>
                                <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-user-edit me-1"></i> تعديل البيانات</span>
                            </div>
                            <div class="card-body p-1.5">
                                <!-- الصف الأول: البيانات الشخصية والتعريفية (خلايا مصغرة) -->
                                <div class="row g-1 mb-1">
                                    <div class="col-md-4">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-user text-primary"></i> اسم المريض <span class="text-danger">*</span></div>
                                            <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ $privateWing->patient_name }}" placeholder="الاسم الرباعي للمريض" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="statistical_number_container">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-id-card text-secondary"></i> الرقم الإحصائي <span class="text-danger">*</span></div>
                                            <input type="text" class="form-control" id="statistical_number" name="statistical_number" value="{{ $privateWing->statistical_number }}" placeholder="الرقم الإحصائي" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-phone text-success"></i> رقم الهاتف</div>
                                            <input type="tel" class="form-control" id="patient_phone" name="patient_phone" value="{{ $privateWing->patient_phone ?? '' }}" pattern="[0-9\-\s\+]+" placeholder="07xxxxxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-globe text-info"></i> جنسية المريض</div>
                                            <select class="form-select form-control" id="patient_nationality" name="patient_nationality">
                                                <option value="عراقي" {{ $privateWing->patient_nationality == 'عراقي' ? 'selected' : '' }}>عراقي</option>
                                                <option value="عربي" {{ $privateWing->patient_nationality == 'عربي' ? 'selected' : '' }}>عربي</option>
                                                <option value="أجنبي" {{ $privateWing->patient_nationality == 'أجنبي' ? 'selected' : '' }}>أجنبي</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- الصف الثاني: الإقامة والتواريخ والضمان والاستثناءات (خلايا مصغرة في سطر واحد) -->
                                <div class="row g-1" id="time_and_days_row">
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-sign-in-alt text-success"></i> تاريخ ووقت الدخول <span class="text-danger">*</span></div>
                                            <div class="input-group input-group-sm">
                                                <input type="date" class="form-control" id="entry_date" name="entry_date" value="{{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}" required>
                                                <input type="time" class="form-control" id="entry_time" name="entry_time" value="{{ $privateWing->entry_time ? \Carbon\Carbon::parse($privateWing->entry_time)->format('H:i') : '00:00' }}" style="max-width: 65px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-sign-out-alt text-danger"></i> تاريخ ووقت الخروج</div>
                                            <div class="input-group input-group-sm">
                                                <input type="date" class="form-control" id="exit_date" name="exit_date" value="{{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}">
                                                <input type="time" class="form-control" id="exit_time" name="exit_time" value="{{ $privateWing->exit_time ? \Carbon\Carbon::parse($privateWing->exit_time)->format('H:i') : '00:00' }}" style="max-width: 65px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-clock text-warning"></i> الأيام <span class="text-danger">*</span></div>
                                            <input type="number" class="form-control text-center fw-bold bg-light" id="days_count" name="days_count" value="{{ $privateWing->days_count }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell d-flex flex-column align-items-center justify-content-center px-1">
                                            <div class="patient-cell-title mb-1" style="font-size: 0.64rem;"><i class="fas fa-shield-alt text-secondary"></i> ضمان</div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="is_insured" name="is_insured" value="1" {{ $privateWing->is_insured == 1 ? 'checked' : '' }} style="cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-building text-secondary"></i> جهة الاستثناء</div>
                                            <input type="text" class="form-control" id="exception_from" name="exception_from" value="{{ $privateWing->exception_from ?? '' }}" placeholder="جهة الاستثناء">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-percent text-info"></i> الاستثناء %</div>
                                            <input type="number" class="form-control text-center fw-bold px-1" id="exception_percentage" name="exception_percentage" value="{{ $privateWing->exception_percentage ?? '' }}" min="0" max="100" step="0.01" placeholder="0 %">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="patient-cell" id="exception_attachment_container">
                                            <div class="patient-cell-title d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-paperclip text-purple"></i> كتاب الاستثناء</span>
                                                <span id="exception_badge" class="badge bg-success text-white py-0 px-1" style="{{ ($privateWing->exception_percentage ?? 0) >= 100 ? '' : 'display: none;' }} font-size: 0.65rem;">معفى 100%</span>
                                                @if(!empty($privateWing->exception_attachment))
                                                    <a href="{{ asset($privateWing->exception_attachment) }}" target="_blank" class="badge bg-info text-white py-0 px-1 text-decoration-none" title="معاينة الملف المرفوع">
                                                        <i class="fas fa-eye me-1"></i> عرض المرفق
                                                    </a>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control form-control-sm" id="exception_attachment" name="exception_attachment" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. شبكة ثنائية الأعمدة (الخدمات على اليمين + الوصولات على اليسار) -->
                        <div class="row g-2 mb-2">
                            <!-- العمود الأيمن: الرسوم والخدمات المقدمة -->
                            <div class="col-lg-6">
                                <div class="card h-100 mb-0 shadow-sm">
                                    <div class="card-header bg-gradient-blue text-white card-header-styled d-flex justify-content-between align-items-center">
                                        <span class="card-main-title"><i class="fas fa-hand-holding-medical text-warning"></i> ثانياً: الرسوم والخدمات الطبية المقدمة</span>
                                        <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-plus-circle me-1"></i> إضافة وتعديل الخدمات</span>
                                    </div>
                                    <div class="card-body p-2 d-flex flex-column">
                                        <!-- شريط إضافة خدمة أفقي سريع بنظام الخلايا -->
                                        <div class="service-inline-bar mb-2 p-1.5 bg-light border rounded">
                                            <div class="row g-1 mb-1">
                                                <div class="col-4">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-layer-group text-info"></i> درجة العملية</div>
                                                        <select name="operation_degree" id="operation_degree" class="form-control select2">
                                                            <option value="">اختر الدرجة...</option>
                                                            <option value="الخاصة">الخاصة</option>
                                                            <option value="فوق الكبرى">فوق الكبرى</option>
                                                            <option value="كبرى">كبرى</option>
                                                            <option value="متوسطة">متوسطة</option>
                                                            <option value="صغرى">صغرى</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="section_id" id="section_id" value="8">
                                                <div class="col-4">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-stethoscope text-secondary"></i> التخصص الفرعي</div>
                                                        <select id="fckn" name="fckn" class="form-control select2">
                                                            <option value="" disabled selected>--حدد التخصص--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-hand-holding-medical text-primary"></i> الخدمة</div>
                                                        <select id="fckr" name="fckr" class="form-control select2">
                                                            <option value="" disabled selected>--حدد الخدمة--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-1 align-items-stretch">
                                                <div class="col-5">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title text-primary"><i class="fas fa-user-md text-primary"></i> <span id="doctor_id_label">اسم القائم بالعمل</span></div>
                                                        <select id="doctor_id" name="doctor_id" class="form-control select2">
                                                            <option value="">اختر القائم بالعمل...</option>
                                                            @foreach($doctors as $doctor)
                                                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title text-success"><i class="fas fa-tag text-success"></i> <span id="service_fee_label">مبلغ الخدمة (د.ع)</span></div>
                                                        <input type="number" step="0.01" class="form-control fw-bold" id="service_fee" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-3 d-flex align-items-stretch">
                                                    <button type="button" class="btn btn-primary w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center py-1" id="add_service" style="min-height: 44px; font-size: 0.8rem;">
                                                        <i class="fas fa-plus-circle me-1"></i> إضافة الخدمة
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- جدول الخدمات مع سكرول داخلي لطيف -->
                                        <div class="table-responsive flex-grow-1" style="max-height: 220px; min-height: 180px; overflow-y: auto;">
                                            <table class="table table-bordered table-sm mb-0" id="services_table">
                                                <thead class="bg-light sticky-top">
                                                    <tr>
                                                        <th>الخدمة</th>
                                                        <th>القائم بالعمل</th>
                                                        <th>الدرجة</th>
                                                        <th>اليومي</th>
                                                        <th>الأيام</th>
                                                        <th class="insurance-header">ضمان</th>
                                                        <th class="insurance-header">مريض%</th>
                                                        <th>الإجمالي</th>
                                                        <th>المريض</th>
                                                        <th>الضمان</th>
                                                        <th style="width: 35px;">حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($services as $service)
                                                    @php
                                                        $totalFee = $service->is_daily ? $service->service_fee * $privateWing->days_count : $service->service_fee;
                                                        $insurancePercentage = $service->insurance_percentage ?? 10;
                                                        
                                                        if ($service->is_insured && $privateWing->is_insured) {
                                                            $patientAmount = $totalFee * ($insurancePercentage / 100);
                                                            $insuranceAmount = $totalFee * ((100 - $insurancePercentage) / 100);
                                                        } else {
                                                            $patientAmount = $totalFee;
                                                            $insuranceAmount = 0;
                                                        }
                                                    @endphp
                                                    <tr class="existing-service bg-light" data-service-id="{{ $service->id }}">
                                                        <td>{{ $service->service_name }}</td>
                                                        <td>{{ $service->doctor_name ?? '' }}</td>
                                                        <td>{{ $service->operation_degree ?? '' }}</td>
                                                        <td>{{ number_format($service->service_fee, 2) }} د.ع</td>
                                                        <td style="text-align: center;">{{ $service->is_daily ? 'نعم' : 'لا' }}</td>
                                                        <td class="insurance-column" style="text-align: center;">{{ $service->is_insured ? 'نعم' : 'لا' }}</td>
                                                        <td class="insurance-column" style="text-align: center;">{{ $insurancePercentage }}%</td>
                                                        <td id="existing-service-total-{{ $service->id }}">{{ number_format($totalFee, 2) }} د.ع</td>
                                                        <td id="existing-service-patient-{{ $service->id }}">{{ number_format($patientAmount, 2) }} د.ع</td>
                                                        <td id="existing-service-insurance-{{ $service->id }}">{{ number_format($insuranceAmount, 2) }} د.ع</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-secondary btn-sm py-0 px-1" disabled title="لا يمكن حذف الخدمات الموجودة"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-light fw-bold sticky-bottom">
                                                    <tr>
                                                        <th colspan="5" class="text-end py-1">المجموع:</th>
                                                        <th class="insurance-header"></th>
                                                        <th class="insurance-header"></th>
                                                        <th id="total_amount" class="text-primary py-1">0 د.ع</th>
                                                        <th id="total_patient_amount" class="text-info py-1">0 د.ع</th>
                                                        <th id="total_insurance_amount" class="text-secondary py-1">0 د.ع</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- العمود الأيسر: مخصص بالكامل لعرض وإدخال وصولات التأمينات -->
                            <div class="col-lg-6">
                                <div class="card h-100 mb-0 shadow-sm">
                                    <div class="card-header bg-gradient-blue text-white card-header-styled d-flex justify-content-between align-items-center">
                                        <span class="card-main-title"><i class="fas fa-receipt text-warning"></i> ثالثاً: وصولات التأمينات والمبالغ المدفوعة</span>
                                        <button type="button" class="btn btn-light btn-sm py-1 px-3 fw-bold text-primary shadow-sm" id="add-receipt" style="font-size: 0.78rem;">
                                            <i class="fas fa-plus-circle me-1 text-primary"></i> إضافة وصل جديد
                                        </button>
                                    </div>
                                    <div class="card-body p-2 d-flex flex-column">
                                        <!-- شريط عناوين الأعمدة للوصولات -->
                                        <div class="row g-1 bg-light p-1 rounded border mb-1 fw-bold text-muted" style="font-size: 0.72rem;">
                                            <div class="col-3 text-center">المبلغ (د.ع)</div>
                                            <div class="col-3 text-center">رقم الوصل</div>
                                            <div class="col-3 text-center">التاريخ</div>
                                            <div class="col-2 text-center">طريقة الدفع</div>
                                            <div class="col-1 text-center">إجراء</div>
                                        </div>

                                        <!-- حاوية قائمة الوصولات المسجلة والجديدة -->
                                        <div id="receipts-container" class="flex-grow-1" style="max-height: 220px; min-height: 180px; overflow-y: auto;">
                                            @foreach($privateWing->receipts as $index => $receipt)
                                            <div class="row g-1 receipt-row mb-1 existing-receipt align-items-center p-1 rounded bg-light border" data-receipt-id="{{ $receipt->id }}" data-amount="{{ $receipt->amount }}">
                                                <div class="col-3">
                                                    <input type="text" class="form-control form-control-sm bg-white p-1 fw-bold text-success text-center" value="{{ number_format($receipt->amount, $receipt->amount == intval($receipt->amount) ? 0 : 2) }} د.ع" readonly disabled>
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" class="form-control form-control-sm bg-white p-1 text-center" value="{{ $receipt->receipt_number }}" readonly disabled placeholder="رقم">
                                                </div>
                                                <div class="col-3">
                                                    <input type="date" class="form-control form-control-sm bg-white p-0 text-center" value="{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '' }}" readonly disabled>
                                                </div>
                                                <div class="col-2">
                                                    <input type="text" class="form-control form-control-sm bg-white p-1 text-center" value="{{ $receipt->payment_type }}" readonly disabled>
                                                </div>
                                                <div class="col-1 text-center p-0">
                                                    <button type="button" class="btn btn-secondary btn-sm py-0 px-1" disabled title="محفوظ في السجل"><i class="fas fa-lock"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. آخر الصفحة: بطاقة الإجماليات والملخص المالي النهائي والاعتماد (Full Width) -->
                        <div class="card mb-0 shadow-sm border-primary">
                            <div class="card-header bg-gradient-blue text-white card-header-styled d-flex justify-content-between align-items-center">
                                <span class="card-main-title"><i class="fas fa-calculator text-warning"></i> رابعاً: الملخص والحسابات المالية النهائية</span>
                                <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-balance-scale me-1"></i> احتساب وتصفية</span>
                            </div>
                            <div class="card-body p-2">
                                <!-- صف خلايا الـ KPI السبعة -->
                                <div class="row g-1">
                                    <div class="col-md">
                                        <div class="kpi-cell border-primary-subtle py-1">
                                            <div class="kpi-title"><i class="fas fa-calculator text-primary"></i> إجمالي الخدمات</div>
                                            <input type="text" class="kpi-input text-primary" id="total_services_provided" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-info-subtle py-1">
                                            <div class="kpi-title"><i class="fas fa-user text-info"></i> مبلغ المريض</div>
                                            <input type="text" class="kpi-input text-info" id="total_services" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-teal-subtle py-1">
                                            <div class="kpi-title"><i class="fas fa-shield-alt text-teal"></i> مبلغ الضمان</div>
                                            <input type="text" class="kpi-input text-secondary" id="total_insurance_summary" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-warning-subtle py-1">
                                            <div class="kpi-title text-warning"><i class="fas fa-coins text-warning"></i> الفائدة (د.ع)</div>
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm fw-bold border-warning text-warning p-0 text-center" id="interest_amount" name="interest_amount" placeholder="0" value="{{ $privateWing->interest_amount ?? 0 }}" style="background: #fff; height: 24px;">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-purple-subtle py-1">
                                            <div class="kpi-title text-purple"><i class="fas fa-file-invoice-dollar text-purple"></i> المطلوب الكلي</div>
                                            <input type="text" class="kpi-input text-purple" id="grand_total_with_interest" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-success-subtle py-1">
                                            <div class="kpi-title text-success"><i class="fas fa-hand-holding-usd text-success"></i> إجمالي الوصولات</div>
                                            <input type="text" class="kpi-input text-success" id="total_deposits" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="kpi-cell border-balance py-1">
                                            <div class="kpi-title"><i class="fas fa-balance-scale text-primary"></i> المتبقي / الاسترداد</div>
                                            <input type="text" class="kpi-input fw-bold" id="remaining_amount" value="0 د.ع" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<!-- Internal Select2 js-->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
// دالة لتنسيق المبالغ المالية بالفواصل
function formatNumber(number) {
    if (number === null || number === undefined || isNaN(number)) return '0';
    var num = parseFloat(number);
    return num.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}
window.formatNumber = formatNumber;

// متغيرات عامة
var totalDepositsAmount = 0;
var totalAmount = 0;

// دالة لحساب الأيام بنظام فنادق
function calculateDays() {
    var entryDate = $('#entry_date').val();
    var exitDate = $('#exit_date').val();
    var exitTime = $('#exit_time').val() || '00:00';

    if (entryDate && exitDate) {
        var entry = new Date(entryDate);
        var exit = new Date(exitDate);
        var diff = exit - entry;
        var diffDays = Math.ceil(diff / (1000 * 60 * 60 * 24));
        if (diffDays < 0) {
            alert('تاريخ الخروج لا يمكن أن يكون قبل تاريخ الدخول');
            $('#exit_date').val('');
            $('#days_count').val(1);
        } else {
            var days = diffDays;
            if (exitTime >= '12:00') days += 1; // خروج بعد الظهر
            if (days < 1) days = 1;
            $('#days_count').val(days);
        }
    }
}

$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: 'ابحث أو اختر...',
        dropdownAutoWidth: true,
        minimumResultsForSearch: 0
    });

    // متغير لتتبع عدد الوصولات القديمة
    const oldReceiptsCount = {{ $privateWing->receipts->count() }};

    // عند تغيير التواريخ أو الأوقات نعيد حساب الأيام
    $('#entry_date, #exit_date, #entry_time, #exit_time').change(function() {
        calculateDays();
    });

    // إعداد أولي لقيمة الأيام عند فتح النموذج
    calculateDays();
    
    // إضافة وصل جديد
    $('#add-receipt').click(function() {
        // index يبدأ من عدد الوصولات القديمة + عدد الوصولات الجديدة
        const newReceiptsCount = $('.receipt-row:not(.existing-receipt)').length;
        const index = oldReceiptsCount + newReceiptsCount;
        const newRow = `
            <div class="row g-1 receipt-row new-receipt mb-1 align-items-center p-1 rounded bg-white border border-success-subtle shadow-sm">
                <div class="col-3">
                    <input type="number" step="0.01" class="form-control form-control-sm p-1" name="deposits[${index}][amount]" placeholder="المبلغ (د.ع)" required>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control form-control-sm p-1" name="deposits[${index}][number]" placeholder="رقم الوصل" required>
                </div>
                <div class="col-3">
                    <input type="date" class="form-control form-control-sm p-0 text-center" name="deposits[${index}][date]" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-2">
                    <select class="form-control form-control-sm px-1" name="deposits[${index}][payment_type]" required>
                        <option value="كاش" selected>كاش</option>
                        <option value="إلكتروني">إلكتروني</option>
                    </select>
                </div>
                <div class="col-1 text-center p-0">
                    <button type="button" class="btn btn-danger btn-sm py-0 px-1 remove-receipt" title="حذف الوصل"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `;
        $('#receipts-container').append(newRow);
        updateTotalDeposit();
    });

    // حذف وصل
    $(document).on('click', '.remove-receipt', function() {
        const receiptRow = $(this).closest('.receipt-row');
        const receiptId = receiptRow.data('receipt-id');

        // إذا كان الوصل موجود في قاعدة البيانات (لديه ID)، لا نسمح بحذفه
        if (receiptId) {
            alert('لا يمكن حذف الوصولات الموجودة. يمكنك فقط إضافة وصولات جديدة.');
            return;
        }

        // إذا كان وصلاً جديداً (لا يوجد ID)، يمكن حذفه
        receiptRow.remove();
        updateTotalDeposit();
    });

    // تحديث إجمالي مبلغ التأمينات
    window.updateTotalDeposit = function() {
        let totalDeposits = 0;  // مجموع مبالغ الوصولات فقط
        
        // حساب الوصولات القديمة من data attributes
        $('.existing-receipt').each(function() {
            const amount = parseFloat($(this).data('amount') || 0);
            totalDeposits += amount;
        });
        
        // حساب الوصولات الجديدة فقط (التي ليس لها class existing-receipt)
        $('.receipt-row:not(.existing-receipt)').each(function() {
            const amount = parseFloat($(this).find('input[name*="[amount]"]').val() || 0);
            totalDeposits += amount;
        });
        
        totalDepositsAmount = totalDeposits;
        $('#total_deposits').val(formatNumber(totalDepositsAmount) + ' د.ع');
        updateRemaining();
    }

    // تحديث المبلغ المتبقي
    window.updateRemaining = function() {
        // التحقق من وجود المصفوفات
        if (typeof window.existingServices === 'undefined') {
            window.existingServices = [];
        }
        if (typeof window.servicesData === 'undefined') {
            window.servicesData = [];
        }
        
        // حساب مبلغ المريض (بدون الضمان)
        var isPatientInsured = $('#is_insured').is(':checked');
        var isOutpatient = ($('#patient_type').val() || $('input[name="patient_type"]:checked').val()) === 'outpatient';
        var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
        var totalPatientAmount = 0;
        var totalInsuranceAmount = 0;
        
        // حساب من الخدمات الموجودة
        window.existingServices.forEach(function(service) {
            var serviceTotal = (service.isDaily && !isOutpatient) ? 
                parseFloat(service.fee) * daysCount : 
                parseFloat(service.fee);
            
            var patientPercentage = service.insurancePercentage || 10;
            
            if (service.isInsured && isPatientInsured) {
                totalPatientAmount += serviceTotal * (patientPercentage / 100);
                totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
            } else {
                totalPatientAmount += serviceTotal;
            }
        });
        
        // حساب من الخدمات الجديدة
        window.servicesData.forEach(function(service) {
            var serviceTotal = (service.isDaily && !isOutpatient) ? 
                parseFloat(service.fee) * daysCount : 
                parseFloat(service.fee);
            
            var patientPercentage = service.insurancePercentage || 10;
            
            if (service.isInsured && isPatientInsured) {
                totalPatientAmount += serviceTotal * (patientPercentage / 100);
                totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
            } else {
                totalPatientAmount += serviceTotal;
            }
        });
        
        // تطبيق نسبة الاستثناء على مبلغ المريض
        var exceptionPercentage = parseFloat($('#exception_percentage').val()) || 0;
        if (exceptionPercentage > 0) {
            totalPatientAmount = totalPatientAmount * (1 - exceptionPercentage / 100);
        }
        
        // حساب إجمالي الخدمات المقدمة (الإجمالي الكلي)
        var totalServicesProvided = totalPatientAmount + totalInsuranceAmount;
        
        // حساب الفائدة المضافة يدوياً
        var interestAmount = parseFloat($('#interest_amount').val()) || 0;
        var grandTotalRequired = totalPatientAmount + interestAmount;
        
        // حساب مبلغ الوصولات
        let totalDeposits = 0;
        $('.existing-receipt').each(function() {
            totalDeposits += parseFloat($(this).data('amount') || 0);
        });
        $('.receipt-row:not(.existing-receipt)').each(function() {
            totalDeposits += parseFloat($(this).find('input[name*="[amount]"]').val() || 0);
        });
        
        // المعادلة: المبلغ المتبقي = إجمالي الوصولات - المبلغ المطلوب شامل الفائدة
        const remaining = totalDeposits - grandTotalRequired;
        
        $('#total_services_provided').val(formatNumber(totalServicesProvided) + ' د.ع');
        $('#total_services').val(formatNumber(totalPatientAmount) + ' د.ع');
        $('#total_insurance_summary').val(formatNumber(totalInsuranceAmount) + ' د.ع');
        $('#grand_total_with_interest').val(formatNumber(grandTotalRequired) + ' د.ع');
        $('#total_deposits').val(formatNumber(totalDeposits) + ' د.ع');
        
        var remainingText = formatNumber(Math.abs(remaining)) + ' د.ع ';
        if (remaining < 0) {
            remainingText += '(متبقي عليه)';
            $('#remaining_amount').removeClass('text-success').addClass('text-danger');
        } else if (remaining > 0) {
            remainingText += '(متبقي له / استرداد)';
            $('#remaining_amount').removeClass('text-danger').addClass('text-success');
        } else {
            remainingText += '(خالص)';
            $('#remaining_amount').removeClass('text-danger text-success');
        }
        $('#remaining_amount').val(remainingText);
    }

    // تحديث الإجمالي عند تغيير أي مبلغ
    $(document).on('input', 'input[name$="[amount]"]', function() {
        updateTotalDeposit();
    });

    // تحديث عند تغيير مبلغ الفائدة
    $('#interest_amount').on('input change', function() {
        updateRemaining();
    });

    // سيتم استدعاء updateTotalDeposit عند تحميل الصفحة من الدالة الثانية
});</script>
<script>
    $(document).ready(function() {
        var initialPatientType = '{{ $privateWing->patient_type ?? "inpatient" }}';
        function toggleTimeAndDaysRow() {
            var patientType = $('#patient_type').val() || initialPatientType;
            if (patientType === 'outpatient') {
                $('#time_and_days_row').hide();
                $('#entry_date').removeAttr('required');
                if (!$('#entry_date').val()) {
                    $('#entry_date').val('{{ date("Y-m-d") }}');
                }
                $('#days_count').val('0');
                $('#statistical_number_container').hide();
                $('#statistical_number').removeAttr('required');
            } else {
                $('#time_and_days_row').show();
                $('#entry_date').attr('required', true);
                if ($('#days_count').val() == '0') {
                    $('#days_count').val('1');
                }
                $('#statistical_number_container').show();
                $('#statistical_number').attr('required', true);
            }
        }

        $('input[name="patient_type"]').change(toggleTimeAndDaysRow);
        toggleTimeAndDaysRow();

        // 1. تحميل قائمة التخصصات الفرعية مباشرة باستخدام AJAX المعتمد في الزيارات
        function loadSubSpecializations(filterSection) {
            var targetSection = filterSection || $('#section_id').val() || 8;
            var fcknSelect = $('#fckn');
            var fckrSelect = $('#fckr');

            fcknSelect.empty().append('<option value="" disabled selected>جاري التحميل...</option>');
            fckrSelect.empty().append('<option value="" disabled selected>--حدد الخدمة--</option>');

            $.ajax({
                url: "{{ route('private-visits.get-specializations') }}",
                type: "GET",
                data: { section_id: targetSection },
                success: function(data) {
                    fcknSelect.empty().append('<option value="" disabled selected>--حدد التخصص الفرعي--</option>');
                    $.each(data, function(key, value) {
                        var text = (value.sercode ? value.sercode + ' - ' : '') + (value.sername || 'تخصص فرعي');
                        var valId = value.sercode || value.id;
                        fcknSelect.append('<option value="' + valId + '">' + text + '</option>');
                    });
                    fcknSelect.prop('disabled', false);
                    fcknSelect.select2({
                        width: '100%',
                        placeholder: '--حدد التخصص الفرعي--',
                        dropdownAutoWidth: true,
                        minimumResultsForSearch: 0
                    }).trigger('change.select2');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching specializations:', error);
                    fcknSelect.empty().append('<option value="" disabled selected>--حدد التخصص الفرعي--</option>');
                    fcknSelect.prop('disabled', false);
                    fcknSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                }
            });
        }

        function isDoctorRequiredService(serviceName) {
            serviceName = (serviceName || '').trim();
            if (!serviceName) {
                return false;
            }

            var doctorServices = [
                'اجور عمليات',
                'اجور زيارات علاجية',
                'اجورالخدمات الطبية المقدمة للعرب والأجانب غير المقيمين',
                'العيون',
                'تمريض وتداوي'
            ];

            if (doctorServices.includes(serviceName)) {
                return true;
            }

            if (/اجور\s+(.*\b)?(الطبيب|المساعد|طبيبة|الطبيبة|الجراح)/i.test(serviceName)) {
                return true;
            }

            return false;
        }

        function toggleDoctorField() {
            var serviceName = $('#fckr option:selected').text();
            var required = isDoctorRequiredService(serviceName);

            $('#doctor_id').prop('disabled', false);
            if (required) {
                $('#doctor_id_label').html('<i class="fas fa-user-md me-1"></i> اسم القائم بالعمل <span class="text-danger">*</span>');
            } else {
                $('#doctor_id_label').html('<i class="fas fa-user-md me-1"></i> اسم القائم بالعمل (اختياري)');
            }
            $('#service_fee_label').html('<i class="fas fa-tag me-1"></i> مبلغ الخدمة (د.ع)');
        }

        $('#fckr').change(toggleDoctorField);
        $('#fckn').change(toggleDoctorField);

        // استدعاء فوري لقائمة خدمات التخصص "عام"
        loadSubSpecializations(8);

        // تغيير التخصص الرئيسي يفلتر التخصصات الفرعية
        $('#section_id').change(function() {
            var sectionId = $(this).val();
            loadSubSpecializations(sectionId);
            $('#fckr').empty().append('<option value="" disabled selected>--حدد الخدمة--</option>').select2({
                width: '100%',
                placeholder: '--حدد الخدمة--',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 0
            }).trigger('change.select2');
            $('#service_fee').val('');
            $('#doctor_id').val('').trigger('change.select2');
        });

        // تغيير التخصص الفرعي يجلب الخدمات المرتبطة به
        $('#fckn').on('change', function() {
            var serviceId = $(this).val();
            var fckrSelect = $('#fckr');
            $('#service_fee').val('');

            fckrSelect.empty().append('<option value="" disabled selected>جاري التحميل...</option>');

            if (serviceId) {
                $.ajax({
                    url: "{{ route('private-visits.get-service-specializations') }}",
                    type: "GET",
                    data: { service_id: serviceId },
                    success: function(data) {
                        fckrSelect.empty().append('<option value="" disabled selected>--حدد الخدمة--</option>');
                        $.each(data, function(key, value) {
                            var text = (value.codesv ? value.codesv + ' - ' : '') + value.namesv;
                            fckrSelect.append('<option value="' + value.id + '">' + text + '</option>');
                        });
                        fckrSelect.select2({
                            width: '100%',
                            placeholder: '--حدد الخدمة--',
                            dropdownAutoWidth: true,
                            minimumResultsForSearch: 0
                        }).trigger('change.select2');
                        if (typeof toggleDoctorField === 'function') {
                            toggleDoctorField();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching service specializations:', error);
                        fckrSelect.empty().append('<option value="" disabled selected>--حدد الخدمة--</option>');
                        fckrSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                    }
                });
            } else {
                fckrSelect.empty().append('<option value="" disabled selected>--حدد الخدمة--</option>');
                fckrSelect.select2({
                    width: '100%',
                    placeholder: '--حدد الخدمة--',
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 0
                }).trigger('change.select2');
            }
        });

        // تهيئة المتغيرات العامة
        window.servicesData = [];

        // دالة لحفظ البيانات في localStorage
        window.saveToLocalStorage = function() {
            try {
                localStorage.setItem('privateWing_edit_services', JSON.stringify(window.servicesData));
                localStorage.setItem('privateWing_edit_isInsured', $('#is_insured').is(':checked') ? '1' : '0');
                localStorage.setItem('privateWing_edit_daysCount', $('#days_count').val());
            } catch (e) {
                console.error('خطأ في حفظ البيانات:', e);
            }
        }

        // دالة لاسترجاع البيانات من localStorage
        window.loadFromLocalStorage = function() {
            try {
                var savedServices = localStorage.getItem('privateWing_edit_services');
                var savedDaysCount = localStorage.getItem('privateWing_edit_daysCount');
                
                // ملاحظة: لا نسترجع حالة الضمان من localStorage في صفحة التعديل
                // لأنها يجب أن تأتي من قاعدة البيانات فقط
                
                // استرجاع عدد الأيام
                if (savedDaysCount) {
                    $('#days_count').val(savedDaysCount);
                }
                
                // استرجاع الخدمات
                if (savedServices) {
                    try {
                        window.servicesData = JSON.parse(savedServices);
                        updateServicesTable();
                        updateTotal();
                        toggleInsuranceColumn();
                    } catch (e) {
                        console.error('خطأ في تحليل بيانات الخدمات:', e);
                        window.servicesData = [];
                    }
                }
                
                if (savedServices) {
                    console.log('✓ تم استرجاع البيانات المحفوظة');
                    // إظهار التنبيه على الشاشة
                    var notification = $('#restore-notification');
                    notification.show().addClass('show');
                    // إخفاء التنبيه بعد 5 ثواني
                    setTimeout(function() {
                        notification.removeClass('show').fadeOut();
                    }, 5000);
                }
            } catch (e) {
                console.error('خطأ في استرجاع البيانات:', e);
            }
        }

        // دالة لمسح البيانات المحفوظة
        window.clearLocalStorage = function() {
            localStorage.removeItem('privateWing_edit_services');
            localStorage.removeItem('privateWing_edit_isInsured');
            localStorage.removeItem('privateWing_edit_daysCount');
            console.log('✓ تم مسح البيانات المحفوظة');
        }

        // دالة لتنسيق الأرقام
        window.formatNumber = formatNumber;

        // دالة لتحديث المجموع الكلي
        window.updateTotal = function() {
            var isOutpatient = ($('#patient_type').val() || $('input[name="patient_type"]:checked').val()) === 'outpatient';
            var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
            var isPatientInsured = $('#is_insured').is(':checked');
            
            totalAmount = 0;
            var totalPatientAmount = 0;
            var totalInsuranceAmount = 0;
            
            // حساب الخدمات الموجودة
            window.existingServices.forEach(function(service) {
                var serviceTotal = (service.isDaily && !isOutpatient) ?
                    parseFloat(service.fee) * daysCount :
                    parseFloat(service.fee);
                
                totalAmount += serviceTotal;
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
                var patientPercentage = service.insurancePercentage || 10;
                
                if (service.isInsured && isPatientInsured) {
                    var patientAmount = serviceTotal * (patientPercentage / 100);
                    var insuranceAmount = serviceTotal * ((100 - patientPercentage) / 100);
                    totalPatientAmount += patientAmount;
                    totalInsuranceAmount += insuranceAmount;
                } else {
                    totalPatientAmount += serviceTotal;
                }
            });
            
            // حساب الخدمات الجديدة
            window.servicesData.forEach(function(service) {
                var serviceTotal = service.isDaily ?
                    parseFloat(service.fee) * daysCount :
                    parseFloat(service.fee);
                
                totalAmount += serviceTotal;
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
                var patientPercentage = service.insurancePercentage || 10;
                
                if (service.isInsured && isPatientInsured) {
                    totalPatientAmount += serviceTotal * (patientPercentage / 100);
                    totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
                } else {
                    totalPatientAmount += serviceTotal;
                }
            });

            // تطبيق نسبة الاستثناء على مبلغ المريض
            var exceptionPercentage = parseFloat($('#exception_percentage').val()) || 0;
            if (exceptionPercentage > 0) {
                totalPatientAmount = totalPatientAmount * (1 - exceptionPercentage / 100);
            }

            // تحديث عرض المجموع في الجدول
            var totalServicesProvided = totalPatientAmount + totalInsuranceAmount;
            
            $('#total_amount').text(formatNumber(totalServicesProvided) + ' د.ع');
            $('#total_patient_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_insurance_amount').text(formatNumber(totalInsuranceAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_services_provided').val(formatNumber(totalServicesProvided) + ' د.ع');
            $('#total_insurance_summary').val(formatNumber(totalInsuranceAmount) + ' د.ع');
            updateRemaining();
        }

        // دالة لإظهار/إخفاء عمود الضمان
        window.toggleInsuranceColumn = function() {
            var isPatientInsured = $('#is_insured').is(':checked');
            
            if (isPatientInsured) {
                $('.insurance-header').show();
                $('.insurance-column').show();
            } else {
                $('.insurance-header').hide();
                $('.insurance-column').hide();
            }
        }

        // دالة لتحديث جدول الخدمات
        window.updateServicesTable = function() {
            var tbody = $('#services_table tbody');
            // حذف الصفوف الجديدة فقط، والاحتفاظ بالصفوف الموجودة
            tbody.find('tr:not(.existing-service)').remove();

            var isOutpatient = ($('#patient_type').val() || $('input[name="patient_type"]:checked').val()) === 'outpatient';
            var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
            var isPatientInsured = $('#is_insured').is(':checked');

            // تحديث الإجماليات للخدمات الموجودة
            tbody.find('.existing-service').each(function() {
                var serviceId = $(this).data('service-id');
                var existingService = window.existingServices.find(s => s.id === serviceId);
                if (existingService) {
                    var totalForService = (existingService.isDaily && !isOutpatient) ?
                        existingService.fee * daysCount :
                        existingService.fee;
                    
                    // حساب مبالغ الضمان
                    var patientAmount = 0;
                    var insuranceAmount = 0;
                    var patientPercentage = existingService.insurancePercentage || 10;
                    
                    if (existingService.isInsured && isPatientInsured) {
                        patientAmount = totalForService * (patientPercentage / 100);
                        insuranceAmount = totalForService * ((100 - patientPercentage) / 100);
                    } else {
                        patientAmount = totalForService;
                        insuranceAmount = 0;
                    }
                    
                    $('#existing-service-total-' + serviceId).text(formatNumber(totalForService) + ' د.ع');
                    $('#existing-service-patient-' + serviceId).text(formatNumber(patientAmount) + ' د.ع');
                    $('#existing-service-insurance-' + serviceId).text(formatNumber(insuranceAmount) + ' د.ع');
                }
            });

            // إضافة الخدمات الجديدة
            window.servicesData.forEach((service, index) => {
                var totalFee = (service.isDaily && !isOutpatient) ? service.fee * daysCount : service.fee;
                
                // حساب مبالغ الضمان بناءً على النسبة
                var patientAmount = 0;
                var insuranceAmount = 0;
                var patientPercentage = service.insurancePercentage || 10;
                
                if (service.isInsured && isPatientInsured) {
                    patientAmount = totalFee * (patientPercentage / 100);
                    insuranceAmount = totalFee * ((100 - patientPercentage) / 100);
                } else {
                    patientAmount = totalFee;
                    insuranceAmount = 0;
                }
                
                // عمود الضمان
                var insuranceColumn = '';
                var percentageColumn = '';
                
                if (isPatientInsured) {
                    insuranceColumn = `
                        <td style="text-align: center;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input insurance-check" type="checkbox" 
                                    ${service.isInsured ? 'checked' : ''} 
                                    data-index="${index}">
                            </div>
                        </td>`;
                    
                    percentageColumn = `
                        <td style="text-align: center;">
                            <select class="form-control form-control-sm insurance-percentage" data-index="${index}" 
                                ${!service.isInsured ? 'disabled style="background-color: #e9ecef;"' : ''}>
                                <option value="5" ${patientPercentage == 5 ? 'selected' : ''}>5%</option>
                                <option value="10" ${patientPercentage == 10 ? 'selected' : ''}>10%</option>
                                <option value="15" ${patientPercentage == 15 ? 'selected' : ''}>15%</option>
                                <option value="20" ${patientPercentage == 20 ? 'selected' : ''}>20%</option>
                                <option value="25" ${patientPercentage == 25 ? 'selected' : ''}>25%</option>
                                <option value="50" ${patientPercentage == 50 ? 'selected' : ''}>50%</option>
                                <option value="75" ${patientPercentage == 75 ? 'selected' : ''}>75%</option>
                                <option value="100" ${patientPercentage == 100 ? 'selected' : ''}>100%</option>
                            </select>
                        </td>`;
                }
                
                tbody.append(`
                    <tr class="new-service">
                        <td>${service.name}</td>
                        <td>${service.doctor_name || ''}</td>
                        <td>${service.operation_degree || ''}</td>
                        <td>${formatNumber(service.fee)} د.ع</td>
                        <td style="text-align: center;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input multiply-days" type="checkbox" 
                                    ${service.isDaily ? 'checked' : ''} 
                                    data-index="${index}">
                            </div>
                        </td>
                        ${insuranceColumn}
                        ${percentageColumn}
                        <td>${formatNumber(totalFee)} د.ع</td>
                        <td>${formatNumber(patientAmount)} د.ع</td>
                        <td>${formatNumber(insuranceAmount)} د.ع</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm delete-service" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
            
            // إظهار/إخفاء عمود الضمان في الرأس
            toggleInsuranceColumn();
        }



        // معالجة تغيير الخدمة
        $('#fckr').change(function() {
            var service_id = $(this).val();
            if (service_id) {
                $.ajax({
                    url: "{{ route('getServicePrice') }}",
                    type: "GET",
                    data: {
                        service_id: service_id
                    },                success: function(data) {
                        if (typeof data.price !== 'undefined') {
                            $('#service_fee').val(data.price);
                        } else {
                            $('#service_fee').val('0');
                            console.warn('لم يتم العثور على سعر للخدمة:', data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('خطأ في جلب السعر:', textStatus, errorThrown);
                        $('#service_fee').val('0');
                        alert('حدث خطأ أثناء جلب سعر الخدمة');
                    }
                });
            } else {
                $('#service_fee').val('');
            }
        });

        // إضافة خدمة جديدة
        $('#add_service').click(function() {
            var serviceId = $('#fckr').val();
            var serviceName = $('#fckr option:selected').text();
            var serviceFee = parseFloat($('#service_fee').val());
            var specialization = $('#section_id option:selected').text();
            var subSpecialization = $('#fckn option:selected').text();
            var doctorId = $('#doctor_id').val();
            var doctorName = $('#doctor_id option:selected').text();
            var operationDegree = $('#operation_degree').val();

            if (!serviceId || !serviceName || isNaN(serviceFee) || serviceFee <= 0) {
                alert('الرجاء اختيار الخدمة وتحديد سعر صحيح أكبر من صفر');
                return;
            }

            var required = isDoctorRequiredService(serviceName);

            if (required && !doctorId) {
                alert('الرجاء اختيار الطبيب لهذا التخصص');
                return;
            }

            if (!required) {
                doctorId = null;
                doctorName = '';
            }

            // تحديد القيمة الافتراضية لاحتساب الأيام
            var isDaily = serviceName.includes('رقود') || 
                         serviceName.includes('الأدوية والمستلزمات');

            // إضافة الخدمة إلى المصفوفة
            window.servicesData.push({
                id: serviceId,
                name: serviceName,
                fee: serviceFee,
                isDaily: isDaily,
                isInsured: false,
                insurancePercentage: 10, // النسبة الافتراضية 10%
                specialization: specialization,
                subSpecialization: subSpecialization,
                doctor_id: doctorId,
                doctor_name: doctorName,
                operation_degree: operationDegree
            });

            // تحديث الجدول والمجاميع
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();

            // إعادة تعيين الحقول
            $('#section_id').val('').trigger('change');
            $('#fckn').val('').trigger('change');
            $('#fckr').val('').trigger('change');
            $('#doctor_id').val('').trigger('change');
            $('#operation_degree').val('').trigger('change');
            $('#service_fee').val('');
        });

        // تحديث المجاميع عند تغيير عدد الأيام
        $('#days_count').on('change', function() {
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة تغيير خانة احتساب الأيام
        $(document).on('change', '.multiply-days', function() {
            var index = $(this).data('index');
            window.servicesData[index].isDaily = $(this).prop('checked');
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة تغيير خانة الضمان
        $(document).on('change', '.insurance-check', function() {
            var index = $(this).data('index');
            var isChecked = $(this).prop('checked');
            window.servicesData[index].isInsured = isChecked;
            
            // تفعيل/تعطيل حقل النسبة وتحديث المظهر
            var row = $(this).closest('tr');
            var percentageSelect = row.find('.insurance-percentage');
            
            if (isChecked) {
                percentageSelect.prop('disabled', false);
                percentageSelect.css('background-color', '');
            } else {
                percentageSelect.prop('disabled', true);
                percentageSelect.css('background-color', '#e9ecef');
            }
            
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة تغيير نسبة الضمان
        $(document).on('change', '.insurance-percentage', function() {
            var index = $(this).data('index');
            var percentage = parseFloat($(this).val());
            window.servicesData[index].insurancePercentage = percentage;
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة تغيير حقل "هل مشمول بالضمان؟"
        $('#is_insured').on('change', function() {
            var isInsured = $(this).is(':checked');
            
            // إذا تم إلغاء الضمان، نلغي الضمان من جميع الخدمات
            if (!isInsured) {
                window.servicesData.forEach(function(service) {
                    service.isInsured = false;
                });
            }
            
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة تغيير نسبة الاستثناء
        $('#exception_percentage').on('input change', function() {
            var pct = parseFloat($(this).val()) || 0;
            if (pct >= 100) {
                $('#exception_badge').show();
                $('#exception_attachment_container').addClass('border-success');
            } else {
                $('#exception_badge').hide();
                $('#exception_attachment_container').removeClass('border-success');
            }
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة حدث النقر على زر الحذف
        $(document).on('click', '.delete-service', function() {
            var index = $(this).data('index');
            window.servicesData.splice(index, 1);
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // معالجة إرسال النموذج
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // التحقق من وجود خدمات (موجودة أو جديدة)
            if (window.existingServices.length === 0 && window.servicesData.length === 0) {
                alert('الرجاء إضافة خدمة واحدة على الأقل');
                return;
            }
            
            var formElement = this;
            var serviceIndex = 0;
            
            // إضافة بيانات الخدمات الموجودة أولاً
            window.existingServices.forEach((service) => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][id]`,
                    value: service.service_id
                }).appendTo(formElement);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][fee]`,
                    value: service.fee
                }).appendTo(formElement);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][is_daily]`,
                    value: service.isDaily ? '1' : '0'
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][is_insured]`,
                    value: service.isInsured ? '1' : '0'
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][insurance_percentage]`,
                    value: service.insurancePercentage || 10
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][doctor_id]`,
                    value: service.doctor_id || ''
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][operation_degree]`,
                    value: service.operation_degree || ''
                }).appendTo(formElement);
                
                serviceIndex++;
            });
            
            // إضافة بيانات الخدمات الجديدة
            window.servicesData.forEach((service) => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][id]`,
                    value: service.id
                }).appendTo(formElement);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][fee]`,
                    value: service.fee
                }).appendTo(formElement);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][is_daily]`,
                    value: service.isDaily ? '1' : '0'
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][is_insured]`,
                    value: service.isInsured ? '1' : '0'
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][insurance_percentage]`,
                    value: service.insurancePercentage || 10
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][doctor_id]`,
                    value: service.doctor_id || ''
                }).appendTo(formElement);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${serviceIndex}][operation_degree]`,
                    value: service.operation_degree || ''
                }).appendTo(formElement);
                
                serviceIndex++;
            });
            
            // مسح البيانات المحفوظة قبل الإرسال
            clearLocalStorage();
            
            this.submit();
        });

        // تخزين الخدمات الموجودة في مصفوفة منفصلة
        window.existingServices = [];
        @if(isset($services) && count($services) > 0)
            @foreach($services as $service)
                window.existingServices.push({
                    id: {{ $service->id }},
                    service_id: {{ $service->service_id }},
                    name: {!! json_encode($service->service_name) !!},
                    doctor_id: {{ $service->doctor_id ?? 'null' }},
                    doctor_name: {!! json_encode($service->doctor_name ?? '') !!},
                    operation_degree: {!! json_encode($service->operation_degree ?? '') !!},
                    fee: {{ $service->service_fee }},
                    isDaily: {{ $service->is_daily ? 'true' : 'false' }},
                    isInsured: {{ $service->is_insured ? 'true' : 'false' }},
                    insurancePercentage: {{ $service->insurance_percentage ?? 10 }}
                });
            @endforeach
        @endif
        
        // استرجاع البيانات المحفوظة (إن وجدت)
        loadFromLocalStorage();
        
        // تحديث الجداول والحسابات
        updateServicesTable();
        updateTotal();
        updateTotalDeposit();
        updateRemaining();
        toggleInsuranceColumn();
    });
</script>
@endsection
