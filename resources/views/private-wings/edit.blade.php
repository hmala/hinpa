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
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-purple text-center">
                    <h2 class="mb-0"><i class="fas fa-edit"></i> تعديل حساب جناح خاص</h2>
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

                    <form action="{{ route('private-wings.update', $privateWing->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <!-- تنبيه استرجاع البيانات -->
                        <div id="restore-notification" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <strong>تم استرجاع البيانات المحفوظة</strong> من الجلسة السابقة
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <input type="hidden" name="fiscal_year" value="{{ $privateWing->fiscal_year }}">
                        <!-- معلومات المريض -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-blue text-center">
                                <h4 class="mb-0"><i class="fas fa-user-injured"></i> معلومات المريض</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 bg-white p-2 rounded border mx-1">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold d-block text-primary"><i class="fas fa-hospital-user me-1"></i> مسار / نوع المريض</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="patient_type" id="patient_type_inpatient" value="inpatient" {{ ($privateWing->patient_type ?? "inpatient") == "inpatient" ? "checked" : "" }} autocomplete="off">
                                            <label class="btn btn-outline-primary fw-bold" for="patient_type_inpatient">
                                                <i class="fas fa-bed me-1"></i> مريض راقد (إقامة كاملة)
                                            </label>

                                            <input type="radio" class="btn-check" name="patient_type" id="patient_type_outpatient" value="outpatient" {{ ($privateWing->patient_type ?? "inpatient") == "outpatient" ? "checked" : "" }} autocomplete="off">
                                            <label class="btn btn-outline-success fw-bold" for="patient_type_outpatient">
                                                <i class="fas fa-user-clock me-1"></i> مريض غير راقد (خدمة خارجية / عيادة)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="hospital" class="form-label fw-bold">المستشفى</label>
                                        <input type="text" class="form-control shadow-sm" id="hospital" name="hospital" value="{{ $privateWing->hospital }}" required readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="health_department" class="form-label fw-bold">دائرة صحة</label>
                                        <input type="text" class="form-control shadow-sm" id="health_department" name="health_department" value="{{ $privateWing->health_department }}" required readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_name" class="form-label fw-bold">اسم المريض</label>
                                        <input type="text" class="form-control shadow-sm" id="patient_name" name="patient_name" value="{{ $privateWing->patient_name }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="annual_number" class="form-label fw-bold">الترقيم السنوي ({{ $privateWing->fiscal_year }}) - على مستوى المؤسسة</label>
                                        <input type="text" class="form-control shadow-sm" id="annual_number" name="annual_number" value="{{ $privateWing->annual_number }}" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3" id="statistical_number_container">
                                        <label for="statistical_number" class="form-label fw-bold">الرقم الإحصائي</label>
                                        <input type="text" class="form-control shadow-sm" id="statistical_number" name="statistical_number" value="{{ $privateWing->statistical_number }}" required>
                                    </div>
                                </div>

                                <div class="row" id="time_and_days_row">
                                    <div class="col-md-4 mb-3">
                                        <label for="entry_date" class="form-label fw-bold">تاريخ الدخول</label>
                                        <input type="date" class="form-control shadow-sm" id="entry_date" name="entry_date" value="{{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}" required>
                                        <input type="time" class="form-control shadow-sm mt-1" id="entry_time" name="entry_time" value="{{ $privateWing->entry_time ? \Carbon\Carbon::parse($privateWing->entry_time)->format('H:i') : '00:00' }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="exit_date" class="form-label fw-bold">تاريخ الخروج</label>
                                        <input type="date" class="form-control shadow-sm" id="exit_date" name="exit_date" value="{{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}">
                                        <input type="time" class="form-control shadow-sm mt-1" id="exit_time" name="exit_time" value="{{ $privateWing->exit_time ? \Carbon\Carbon::parse($privateWing->exit_time)->format('H:i') : '00:00' }}">
                                    </div>
                                       <div class="col-md-4 mb-3">
                                        <label for="days_count" class="form-label fw-bold">عدد الأيام</label>
                                        <input type="number" class="form-control shadow-sm" id="days_count" name="days_count" value="{{ $privateWing->days_count }}" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="list_date" class="form-label fw-bold">تاريخ تنظيم القائمة</label>
                                        <input type="date" class="form-control shadow-sm" id="list_date" name="created_at" value="{{ $privateWing->created_at ? $privateWing->created_at->format('Y-m-d') : '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="is_insured" class="form-label fw-bold">مشمول بالضمان</label>
                                        <div style="text-align: center; margin-top: 0.5rem;">
                                            <input class="form-check-input" type="checkbox" id="is_insured" name="is_insured" value="1" {{ $privateWing->is_insured == 1 ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                        </div>
                                    </div>
                                     <div class="col-md-4 mb-3">
                                        <label for="patient_nationality" class="form-label fw-bold">جنسية المريض</label>
                                        <select class="form-control shadow-sm" id="patient_nationality" name="patient_nationality">
                                            <option value="">اختر الجنسية</option>
                                            <option value="عراقي" {{ $privateWing->patient_nationality == 'عراقي' ? 'selected' : '' }}>عراقي</option>
                                            <option value="عربي" {{ $privateWing->patient_nationality == 'عربي' ? 'selected' : '' }}>عربي</option>
                                            <option value="أجنبي" {{ $privateWing->patient_nationality == 'أجنبي' ? 'selected' : '' }}>أجنبي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                     

                     
                        <!-- الرسوم والخدمات -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-green text-center">
                                <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> الرسوم والخدمات</h4>
                            </div>
                            <div class="card-body">

                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">إضافة الخدمات والرسوم</h5>
                                            </div>
                                            <div class="card-body">

                                                <div class="row mb-3">
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="operation_degree" class="form-label fw-bold">درجة العملية</label>
                                                                <select name="operation_degree" id="operation_degree" class="form-control select2">
                                                                    <option value="">اختر درجة العملية...</option>
                                                                    <option value="الخاصة">الخاصة</option>
                                                                    <option value="فوق الكبرى">فوق الكبرى</option>
                                                                    <option value="كبرى">كبرى</option>
                                                                    <option value="متوسطة">متوسطة</option>
                                                                    <option value="صغرى">صغرى</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <select name="section_id" id="section_id" class="form-control select2">
                                                                    <option value="">اختر التخصص...</option>
                                                                    @foreach($typeSpecializations as $typeSpecialization)
                                                                        <option value="{{ $typeSpecialization->id }}">{{ $typeSpecialization->tsname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <select id="fckn" name="fckn" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد التخصص الفرعي--</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <select id="fckr" name="fckr" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد الخدمة--</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text">د.ع</span>
                                                            <input type="number" step="0.01" class="form-control shadow-sm" id="service_fee">
                                                            <button type="button" class="btn btn-primary" id="add_service">
                                                                <i class="fas fa-plus-circle"></i> إضافة
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <select id="doctor_id" name="doctor_id" class="form-control select2">
                                                            <option value="">اختر الطبيب...</option>
                                                            @foreach($doctors as $doctor)
                                                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered" id="services_table">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>الخدمة</th>
                                                                <th>الطبيب</th>
                                                                <th>درجة العملية</th>
                                                                <th>التكلفة اليومية</th>
                                                                <th>احتساب الأيام</th>
                                                                <th class="insurance-header">مشمول بالضمان</th>
                                                                <th class="insurance-header">نسبة المريض %</th>
                                                                <th>الإجمالي</th>
                                                                <th>مبلغ المريض</th>
                                                                <th>مبلغ الضمان</th>
                                                                <th>العمليات</th>
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
                                                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="لا يمكن حذف الخدمات الموجودة"><i class="fas fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="5">المجموع</th>
                                                                <th class="insurance-header"></th>
                                                                <th class="insurance-header"></th>
                                                                <th id="total_amount">0 د.ع</th>
                                                                <th id="total_patient_amount">0 د.ع</th>
                                                                <th id="total_insurance_amount">0 د.ع</th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات التأمينات -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-orange text-center">
                                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> وصولات التأمينات</h4>
                            </div>
                            <div class="card-body">

                                <div id="receipts-container">
                                    @foreach($privateWing->receipts as $index => $receipt)
                                    <div class="row receipt-row mb-3 existing-receipt" data-receipt-id="{{ $receipt->id }}" data-amount="{{ $receipt->amount }}" data-interest="{{ $receipt->interest_amount }}">
                                        <div class="col-md-2">
                                            <label>مبلغ التأمين</label>
                                            <input type="number" step="0.01" class="form-control" value="{{ $receipt->amount }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-2">
                                            <label>رقم الوصل</label>
                                            <input type="text" class="form-control" value="{{ $receipt->receipt_number }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-2">
                                            <label>تاريخ الوصل</label>
                                            <input type="date" class="form-control" value="{{ $receipt->receipt_date->format('Y-m-d') }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-2">
                                            <label>نوع الدفع</label>
                                            <input type="text" class="form-control" value="{{ $receipt->payment_type }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-1">
                                            <label>النسبة (%)</label>
                                            <input type="number" step="0.01" class="form-control" value="{{ $receipt->interest_rate }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-2">
                                            <label>الفائدة (د.ع)</label>
                                            <input type="number" step="0.01" class="form-control" value="{{ $receipt->interest_amount }}" readonly disabled style="background-color: #e9ecef;">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-secondary remove-receipt" disabled title="لا يمكن حذف الوصولات الموجودة"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-success" id="add-receipt">
                                    <i class="fas fa-plus"></i> إضافة وصل جديد
                                </button>
                            </div>
                        </div>

                        <!-- الإجماليات -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-info text-center">
                                <h4 class="mb-0"><i class="fas fa-calculator"></i> الإجماليات</h4>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الخدمات المقدمة</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_services_provided" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي مبلغ المريض</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_services" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي مبلغ الضمان</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_insurance_summary" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الإيداعات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_deposits" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الفائدة</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_interest" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">المبلغ المتبقي</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="remaining_amount" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول الاستثناء -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="exception_from" class="form-label fw-bold">استثناء من طرف من</label>
                                        <input type="text" class="form-control shadow-sm" id="exception_from" name="exception_from" value="{{ $privateWing->exception_from ?? '' }}" placeholder="أدخل طرف الاستثناء">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="exception_percentage" class="form-label fw-bold">نسبة الاستثناء (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control shadow-sm" id="exception_percentage" name="exception_percentage" value="{{ $privateWing->exception_percentage ?? '' }}" min="0" max="100" step="0.01" placeholder="أدخل النسبة">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">تحديث</button>
                            <a href="{{ route('private-wings.index') }}" class="btn btn-secondary">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
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
    // متغير لتتبع الوصولات المحذوفة - لم يعد مطلوباً
    // let deletedReceipts = [];

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
            <div class="row receipt-row new-receipt mb-3">
                <div class="col-md-2">
                    <label>مبلغ التأمين</label>
                    <input type="number" step="0.01" class="form-control" name="deposits[${index}][amount]" required onchange="updateInterest(this, ${index})">
                </div>
                <div class="col-md-2">
                    <label>رقم الوصل</label>
                    <input type="text" class="form-control" name="deposits[${index}][number]" required>
                </div>
                <div class="col-md-2">
                    <label>تاريخ الوصل</label>
                    <input type="date" class="form-control" name="deposits[${index}][date]" required>
                </div>
                <div class="col-md-2">
                    <label>نوع الدفع</label>
                    <select class="form-control" name="deposits[${index}][payment_type]" required onchange="updateInterest(this, ${index})">
                        <option value="كاش" selected>كاش</option>
                        <option value="إلكتروني">إلكتروني</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label>النسبة (%)</label>
                    <input type="number" step="0.01" class="form-control" name="deposits[${index}][interest_rate]" readonly>
                </div>
                <div class="col-md-2">
                    <label>الفائدة (د.ع)</label>
                    <input type="number" step="0.01" class="form-control" name="deposits[${index}][interest_amount]" readonly>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-receipt"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `;
        $('#receipts-container').append(newRow);
        // احتساب الفائدة للوصل الجديد
        const newRowElement = $('#receipts-container .receipt-row').last();
        const amountInput = newRowElement.find('input[name*="amount"]');
        const paymentTypeSelect = newRowElement.find('select[name*="payment_type"]');
        // حساب الفائدة الأولية
        const amount = parseFloat(amountInput.val()) || 0;
        const paymentType = paymentTypeSelect.val();
        const interestRate = paymentType === 'كاش' ? 5.00 : 0.00;
        const interestAmount = (amount * interestRate) / 100;
        newRowElement.find('input[name*="interest_rate"]').val(interestRate.toFixed(2));
        newRowElement.find('input[name*="interest_amount"]').val(interestAmount.toFixed(2));
        // إضافة event listeners للحساب التلقائي
        amountInput.on('input', function() {
            updateInterest(this, index);
        });
        paymentTypeSelect.on('change', function() {
            updateInterest(this, index);
        });
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
        let totalInterest = 0;  // مجموع الفائدة فقط
        
        // حساب الوصولات القديمة من data attributes
        $('.existing-receipt').each(function() {
            const amount = parseFloat($(this).data('amount') || 0);
            const interestAmount = parseFloat($(this).data('interest') || 0);
            totalDeposits += amount;
            totalInterest += interestAmount;
        });
        
        // حساب الوصولات الجديدة فقط (التي ليس لها class existing-receipt)
        $('.receipt-row:not(.existing-receipt)').each(function() {
            const amount = parseFloat($(this).find('input[name*="[amount]"]').val() || 0);
            const interestAmount = parseFloat($(this).find('input[name*="[interest_amount]"]').val() || 0);
            totalDeposits += amount;
            totalInterest += interestAmount;
        });
        
        // إجمالي الإيداعات = مبلغ الوصل + الفائدة
        totalDepositsAmount = totalDeposits + totalInterest;
        $('#total_deposits').val(formatNumber(totalDepositsAmount) + ' د.ع');
        $('#total_interest').val(formatNumber(totalInterest) + ' د.ع');
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
        var isOutpatient = $('input[name="patient_type"]:checked').val() === 'outpatient';
        var daysCount = isOutpatient ? 0 : (parseInt($('#days_count').val()) || 1);
        var totalPatientAmount = 0;
        
        // حساب من الخدمات الموجودة
        window.existingServices.forEach(function(service) {
            var serviceTotal = (service.isDaily && !isOutpatient) ? 
                parseFloat(service.fee) * daysCount : 
                parseFloat(service.fee);
            
            var patientPercentage = service.insurancePercentage || 10;
            
            if (service.isInsured && isPatientInsured) {
                totalPatientAmount += serviceTotal * (patientPercentage / 100);
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
            } else {
                totalPatientAmount += serviceTotal;
            }
        });
        
        // تطبيق نسبة الاستثناء على مبلغ المريض
        var exceptionPercentage = parseFloat($('#exception_percentage').val()) || 0;
        if (exceptionPercentage > 0) {
            totalPatientAmount = totalPatientAmount * (1 - exceptionPercentage / 100);
        }
        
        // حساب مبلغ الوصولات بدون الفائدة
        let totalDepositsOnly = 0;
        $('.existing-receipt').each(function() {
            totalDepositsOnly += parseFloat($(this).data('amount') || 0);
        });
        $('.receipt-row:not(.existing-receipt)').each(function() {
            totalDepositsOnly += parseFloat($(this).find('input[name*="[amount]"]').val() || 0);
        });
        
        // المعادلة: المبلغ المتبقي = مبلغ الوصولات (بدون فائدة) - مبلغ المريض
        const remaining = totalDepositsOnly - totalPatientAmount;
        $('#remaining_amount').val(formatNumber(remaining) + ' د.ع');
    }

    // تحديث الإجمالي عند تغيير أي مبلغ
    $(document).on('input', 'input[name$="[amount]"]', function() {
        updateTotalDeposit();
    });

    // دالة لحساب الفائدة
    function updateInterest(element, index) {
        const row = $(element).closest('.receipt-row');
        const amount = parseFloat(row.find('input[name*="amount"]').val()) || 0;
        const paymentType = row.find('select[name*="payment_type"]').val();
        const interestRate = paymentType === 'كاش' ? 5.00 : 0.00;
        const interestAmount = (amount * interestRate) / 100;

        row.find('input[name*="interest_rate"]').val(interestRate.toFixed(2));
        row.find('input[name*="interest_amount"]').val(interestAmount.toFixed(2));

        updateTotalDeposit();
    }

    // سيتم استدعاء updateTotalDeposit عند تحميل الصفحة من الدالة الثانية
});</script>
<script>
    $(document).ready(function() {
        var initialPatientType = '{{ $privateWing->patient_type ?? "inpatient" }}';
        function toggleTimeAndDaysRow() {
            var patientType = $('input[name="patient_type"]:checked').val() || initialPatientType;
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

        // تحميل قائمة التخصص الفرعي دائماً عند التحميل (حتى في صفحة التعديل)
        function loadSubSpecializations(filterSection) {
            var data = { _token: '{{ csrf_token() }}' };
            if (filterSection) data.section_id = filterSection;

            loadSelectDataWithRetry(
                "{{ route('getSpecializations') }}",
                '#fckn',
                {
                    method: 'POST',
                    data: data,
                    defaultText: '--حدد التخصص الفرعي--',
                    valueField: 'sercode',
                    textField: 'sername',
                    retries: 3,
                    timeout: 10000,
                    onSuccess: function() {
                        $('#fckn').prop('disabled', false);
                    },
                    onError: function() {
                        $('#fckn').empty();
                        $('#fckn').append('<option value="" disabled selected>--حدد التخصص الفرعي--</option>');
                        $('#fckn').prop('disabled', false);
                    }
                }
            );
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

            if (required) {
                $('#doctor_id').prop('disabled', false);
                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب (مطلوب)');
            } else {
                $('#doctor_id').prop('disabled', false);
                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب (اختياري)');
            }
        }

        $('#fckr').change(toggleDoctorField);
        $('#fckn').change(toggleDoctorField);

        // سعيد استدعاء التخصصات الفرعية فوراً
        loadSubSpecializations();

        // تغيير القسم فقط يعيد تهيئة الطبيب، بدون مساس التخصص الفرعي
        $('#section_id').change(function() {
            $('#doctor_id').prop('disabled', true).val('').trigger('change');
            $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
        });

        // تغيير التخصص الفرعي يجلب الخدمات المرتبطة به
        $('#fckn').change(function() {
            var serviceId = $(this).val();
            $('#service_fee').val('');

            if (serviceId) {
                loadSelectDataWithRetry(
                    "{{ route('getServiceSpecializations') }}",
                    '#fckr',
                    {
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            service_id: serviceId
                        },
                        defaultText: '--حدد الخدمة--',
                        valueField: 'id',
                        textField: 'namesv',
                        retries: 3,
                        timeout: 10000,
                        onSuccess: function() {
                            toggleDoctorField();
                        },
                        onError: function() {
                            $('#fckr').empty();
                            $('#fckr').append('<option value="" disabled selected>--حدد الخدمة--</option>');
                            $('#doctor_id').prop('disabled', true).val('').trigger('change');
                        }
                    }
                );
            } else {
                $('#fckr').empty();
                $('#fckr').append('<option value="" disabled selected>--حدد الخدمة--</option>');
                $('#doctor_id').prop('disabled', true).val('').trigger('change');
                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
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
        window.formatNumber = function(number) {
            return number.toLocaleString('ar-IQ', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // دالة لتحديث المجموع الكلي
        window.updateTotal = function() {
            var isOutpatient = $('input[name="patient_type"]:checked').val() === 'outpatient';
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

            var isOutpatient = $('input[name="patient_type"]:checked').val() === 'outpatient';
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
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
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
            updateTotal();
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
