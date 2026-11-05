@extends('layouts.master')
@section('title')
ألاسرة المهيئة 
@stop
@section('css')
<!-- Custom CSS -->
<link href="{{ URL::asset('assets/css/custom/main.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/css/custom/private-wings.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/css/custom/print.css') }}" rel="stylesheet" />

<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<!--Internal   Notify -->
<link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
<!-- Internal Select2 css -->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<style>
    /* تصغير حجم الجداول والحقول للأناقة وحفظ المساحة */
    #services_table th, #services_table td,
    #deposits_table th, #deposits_table td {
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
    
    /* Loading Overlay Styles */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.active {
        display: flex;
    }
    
    .loading-content {
        text-align: center;
        background: white;
        padding: 30px 50px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }
    
    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
    }
    
    .loading-subtext {
        font-size: 14px;
        color: #666;
    }
</style>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">جاري الحفظ<span class="loading-dots"></span></div>
        <div class="loading-subtext">يرجى الانتظار، لا تغلق الصفحة</div>
    </div>
</div>

<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-2">
                <div class="card-header bg-gradient-purple text-white py-1 px-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fs-6"><i class="fas fa-plus-circle me-1"></i> إضافة حساب جناح خاص جديد</h5>
                    <span class="badge bg-white text-purple fw-bold">{{ date('Y/m/d') }}</span>
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

                    <form id="private_wing_form" action="{{ route('private-wings.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Hidden fields for moh_id and fck_id -->
                        <input type="hidden" name="moh_id" value="{{ Auth::user()->mohcode }}">
                        <input type="hidden" name="fck_id" value="{{ Auth::user()->fckid }}">
                        
                        <!-- تنبيه استرجاع البيانات -->
                        <div id="restore-notification" class="alert alert-success alert-dismissible fade p-1 mb-1 fs-7" role="alert" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <strong>تم استرجاع البيانات المحفوظة</strong> من الجلسة السابقة
                            <button type="button" class="btn-close py-1" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        
                        <!-- شريط الترويسة العلوي للبيانات التلقائية والأزرار في صف واحد مدمج -->
                        <div class="card mb-2 bg-light border shadow-sm">
                            <div class="card-body py-1.5 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-hospital text-primary me-1"></i> المستشفى: 
                                        <span class="badge bg-white text-primary border px-2 py-1">{{ Auth::user()->fck ? Auth::user()->fck->Fckname : 'غير محدد' }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-landmark text-secondary me-1"></i> دائرة صحة / المقر: 
                                        <span class="badge bg-white text-secondary border px-2 py-1">{{ Auth::user()->mohcode ? \App\Models\mohs::find(Auth::user()->mohcode)->mohname : 'مقر الوزارة' }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-calendar-check text-info me-1"></i> تاريخ التنظيم: 
                                        <span class="badge bg-white text-dark border px-2 py-1">{{ date('Y-m-d') }}</span>
                                    </span>
                                    <span class="fw-bold text-dark fs-7">
                                        <i class="fas fa-hashtag text-warning me-1"></i> الترقيم السنوي ({{ $currentYear }}): 
                                        <span class="badge bg-warning text-dark border px-2 py-1">{{ $nextAnnualNumber }}</span>
                                    </span>
                                    <input type="hidden" name="patient_type" id="patient_type" value="{{ request('patient_type', 'inpatient') }}">
                                    @if(request('patient_type') == 'outpatient')
                                        <span class="badge bg-success text-white py-1 px-2 fs-7 shadow-sm"><i class="fas fa-user-clock me-1"></i> غير راقد</span>
                                    @else
                                        <span class="badge bg-primary text-white py-1 px-2 fs-7 shadow-sm"><i class="fas fa-bed me-1"></i> مريض راقد</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" form="private_wing_form" class="btn btn-primary btn-sm px-3 py-1 fw-bold shadow-sm" id="submit_btn" onclick="if(this.hasAttribute('data-clicked')){return false;} this.setAttribute('data-clicked', 'true');">
                                        <i class="fas fa-save me-1"></i> حفظ السجل والبيانات
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm px-2 py-1 shadow-sm fw-bold" id="clear_form_btn" title="مسح وتفريغ النموذج">
                                        <i class="fas fa-eraser me-1"></i> مسح
                                    </button>
                                    <a href="{{ route('private-wings.index') }}" class="btn btn-secondary btn-sm px-3 py-1 shadow-sm">
                                        <i class="fas fa-arrow-right me-1"></i> رجوع
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- الحقول المخفية للبيانات التلقائية -->
                        <input type="hidden" id="hospital" name="hospital" value="{{ Auth::user()->fck ? Auth::user()->fck->Fckname : '' }}">
                        <input type="hidden" id="health_department" name="health_department" value="{{ Auth::user()->mohcode ? \App\Models\mohs::find(Auth::user()->mohcode)->mohname : '' }}">
                        <input type="hidden" id="list_date" name="created_at" value="{{ date('Y-m-d') }}">
                        <input type="hidden" id="annual_number" value="{{ $nextAnnualNumber }}">

                        <!-- 1. بطاقة إدخال بيانات المريض (نظام خلايا مصغرة وأنيقة) -->
                        <div class="card mb-2 shadow-sm">
                            <div class="card-header bg-gradient-blue text-white card-header-styled d-flex justify-content-between align-items-center">
                                <span class="card-main-title"><i class="fas fa-user-injured text-warning"></i> أولاً: بيانات ومعلومات المريض والإقامة</span>
                                <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-user-check me-1"></i> معلومات المريض</span>
                            </div>
                            <div class="card-body p-1.5">
                                <!-- الصف الأول: البيانات الشخصية والتعريفية (خلايا مصغرة) -->
                                <div class="row g-1 mb-1">
                                    <div class="col-md-4">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-user text-primary"></i> اسم المريض <span class="text-danger">*</span></div>
                                            <input type="text" class="form-control" id="patient_name" name="patient_name" placeholder="الاسم الرباعي للمريض" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="statistical_number_container">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-id-card text-secondary"></i> الرقم الإحصائي <span class="text-danger">*</span></div>
                                            <input type="text" class="form-control" id="statistical_number" name="statistical_number" placeholder="الرقم الإحصائي" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-phone text-success"></i> رقم الهاتف</div>
                                            <input type="tel" class="form-control" id="patient_phone" name="patient_phone" pattern="[0-9\-\s\+]+" placeholder="07xxxxxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-globe text-info"></i> جنسية المريض</div>
                                            <select class="form-select form-control" id="patient_nationality" name="patient_nationality">
                                                <option value="عراقي" selected>عراقي</option>
                                                <option value="عربي">عربي</option>
                                                <option value="أجنبي">أجنبي</option>
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
                                                <input type="date" class="form-control" id="entry_date" name="entry_date" required>
                                                <input type="time" class="form-control" id="entry_time" name="entry_time" value="00:00" style="max-width: 65px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-sign-out-alt text-danger"></i> تاريخ ووقت الخروج</div>
                                            <div class="input-group input-group-sm">
                                                <input type="date" class="form-control" id="exit_date" name="exit_date">
                                                <input type="time" class="form-control" id="exit_time" name="exit_time" value="00:00" style="max-width: 65px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-clock text-warning"></i> الأيام <span class="text-danger">*</span></div>
                                            <input type="number" class="form-control text-center fw-bold" id="days_count" name="days_count" value="1" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell d-flex flex-column align-items-center justify-content-center px-1">
                                            <div class="patient-cell-title mb-1" style="font-size: 0.64rem;"><i class="fas fa-shield-alt text-secondary"></i> ضمان</div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="is_insured" style="cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-building text-secondary"></i> جهة الاستثناء</div>
                                            <input type="text" class="form-control" id="exception_from" name="exception_from" placeholder="جهة الاستثناء">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="patient-cell">
                                            <div class="patient-cell-title"><i class="fas fa-percent text-info"></i> الاستثناء %</div>
                                            <input type="number" class="form-control text-center fw-bold px-1" id="exception_percentage" name="exception_percentage" min="0" max="100" step="0.01" placeholder="0 %">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="patient-cell" id="exception_attachment_container">
                                            <div class="patient-cell-title d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-paperclip text-purple"></i> مرفق كتاب الاستثناء</span>
                                                <span id="exception_badge" class="badge bg-success text-white py-0 px-1" style="display: none; font-size: 0.65rem;">معفى 100%</span>
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
                                        <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-plus-circle me-1"></i> إضافة الخدمات</span>
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

                                        <!-- جدول الخدمات المضافة مع سكرول داخلي لطيف -->
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
                                                </tbody>
                                                <tfoot class="bg-light fw-bold sticky-bottom">
                                                    <tr>
                                                        <th colspan="7" class="text-end py-1">المجموع:</th>
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
                                        <span class="badge bg-white text-primary card-header-badge"><i class="fas fa-money-check-alt me-1"></i> تسجيل المقبوضات</span>
                                    </div>
                                    <div class="card-body p-2 d-flex flex-column">
                                        <!-- قسم إدخال وصل تأمين جديد بنظام الخلايا -->
                                        <div class="service-inline-bar mb-2 p-1.5 bg-light border border-success-subtle rounded">
                                            <div class="row g-1 mb-1">
                                                <div class="col-3">
                                                    <div class="form-cell border-success-subtle">
                                                        <div class="form-cell-title text-success"><i class="fas fa-coins text-success"></i> المبلغ (د.ع) <span class="text-danger">*</span></div>
                                                        <input type="number" class="form-control fw-bold text-success" id="new_deposit_amount" placeholder="0" min="0" step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-hashtag text-secondary"></i> رقم الوصل <span class="text-danger">*</span></div>
                                                        <input type="text" class="form-control" id="new_deposit_number" placeholder="رقم الوصل">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-calendar-alt text-info"></i> التاريخ <span class="text-danger">*</span></div>
                                                        <input type="date" class="form-control text-center" id="new_deposit_date" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-cell">
                                                        <div class="form-cell-title"><i class="fas fa-credit-card text-warning"></i> طريقة الدفع</div>
                                                        <select class="form-select form-control" id="new_deposit_payment_type">
                                                            <option value="كاش" selected>كاش</option>
                                                            <option value="إلكتروني">إلكتروني</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-1">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-success btn-sm w-100 py-1 fw-bold shadow-sm" id="add_deposit_btn" style="font-size: 0.78rem;">
                                                        <i class="fas fa-plus-circle me-1"></i> إضافة هذا الوصل إلى قائمة الوصولات
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- جدول قائمة الوصولات المضافة بمساحة واسعة ومريحة -->
                                        <div class="table-responsive flex-grow-1" style="max-height: 220px; min-height: 180px; overflow-y: auto;">
                                            <table class="table table-bordered table-hover table-sm mb-0" id="deposits_table">
                                                <thead class="bg-light sticky-top">
                                                    <tr>
                                                        <th>المبلغ (د.ع)</th>
                                                        <th>رقم الوصل</th>
                                                        <th>التاريخ</th>
                                                        <th>طريقة الدفع</th>
                                                        <th style="width: 45px;" class="text-center">حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="text-muted text-center empty-deposits-row">
                                                        <td colspan="5" class="py-3"><i class="fas fa-info-circle me-1 text-secondary"></i> لا توجد وصولات مسجلة بعد - أدخل بيانات الوصل واضغط إضافة</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="bg-light fw-bold sticky-bottom">
                                                    <tr>
                                                        <th colspan="3" class="text-end py-1">إجمالي الوصولات المدخلة:</th>
                                                        <th colspan="2" id="deposits_table_total" class="text-success py-1">0 د.ع</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
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
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm fw-bold border-warning text-warning p-0 text-center" id="interest_amount" name="interest_amount" placeholder="0" value="0" style="background: #fff; height: 24px;">
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
    $(document).ready(function() {
                        // تفعيل الحفظ التلقائي للنموذج بأمان إذا كان الملف موجوداً
                        if (typeof FormAutoSave !== 'undefined') {
                            FormAutoSave.init('form', {
                                excludeFields: ['_token'],
                                showRestoreMessage: true
                            });
                        }

                        // تهيئة Select2 لجميع القوائم مع تفعيل البحث داخل القائمة
                        $('.select2').select2({
                            width: '100%',
                            placeholder: 'ابحث أو اختر...',
                            dropdownAutoWidth: true,
                            minimumResultsForSearch: 0
                        });

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

                        // استدعاء فوري لقائمة خدمات التخصص "عام"
                        loadSubSpecializations(8);

                        // 2. تغيير الخدمات بناءً على التخصص الفرعي المحدد
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
                    });
                </script>
<script>
    $(document).ready(function() {
        // تهيئة المتغيرات العامة أولاً
        window.servicesData = [];
        window.deposits = [];
        var totalAmount = 0;
        
        var isOutpatient = $('#patient_type').val() === 'outpatient';
        if (isOutpatient) {
            $('#time_and_days_row').hide();
            $('#entry_date').removeAttr('required').val('{{ date("Y-m-d") }}');
            $('#days_count').removeAttr('required').val('0');
            $('#statistical_number_container').hide();
            $('#statistical_number').removeAttr('required');
        }
        
        // التحقق من معامل URL لمسح البيانات (عند فتح الصفحة من جديد بعد عملية ناجحة)
        const urlParams = new URLSearchParams(window.location.search);
        const shouldClearData = urlParams.get('clear') === '1';
        
        if (shouldClearData) {
            // مسح البيانات المحفوظة والنموذج
            clearLocalStorage();
            clearFormFields();
            // إزالة المعامل من URL بدون إعادة تحميل الصفحة
            window.history.replaceState({}, document.title, window.location.pathname);
        } else {
            // استرجاع البيانات المحفوظة (سيتم استبدال المصفوفات الفارغة بالبيانات المحفوظة)
            loadFromLocalStorage();
        }
        
        function isDoctorRequiredService(serviceName) {
            serviceName = (serviceName || '').trim();
            if (!serviceName) {
                return false;
            }

            // الخدمات الظاهرة في القائمة التي يجب توفير طبيب لها
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

            // أي خدمة تشترك في عبارة الطبيب أو المساعد تكون مطلوبة
            if (/اجور\s+(.*\b)?(الطبيب|المساعد|طبيبة|الطبيبة|الجراح)/i.test(serviceName)) {
                return true;
            }

            return false;
        }

        function toggleDoctorField() {
            // حقل القائم بالعمل متاح واختياري دائمًا لجميع الخدمات
            $('#doctor_id').prop('disabled', false);
            $('#doctor_id_label').html('<i class="fas fa-user-md me-1"></i> اسم القائم بالعمل');
            $('#service_fee_label').html('<i class="fas fa-tag me-1"></i> مبلغ الخدمة (د.ع)');
        }

        $('#fckr').change(toggleDoctorField);
        $('#fckn').change(toggleDoctorField);

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

        // دالة لحفظ البيانات في localStorage
        function saveToLocalStorage() {
            try {
                // حفظ معلومات المريض
                localStorage.setItem('privateWing_patientName', $('#patient_name').val() || '');
                localStorage.setItem('privateWing_statisticalNumber', $('#statistical_number').val() || '');
                localStorage.setItem('privateWing_entryDate', $('#entry_date').val() || '');
                localStorage.setItem('privateWing_entryTime', $('#entry_time').val() || '00:00');
                localStorage.setItem('privateWing_exitDate', $('#exit_date').val() || '');
                localStorage.setItem('privateWing_exitTime', $('#exit_time').val() || '00:00');
                localStorage.setItem('privateWing_patientNationality', $('#patient_nationality').val() || '');
                localStorage.setItem('privateWing_isInsured', $('#is_insured').is(':checked') ? '1' : '0');
                localStorage.setItem('privateWing_daysCount', $('#days_count').val() || '1');
                localStorage.setItem('privateWing_interestAmount', $('#interest_amount').val() || '0');
                
                // حفظ الخدمات والوصولات
                localStorage.setItem('privateWing_services', JSON.stringify(window.servicesData));
                localStorage.setItem('privateWing_deposits', JSON.stringify(window.deposits));
                
                console.log('✓ تم حفظ جميع البيانات في localStorage');
            } catch (e) {
                console.error('خطأ في حفظ البيانات:', e);
            }
        }

        // دالة لاسترجاع البيانات من localStorage
        function loadFromLocalStorage() {
            try {
                // استرجاع معلومات المريض
                var savedPatientName = localStorage.getItem('privateWing_patientName');
                var savedStatisticalNumber = localStorage.getItem('privateWing_statisticalNumber');
                var savedEntryDate = localStorage.getItem('privateWing_entryDate');
                var savedEntryTime = localStorage.getItem('privateWing_entryTime');
                var savedExitDate = localStorage.getItem('privateWing_exitDate');
                var savedExitTime = localStorage.getItem('privateWing_exitTime');
                var savedPatientNationality = localStorage.getItem('privateWing_patientNationality');
                var savedIsInsured = localStorage.getItem('privateWing_isInsured');
                var savedDaysCount = localStorage.getItem('privateWing_daysCount');
                var savedInterestAmount = localStorage.getItem('privateWing_interestAmount');
                
                if (savedPatientName) $('#patient_name').val(savedPatientName);
                if (savedStatisticalNumber) $('#statistical_number').val(savedStatisticalNumber);
                if (savedEntryDate) $('#entry_date').val(savedEntryDate);
                if (savedEntryTime) $('#entry_time').val(savedEntryTime);
                if (savedExitDate) $('#exit_date').val(savedExitDate);
                if (savedExitTime) $('#exit_time').val(savedExitTime);
                if (savedPatientNationality) $('#patient_nationality').val(savedPatientNationality);
                if (savedIsInsured) $('#is_insured').prop('checked', savedIsInsured === '1');
                if (savedDaysCount) $('#days_count').val(savedDaysCount);
                if (savedInterestAmount !== null && savedInterestAmount !== undefined) $('#interest_amount').val(savedInterestAmount);
                
                var savedServices = localStorage.getItem('privateWing_services');
                var savedDeposits = localStorage.getItem('privateWing_deposits');
                
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
                
                // استرجاع الوصولات
                if (savedDeposits) {
                    try {
                        window.deposits = JSON.parse(savedDeposits);
                        updateDepositsTable();
                    } catch (e) {
                        console.error('خطأ في تحليل بيانات الوصولات:', e);
                        window.deposits = [];
                    }
                }
                
                if (savedServices || savedDeposits || savedPatientName) {
                    console.log('✓ تم استرجاع البيانات المحفوظة');
                    
                    // جلب أرقام الترقيم الجديدة لتحديث الحقول المخفية
                    fetchNewNumbers();
                    
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
        function clearLocalStorage() {
            localStorage.removeItem('privateWing_patientName');
            localStorage.removeItem('privateWing_statisticalNumber');
            localStorage.removeItem('privateWing_entryDate');
            localStorage.removeItem('privateWing_entryTime');
            localStorage.removeItem('privateWing_exitDate');
            localStorage.removeItem('privateWing_exitTime');
            localStorage.removeItem('privateWing_patientNationality');
            localStorage.removeItem('privateWing_services');
            localStorage.removeItem('privateWing_deposits');
            localStorage.removeItem('privateWing_isInsured');
            localStorage.removeItem('privateWing_daysCount');
            localStorage.removeItem('privateWing_interestAmount');
            console.log('✓ تم مسح البيانات المحفوظة');
        }
        
        // دالة لجلب الترقيمات الجديدة من السيرفر (للعرض فقط)
        function fetchNewNumbers() {
            $.ajax({
                url: '{{ route("private-wings.get-next-numbers") }}',
                type: 'GET',
                success: function(response) {
                    // تحديث العرض فقط - الأرقام ستُولد في السيرفر عند الحفظ
                    $('#annual_number').val(response.nextAnnualNumber);
                    console.log('✓ تم تحديث عرض الترقيمات:', response);
                },
                error: function(xhr) {
                    console.error('خطأ في جلب الترقيمات:', xhr);
                }
            });
        }
        
        // دالة لمسح جميع حقول النموذج
        function clearFormFields() {
            // مسح معلومات المريض
            $('#patient_name').val('');
            $('#statistical_number').val('');
            $('#entry_date').val(isOutpatient ? '{{ date("Y-m-d") }}' : '');
            $('#entry_time').val('00:00');
            $('#exit_date').val('');
            $('#exit_time').val('00:00');
            $('#patient_nationality').val('');
            $('#is_insured').prop('checked', false);
            $('#days_count').val(isOutpatient ? '0' : '1');
            $('#interest_amount').val('0');
            
            // مسح الخدمات والوصولات
            window.servicesData = [];
            window.deposits = [];
            
            // تحديث الجداول
            updateServicesTable();
            updateDepositsTable();
            updateTotal();
            toggleInsuranceColumn();
            
            // جلب الترقيمات الجديدة
            fetchNewNumbers();
            
            console.log('✓ تم مسح جميع حقول النموذج');
        }

        // دالة لحساب المجموع الكلي
        function updateTotal() {
            var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
            var isPatientInsured = $('#is_insured').is(':checked');
            totalAmount = 0;
            var totalPatientAmount = 0;
            var totalInsuranceAmount = 0;
            
            window.servicesData.forEach(function(service) {
                // إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var serviceTotal = (service.isDaily && !isOutpatient) ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                
                totalAmount += serviceTotal;
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
                var patientPercentage = service.insurancePercentage || 100;
                
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
            var grandTotalWithInterest = totalPatientAmount + interestAmount;
            
            // تحديث المجاميع في الجدول
            $('#total_amount').text(formatNumber(totalServicesProvided) + ' د.ع');
            $('#total_patient_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_insurance_amount').text(formatNumber(totalInsuranceAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_services_provided').val(formatNumber(totalServicesProvided) + ' د.ع');
            $('#total_insurance_summary').val(formatNumber(totalInsuranceAmount) + ' د.ع');
            $('#grand_total_with_interest').val(formatNumber(grandTotalWithInterest) + ' د.ع');
            
            // تحديث المبلغ المتبقي
            updateRemaining();
        }

        // دالة لإظهار/إخفاء عمود الضمان
        function toggleInsuranceColumn() {
            var isPatientInsured = $('#is_insured').is(':checked');
            var insuranceHeader = $('#services_table thead th:contains("مشمول بالضمان")');
            var percentageHeader = $('#services_table thead th:contains("نسبة المريض")');
            
            if (isPatientInsured) {
                insuranceHeader.show();
                percentageHeader.show();
            } else {
                insuranceHeader.hide();
                percentageHeader.hide();
            }
        }

        // دالة لتحديث جدول الخدمات
        function updateServicesTable() {
            var tbody = $('#services_table tbody');
            tbody.empty();
            var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
            var isPatientInsured = $('#is_insured').is(':checked');
            
            window.servicesData.forEach((service, index) => {
                // حساب الإجمالي - إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var totalFee = (service.isDaily && !isOutpatient) ? service.fee * daysCount : service.fee;
                
                // حساب مبالغ الضمان بناءً على النسبة
                var patientAmount = 0;
                var insuranceAmount = 0;
                var patientPercentage = service.insurancePercentage || 100;
                
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
                        <td>
                            <div class="form-check">
                                <input class="form-check-input insurance-check" type="checkbox" 
                                    ${service.isInsured ? 'checked' : ''} 
                                    data-index="${index}">
                            </div>
                        </td>`;
                    
                    percentageColumn = `
                        <td>
                            <select class="form-control form-control-sm insurance-percentage" data-index="${index}" 
                                ${!service.isInsured ? 'disabled style="background-color: #d7d8daff;"' : ''}>
                                <option value="5" ${patientPercentage == 5 ? 'selected' : ''}>5%</option>
                                <option value="10" ${patientPercentage == 10 ? 'selected' : ''}>10%</option>
                                <option value="15" ${patientPercentage == 15 ? 'selected' : ''}>15%</option>
                                <option value="20" ${patientPercentage == 20 ? 'selected' : ''}>20%</option>
                                <option value="25" ${patientPercentage == 25 ? 'selected' : ''}>25%</option>
                                <option value="50" ${patientPercentage == 50 ? 'selected' : ''}>50%</option>
                                <option value="75" ${patientPercentage == 75 ? 'selected' : ''}>75%</option>
                            </select>
                        </td>`;
                }
                
                tbody.append(`
                    <tr>
                        <td>${service.name}</td>
                        <td>${service.doctor_name || ''}</td>
                        <td>${service.operation_degree || ''}</td>
                        <td>${formatNumber(service.fee)} د.ع</td>
                        <td>
                            <div class="form-check">
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
                        <td>
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

        // دالة لحساب الفرق بين التواريخ بنظام فندقي
        function calculateDays() {
            var entryDate = $('#entry_date').val();
            var exitDate = $('#exit_date').val();
            var exitTime = $('#exit_time').val() || '00:00';

            if (entryDate && exitDate) {
                var entry = new Date(entryDate);
                var exit = new Date(exitDate);

                var diffTime = exit - entry;
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (diffDays < 0) {
                    alert('تاريخ الخروج لا يمكن أن يكون قبل تاريخ الدخول');
                    $('#exit_date').val('');
                    $('#days_count').val(1);
                } else {
                    var days = diffDays;
                    if (exitTime >= '12:00') days += 1; // خروج بعد الظهر -> يوم إضافي
                    if (days < 1) days = 1;
                    $('#days_count').val(days);
                }
            }
        }

        // تفعيل حساب الأيام عند تغيير أي من التاريخين
        $('#entry_date, #exit_date, #entry_time, #exit_time').change(function() {
            calculateDays();
        });

        // جعل حقل عدد الأيام للقراءة فقط
        $('#days_count').prop('readonly', true);        // معالجة تغيير الخدمة المحددة
        $('#fckr').change(function() {
            var selectedOption = $(this).find('option:selected');
            var serviceId = selectedOption.val();
            
            if (serviceId) {
                // جلب سعر الخدمة من الخادم
                $.ajax({
                    url: "{{ route('getServicePrice') }}",
                    type: "GET",
                    data: {
                        service_id: serviceId
                    },
                    success: function(data) {
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

        // معالجة زر إضافة الخدمة
        $('#add_service').click(function() {
            var serviceId = $('#fckr').val();
            var serviceName = $('#fckr option:selected').text();
            var serviceFee = parseFloat($('#service_fee').val());
            var specialization = $('#section_id option:selected').text();
            var subSpecialization = $('#fckn option:selected').text();
            var doctorId = $('#doctor_id').val();
            var doctorName = $('#doctor_id option:selected').text();
            var operationDegree = $('#operation_degree').val();

            console.log('doctorId:', doctorId);
            console.log('doctorName:', doctorName);

            if (!serviceId || !serviceName || isNaN(serviceFee) || serviceFee <= 0) {
                alert('الرجاء اختيار الخدمة وتحديد سعر صحيح أكبر من صفر');
                return;
            }

            // حقل الطبيب اختياري دائمًا - لا توجد قيود
            if (!doctorId) {
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

            // إعادة تعيين الحقول (بدون المساس بالتخصص العام المثبت)
            $('#fckn').val('').trigger('change.select2');
            $('#fckr').empty().append('<option value="" disabled selected>--حدد الخدمة--</option>').trigger('change.select2');
            $('#doctor_id').val('').trigger('change.select2');
            $('#operation_degree').val('').trigger('change.select2');
            $('#service_fee').val('');
        });

        // معالجة حدث النقر على زر الحذف
        $(document).on('click', '.delete-service', function() {
            var index = $(this).data('index');
            window.servicesData.splice(index, 1);
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });

        // تحديث الجدول عند تغيير عدد الأيام
        $('#days_count').on('change', function() {
            updateServicesTable();
            updateTotal();
            saveToLocalStorage();
        });        // معالجة تغيير خانة احتساب الأيام
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
            updateDepositsTable();
            saveToLocalStorage();
        });

        // معالجة تغيير مبلغ الفائدة
        $('#interest_amount').on('input change', function() {
            updateTotal();
            saveToLocalStorage();
        });

        // حفظ تلقائي عند تغيير معلومات المريض
        $('#patient_name, #statistical_number, #entry_date, #entry_time, #exit_date, #exit_time, #patient_nationality, #exception_from').on('input change', function() {
            saveToLocalStorage();
        });

        // حفظ البيانات قبل مغادرة الصفحة أو إعادة تحميلها
        $(window).on('beforeunload', function() {
            saveToLocalStorage();
        });

        function updateDepositsTable() {
            var tbody = $('#deposits_table tbody');
            tbody.empty();

            var exceptionPercentage = parseFloat($('#exception_percentage').val()) || 0;

            if (!window.deposits || window.deposits.length === 0) {
                if (exceptionPercentage >= 100) {
                    tbody.append(`
                        <tr class="text-success text-center empty-deposits-row" style="background-color: #ecfdf5;">
                            <td colspan="5" class="py-2.5 fw-bold"><i class="fas fa-check-circle me-1 text-success"></i> المريض معفى بالكامل بنسبة 100% بموجب كتاب استثناء رسمي (لا يشترط إدخال وصولات قبض)</td>
                        </tr>
                    `);
                } else {
                    tbody.append(`
                        <tr class="text-muted text-center empty-deposits-row">
                            <td colspan="5" class="py-3"><i class="fas fa-info-circle me-1 text-secondary"></i> لا توجد وصولات مسجلة بعد - أدخل بيانات الوصل واضغط إضافة</td>
                        </tr>
                    `);
                }
            } else {
                window.deposits.forEach((deposit, idx) => {
                    tbody.append(`
                        <tr class="align-middle">
                            <td class="fw-bold text-success">${formatNumber(deposit.amount)} د.ع</td>
                            <td><span class="badge bg-light text-dark border px-2">${deposit.number}</span></td>
                            <td>${deposit.date}</td>
                            <td><span class="badge ${deposit.payment_type === 'إلكتروني' ? 'bg-info text-white' : 'bg-secondary text-white'}">${deposit.payment_type}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm py-0 px-2 delete-deposit" data-index="${idx}" title="حذف الوصل">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }

            // تحديث المبلغ المتبقي والإجمالي
            updateRemaining();
        }

        function updateRemaining() {
            // حساب إجمالي وصولات التأمين
            var totalDeposits = (window.deposits || []).reduce((sum, deposit) => 
                sum + parseFloat(deposit.amount || 0), 0);
            
            // حساب مبلغ المريض (بدون الضمان)
            var isPatientInsured = $('#is_insured').is(':checked');
            var totalPatientAmount = 0;
            
            (window.servicesData || []).forEach(function(service) {
                var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
                var serviceTotal = (service.isDaily && !isOutpatient) ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                
                var patientPercentage = service.insurancePercentage || 100;
                
                if (service.isInsured && isPatientInsured) {
                    totalPatientAmount += serviceTotal * (patientPercentage / 100);
                } else {
                    totalPatientAmount += serviceTotal;
                }
            });

            // تطبيق نسبة الاستثناء على مبلغ المريض
            var exceptionPercentage = parseFloat($('#exception_percentage').val()) || 0;
            if (exceptionPercentage > 0) {
                totalPatientAmount = totalPatientAmount * (1 - exceptionPercentage / 100);
            }

            var interestAmount = parseFloat($('#interest_amount').val()) || 0;
            var grandTotalRequired = totalPatientAmount + interestAmount;

            // المعادلة: المبلغ المتبقي = إجمالي الوصولات - المبلغ المطلوب شامل الفائدة
            var remainingAmount = totalDeposits - grandTotalRequired;
            
            $('#total_deposits').val(formatNumber(totalDeposits) + ' د.ع');
            $('#deposits_table_total').text(formatNumber(totalDeposits) + ' د.ع');
            
            var remainingText = '';
            if (exceptionPercentage >= 100 && totalDeposits === 0) {
                remainingText = '0 د.ع (معفى 100% بالاستثناء)';
                $('#remaining_amount').removeClass('text-danger').addClass('text-success');
            } else {
                remainingText = formatNumber(Math.abs(remainingAmount)) + ' د.ع ';
                if (remainingAmount < 0) {
                    remainingText += '(متبقي عليه)';
                    $('#remaining_amount').removeClass('text-success').addClass('text-danger');
                } else if (remainingAmount > 0) {
                    remainingText += '(متبقي له / استرداد)';
                    $('#remaining_amount').removeClass('text-danger').addClass('text-success');
                } else {
                    remainingText += '(خالص)';
                    $('#remaining_amount').removeClass('text-danger text-success');
                }
            }
            $('#remaining_amount').val(remainingText);
        }

        // إضافة وصل جديد
        $('#add_deposit_btn').click(function() {
            var amount = parseFloat($('#new_deposit_amount').val());
            var number = $('#new_deposit_number').val();
            var date = $('#new_deposit_date').val();
            var paymentType = $('#new_deposit_payment_type').val();

            if (!amount || isNaN(amount) || amount <= 0) {
                alert('الرجاء إدخال مبلغ صحيح');
                return;
            }
            if (!number) {
                alert('الرجاء إدخال رقم الوصل');
                return;
            }
            if (!date) {
                alert('الرجاء إدخال تاريخ الوصل');
                return;
            }

            window.deposits.push({
                amount: amount,
                number: number,
                date: date,
                payment_type: paymentType,
                interest_rate: 0,
                interest_amount: 0
            });

            // تحديث الجدول والمجاميع
            updateDepositsTable();
            saveToLocalStorage();

            // تنظيف الحقول
            $('#new_deposit_amount').val('');
            $('#new_deposit_number').val('');
            $('#new_deposit_date').val('');
        });

        // حذف وصل
        $(document).on('click', '.delete-deposit', function() {
            var index = $(this).data('index');
            window.deposits.splice(index, 1);
            updateDepositsTable();
            saveToLocalStorage();
        });

        // معالجة إرسال النموذج
        var isSubmitting = false;
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // منع الإرسال المتكرر - تعيين فوري
            if (isSubmitting) {
                console.log('محاولة إرسال متكررة تم منعها');
                return false;
            }
            
            // ===== التحقق من البيانات أولاً قبل أي شيء =====
            
            // التحقق من استرجاع البيانات من localStorage إذا لزم الأمر
            if (!window.servicesData || window.servicesData.length === 0) {
                var savedServices = localStorage.getItem('privateWing_services');
                if (savedServices) {
                    try {
                        window.servicesData = JSON.parse(savedServices);
                        console.log('Restored services from localStorage:', window.servicesData);
                    } catch (e) {
                        console.error('Error parsing saved services:', e);
                    }
                }
            }
            
            if (!window.deposits || window.deposits.length === 0) {
                var savedDeposits = localStorage.getItem('privateWing_deposits');
                if (savedDeposits) {
                    try {
                        window.deposits = JSON.parse(savedDeposits);
                        console.log('Restored deposits from localStorage:', window.deposits);
                    } catch (e) {
                        console.error('Error parsing saved deposits:', e);
                    }
                }
            }
            
            // إظهار البيانات قبل الإرسال للتأكد من صحتها
            console.log('Services data before submit:', window.servicesData);
            console.log('Deposits data before submit:', window.deposits);
            
            // التحقق من وجود خدمات
            if (!window.servicesData || window.servicesData.length === 0) {
                alert('الرجاء إضافة خدمة واحدة على الأقل');
                return false; // إيقاف تام
            }

            // التحقق من وجود وصولات - يتم استثناء الحالات المعفية 100%
            var exceptionPct = parseFloat($('#exception_percentage').val()) || 0;
            if (exceptionPct < 100) {
                if (!window.deposits || window.deposits.length === 0) {
                    alert('الرجاء إضافة وصل قبض / تأمين واحد على الأقل، أو تحديد نسبة الاستثناء 100% في حال الإعفاء الكامل.');
                    return false; // إيقاف تام
                }
            }
            
            // ===== بعد التحقق الناجح، نبدأ بتجهيز النموذج للإرسال =====
            
            // تفعيل الحماية ضد الإرسال المتكرر
            isSubmitting = true;
            
            // تعطيل زر الحفظ فورًا
            $('#submit_btn').prop('disabled', true);
            
            // إضافة حقل is_insured بقيمة boolean
            $('<input>').attr({
                type: 'hidden',
                name: 'is_insured',
                value: $('#is_insured').is(':checked') ? '1' : '0'
            }).appendTo(this);
            
            console.log('is_insured value:', $('#is_insured').is(':checked') ? '1' : '0');
            
            // إضافة بيانات الخدمات إلى النموذج
            window.servicesData.forEach((service, index) => {
                console.log('Adding service:', service);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][id]`,
                    value: service.id
                }).appendTo(this);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][fee]`,
                    value: service.fee
                }).appendTo(this);
                
                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][is_daily]`,
                    value: service.isDaily ? '1' : '0'
                }).appendTo(this);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][is_insured]`,
                    value: service.isInsured ? '1' : '0'
                }).appendTo(this);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][insurance_percentage]`,
                    value: service.insurancePercentage || 100
                }).appendTo(this);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][doctor_id]`,
                    value: service.doctor_id || ''
                }).appendTo(this);

                $('<input>').attr({
                    type: 'hidden',
                    name: `services[${index}][operation_degree]`,
                    value: service.operation_degree || ''
                }).appendTo(this);
            });

            // إضافة بيانات وصولات التأمين
            window.deposits.forEach((deposit, index) => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][amount]`,
                    value: deposit.amount
                }).appendTo(this);
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][number]`,
                    value: deposit.number
                }).appendTo(this);
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][date]`,
                    value: deposit.date
                }).appendTo(this);
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][payment_type]`,
                    value: deposit.payment_type
                }).appendTo(this);
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][interest_rate]`,
                    value: deposit.interest_rate
                }).appendTo(this);
                $('<input>').attr({
                    type: 'hidden',
                    name: `deposits[${index}][interest_amount]`,
                    value: deposit.interest_amount
                }).appendTo(this);
            });
            
            // طباعة جميع البيانات المرسلة
            console.log('Form data:', $(this).serialize());
            
            // مسح البيانات المحفوظة قبل الإرسال
            clearLocalStorage();
            
            // إظهار شاشة التحميل
            $('#loadingOverlay').addClass('active');
            
            // تعطيل باقي الأزرار
            $('#clear_form_btn').prop('disabled', true);
            
            // إرسال النموذج
            this.submit();
        });

        // ربط حدث تغيير التخصص الفرعي
        $('#fckn').change(function() {
            toggleDoctorField();
        });

        // تهيئة حالة حقل الطبيب عند تحميل الصفحة
        toggleDoctorField();
        
        // زر مسح النموذج
        $('#clear_form_btn').click(function() {
            if (confirm('هل أنت متأكد من مسح جميع البيانات المدخلة؟')) {
                // مسح localStorage والنموذج
                clearLocalStorage();
                clearFormFields();
            }
        });
    });
</script>
@endsection