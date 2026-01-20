@extends('layouts.master')
@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<!-- Internal Owl Carousel css-->
<link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
<!-- Internal Morris.js Charts Plugin -->
<link href="{{ URL::asset('assets/plugins/morris.js/morris.css') }}" rel="stylesheet">
<!-- Custom Statistics CSS -->
<link href="{{ URL::asset('css/statistics.css') }}" rel="stylesheet">
<!-- Chartjs -->
<style>
    .statistics-card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        background: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    

    
    .comparison-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #495057;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }
    
    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }
    
    /* Print Styles */
    @media print {
        body {
            font-size: 12px;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            width: 100% !important;
            max-width: none !important;
        }
        
        .filter-section,
        .breadcrumb-header,
        .print-btn,
        .card-header,
        .btn,
        .no-print {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 1rem;
            width: 100% !important;
        }
        
        .table {
            font-size: 9px;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        
        .table th,
        .table td {
            border: 1px solid #333 !important;
            padding: 3px !important;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        
        .table thead th {
            background-color: #f5f5f5 !important;
            color: #333 !important;
            font-weight: bold;
            font-size: 8px !important;
        }
        
        .table-info td {
            background-color: #d1ecf1 !important;
            font-weight: bold;
        }
        
        .table-secondary td {
            background-color: #e2e3e5 !important;
            font-weight: bold;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 8px 0;
            page-break-after: avoid;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        
        .comparison-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        
        .print-header h1 {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .print-header h2 {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .print-header p {
            font-size: 10px;
            margin: 0;
        }
        
        /* Force page break before new major sections */
        .page-break {
            page-break-before: always;
        }
        
        /* Ensure tables don't break awkwardly */
        .table-responsive {
            overflow: visible !important;
        }
        

        
        @page {
            size: A4 landscape;
            margin: 0.5cm 1cm;
        }
        
        /* Additional landscape optimizations */
        body {
            width: 100% !important;
            max-width: none !important;
        }
        
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }
        
        .table {
            width: 100% !important;
            table-layout: fixed;
            word-wrap: break-word;
        }
        
        .table th,
        .table td {
            font-size: 9px !important;
            padding: 2px !important;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .section-title {
            font-size: 12px !important;
            margin: 10px 0 5px 0 !important;
        }
        
        /* Optimize table column widths for landscape */
        .table th:first-child,
        .table td:first-child {
            width: 25% !important;
            min-width: 120px;
        }
        
        .table th:not(:first-child),
        .table td:not(:first-child) {
            width: auto !important;
            min-width: 40px;
        }
    }
</style>
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإحصائيات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ المقارنات والإحصائيات</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<!-- Print Header (Hidden on screen, shown on print) -->
<div class="print-header d-none">
    <h1>وزارة الصحة - إحصائيات المؤسسات الصحية</h1>
    <h2 id="print-title">تقرير الإحصائيات الشهرية</h2>
    <p id="print-date">تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
</div>

<!-- row -->
<div class="row">
    <div class="col-xl-12">
        <!-- Filter Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">خيارات الفلترة والمقارنة</h3>
            </div>
            <div class="card-body filter-section">
                <form id="statisticsForm">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="moh_id">اختيار الدائرة:</label>
                            <select class="form-control select2" id="moh_id" name="moh_id">
                                <option value="all">جميع الدوائر</option>
                                @foreach($mohs as $moh)
                                    <option value="{{ $moh->id }}">{{ $moh->mohname }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="fctype_id">نوع المؤسسة:</label>
                            <select class="form-control select2" id="fctype_id" name="fctype_id">
                                <option value="all">جميع الأنواع</option>
                                @foreach($fctypes as $fctype)
                                    <option value="{{ $fctype->id }}">{{ $fctype->Fname }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="fck_id">اختيار المؤسسة:</label>
                            <select class="form-control select2" id="fck_id" name="fck_id">
                                <option value="all">جميع المؤسسات</option>
                                @foreach($fcks as $fck)
                                    <option value="{{ $fck->id }}" data-moh="{{ $fck->moh_id }}" data-fctype="{{ $fck->fctypesid }}">{{ $fck->Fckname }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="year">السنة:</label>
                            <select class="form-control" id="year" name="year">
                                @for($year = 2025; $year <= date('Y') + 1; $year++)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary mr-2" onclick="loadStatistics()">
                                <i class="fas fa-chart-line"></i> عرض المقارنة
                            </button>
                            <button type="button" class="btn btn-success mr-2" onclick="exportStatistics()">
                                <i class="fas fa-download"></i> تصدير
                            </button>
                            <button type="button" class="btn btn-warning mr-2 print-btn" onclick="printStatistics()" style="display: none;">
                                <i class="fas fa-print"></i> طباعة
                            </button>
                            <button type="button" class="btn btn-info" onclick="resetForm()">
                                <i class="fas fa-refresh"></i> إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Loading Section -->
        <div id="loadingSection" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">جاري التحميل...</span>
            </div>
            <p class="mt-2">جاري تحميل الإحصائيات...</p>
        </div>
        
        <!-- Statistics Results Section -->
        <div id="statisticsResults" style="display: none;">

            
            <!-- Monthly Comparison Tables will be inserted here dynamically -->
            
        </div>
    </div>
</div>
<!-- /row -->
@endsection

@section('js')
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>

<script>
// Store all institutions data
var allInstitutions = @json($fcks);

$(document).ready(function() {
    console.log('Page loaded, institutions data:', allInstitutions);
    
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        allowClear: true
    });
    
    // Initialize institutions dropdown on page load
    function initializeInstitutions() {
        var fckSelect = $('#fck_id');
        
        console.log('Initializing institutions dropdown');
        
        // Add all institutions initially
        allInstitutions.forEach(function(fck) {
            var optionExists = fckSelect.find('option[value="' + fck.id + '"]').length;
            if (!optionExists) {
                fckSelect.append('<option value="' + fck.id + '" data-moh="' + fck.moh_id + '">' + fck.Fckname + '</option>');
            }
        });
        
        // Trigger Select2 update
        fckSelect.trigger('change.select2');
        console.log('Institutions initialized, total options:', fckSelect.find('option').length);
    }
    
    // Filter institutions based on selected department and type
    $('#moh_id, #fctype_id').on('change', function() {
        var mohId = $('#moh_id').val();
        var fctypeId = $('#fctype_id').val();
        var fckSelect = $('#fck_id');
        
        console.log('Filter changed - MOH:', mohId, 'FCType:', fctypeId);
        
        // Destroy existing Select2
        fckSelect.select2('destroy');
        
        // Clear and rebuild options
        fckSelect.empty();
        fckSelect.append('<option value="all">جميع المؤسسات</option>');
        
        // Filter institutions based on both department and type
        var filteredInstitutions = allInstitutions.filter(function(fck) {
            var mohMatch = (mohId === 'all' || mohId === '' || fck.moh_id == mohId);
            var typeMatch = (fctypeId === 'all' || fctypeId === '' || fck.fctypesid == fctypeId);
            return mohMatch && typeMatch;
        });
        
        console.log('Filtered institutions count:', filteredInstitutions.length);
        
        filteredInstitutions.forEach(function(fck) {
            fckSelect.append('<option value="' + fck.id + '" data-moh="' + fck.moh_id + '" data-fctype="' + fck.fctypesid + '">' + fck.Fckname + '</option>');
        });
        
        // Reinitialize Select2
        fckSelect.select2({
            width: '100%',
            allowClear: true
        });
        
        // Set to "all" and trigger change
        fckSelect.val('all').trigger('change');
        
        console.log('Institution dropdown updated, options count:', fckSelect.find('option').length);
        
        // تحديث الإحصائيات تلقائياً عند تغيير نوع المؤسسة أو الدائرة
        loadStatistics();
    });
    
    // When institution is selected, update department accordingly
    $('#fck_id').on('change', function() {
        var fckId = $(this).val();
        
        if (fckId !== 'all' && fckId) {
            var selectedInstitution = allInstitutions.find(function(fck) {
                return fck.id == fckId;
            });
            
            if (selectedInstitution) {
                $('#moh_id').val(selectedInstitution.moh_id).trigger('change.select2');
            }
        }
        
        // تحديث الإحصائيات عند تغيير المؤسسة
        loadStatistics();
    });
    
    // تحديث الإحصائيات عند تغيير السنة
    $('#year').on('change', function() {
        loadStatistics();
    });
    
    // Call initialization
    initializeInstitutions();
    
    // Load initial statistics
    loadStatistics();
});

function loadStatistics() {
    var formData = $('#statisticsForm').serialize();
    
    $('#loadingSection').show();
    $('#statisticsResults').hide();
    
    $.ajax({
        url: '{{ route("statistics.comparison") }}',
        method: 'GET',
        data: formData,
        success: function(response) {
            console.log('Response data:', response); // للتحقق من البيانات
            console.log('Response type:', response.type);
            if (response.monthly_data && response.monthly_data.length > 0) {
                console.log('First month data:', response.monthly_data[0]);
                if (response.monthly_data[0].moh_data) {
                    console.log('First MOH data sample:', response.monthly_data[0].moh_data[0]);
                }
            }
            displayStatistics(response);
            $('#loadingSection').hide();
            $('#statisticsResults').show();
        },
        error: function(xhr, status, error) {
            console.error('Error loading statistics:', error);
            alert('حدث خطأ في تحميل الإحصائيات');
            $('#loadingSection').hide();
        }
    });
}

function displayStatistics(data) {
    // عرض معلومات السنة
    $('#currentYear').text(data.year || new Date().getFullYear());

    
    // مسح المحتوى السابق
    $('.comparison-section').remove();
    
    if (data.type === 'monthly_general') {
        // عرض المقارنة الشهرية العامة
        displayGeneralMonthlyComparison(data.monthly_data);
        $('#comparisonDescription').text('مقارنة شاملة بين جميع الدوائر الصحية على مدار العام');
    } else if (data.type === 'monthly_moh') {
        // عرض المقارنة الشهرية للدائرة
        displayMonthlyComparison(data);
        $('#comparisonDescription').text(`مقارنة شهرية تفصيلية لدائرة ${data.moh_name}`);
    } else if (data.type === 'monthly_fctype') {
        // عرض المقارنة الشهرية لنوع المؤسسة
        displayMonthlyComparison(data);
        const description = data.moh_name ? 
            `مقارنة شهرية تفصيلية لنوع المؤسسة: ${data.fctype_name} - دائرة ${data.moh_name}` :
            `مقارنة شهرية تفصيلية لنوع المؤسسة: ${data.fctype_name}`;
        $('#comparisonDescription').text(description);
    } else if (data.type === 'monthly_fck') {
        // عرض المقارنة الشهرية للمؤسسة
        displayMonthlyComparison(data);
        $('#comparisonDescription').text(`مقارنة شهرية تفصيلية لمؤسسة ${data.fck_name}`);
    }
    
    // إظهار زر الطباعة
    $('.print-btn').show();
}

function displayGeneralMonthlyComparison(monthlyData) {
    // إخفاء الجداول الفردية وإظهار جدول المقارنة الشهرية
    $('.comparison-section').hide();
    
    // حساب الإجماليات لكل شهر
    var generalData = {
        type: 'monthly_general',
        monthly_data: []
    };
    
    monthlyData.forEach(function(monthData) {
        var monthTotal = {
            month_name: monthData.month_name,
            families: {
                total_units: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_units || 0), 0),
                total_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_beds || 0), 0),
                total_outpatients: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_outpatients || 0), 0),
                total_inpatients: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_inpatients || 0), 0),
                total_stay_days: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_stay_days || 0), 0),
                total_deaths: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_deaths || 0), 0)
            },
            surgeries: {
                total_special: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries_special || 0), 0),
                total_major_complex: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries_major_complex || 0), 0),
                total_major: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries_major || 0), 0),
                total_medium: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries_medium || 0), 0),
                total_minor: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries_minor || 0), 0),
                total_all: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_surgeries || 0), 0)
            },
            incidents: {
                total_live_births_natural: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_births_natural || 0), 0),
                total_live_births_cesarean: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_births_cesarean || 0), 0),
                total_births: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_births || 0), 0),
                total_maternal_deaths: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_maternal_deaths || 0), 0),
                total_deaths: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_deaths || 0), 0)
            },
            consultations: {
                total_halls: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_consultation_halls || 0), 0),
                total_hall_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_consultation_beds || 0), 0)
            },
            // دمج بيانات الصالات التفصيلية من جميع الدوائر
            consultation_details: (() => {
                var allConsultationTypes = {};
                monthData.moh_data.forEach(function(moh) {
                    if (moh.consultation_details && moh.consultation_details.length > 0) {
                        moh.consultation_details.forEach(function(detail) {
                            if (!allConsultationTypes[detail.type_name]) {
                                allConsultationTypes[detail.type_name] = {
                                    type_name: detail.type_name,
                                    halls: 0,
                                    beds: 0
                                };
                            }
                            allConsultationTypes[detail.type_name].halls += parseInt(detail.halls) || 0;
                            allConsultationTypes[detail.type_name].beds += parseInt(detail.beds) || 0;
                        });
                    }
                });
                return Object.values(allConsultationTypes);
            })(),
            // دمج بيانات الردهات التفصيلية من جميع الدوائر
            ward_details: (() => {
                var allWardTypes = {};
                monthData.moh_data.forEach(function(moh) {
                    if (moh.ward_details && moh.ward_details.length > 0) {
                        moh.ward_details.forEach(function(ward) {
                            if (!allWardTypes[ward.ward_name]) {
                                allWardTypes[ward.ward_name] = {
                                    ward_name: ward.ward_name,
                                    total_units: 0,
                                    total_beds: 0,
                                    total_outpatients: 0,
                                    total_inpatients: 0
                                };
                            }
                            allWardTypes[ward.ward_name].total_units += parseInt(ward.total_units) || 0;
                            allWardTypes[ward.ward_name].total_beds += parseInt(ward.total_beds) || 0;
                            allWardTypes[ward.ward_name].total_outpatients += parseInt(ward.total_outpatients) || 0;
                            allWardTypes[ward.ward_name].total_inpatients += parseInt(ward.total_inpatients) || 0;
                        });
                    }
                });
                return Object.values(allWardTypes);
            })(),
            cases: {
                total_emergency_cases: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_emergency_cases || 0), 0),
                total_light_cases: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_light_cases || 0), 0),
                total_ambulance_cases: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_ambulance_cases || 0), 0),
                total_outpatient_clinical: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_outpatient_clinical || 0), 0),
                total_outpatient_lab: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_outpatient_lab || 0), 0),
                total_inpatient_clinical: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_inpatient_clinical || 0), 0),
                total_inpatient_lab: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_inpatient_lab || 0), 0),
                total_available_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_available_beds || 0), 0),
                total_other_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_other_beds || 0), 0),
                total_emergency_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_emergency_beds || 0), 0),
                total_closed_ward_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_closed_ward_beds || 0), 0),
                total_moh_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_moh_beds || 0), 0),
                total_all_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_all_beds || 0), 0),
                total_facility_beds: monthData.moh_data.reduce((sum, moh) => sum + parseInt(moh.total_facility_beds || 0), 0)
            }
        };
        
        generalData.monthly_data.push(monthTotal);
    });
    
    // استخدام نفس دالة العرض المستخدمة للدوائر والمؤسسات
    generalData.moh_name = 'جميع الدوائر الصحية';
    displayMonthlyComparison(generalData);
}

function displayMonthlyComparison(data) {
    // إخفاء الجداول الفردية وإظهار جدول المقارنة الشهرية
    $('.comparison-section').hide();
    
    var title = '';
    if (data.type === 'monthly_general') {
        title = `المقارنة الشهرية - ${data.moh_name}`;
    } else if (data.type === 'monthly_moh') {
        title = `المقارنة الشهرية - ${data.moh_name}`;
    } else if (data.type === 'monthly_fctype') {
        title = data.moh_name ? 
            `المقارنة الشهرية - ${data.fctype_name} (${data.moh_name})` :
            `المقارنة الشهرية - ${data.fctype_name}`;
    } else {
        title = `المقارنة الشهرية - ${data.fck_name}`;
    }
    
    // جدول الأسرة الشهرية (مدمج)
    var familyComparisonHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الأسرة (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">البيان</th>
    `;
    
    // إضافة أعمدة الأشهر
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `<th class="text-center">${monthData.month_name}</th>`;
    });
    
    familyComparisonHtml += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">عدد الوحدات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center">${(monthData.families.total_units || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                                <tr class="table-info">
                                    <td class="font-weight-bold">عدد الأسرة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center font-weight-bold">${(monthData.families.total_beds || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">المرضى الخارجيون</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center">${(monthData.families.total_outpatients || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">المرضى الداخليون</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center">${(monthData.families.total_inpatients || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">أيام المكوث</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center">${(monthData.families.total_stay_days || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                                <tr class="table-danger">
                                    <td class="font-weight-bold">الوفيات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        familyComparisonHtml += `
                                    <td class="text-center font-weight-bold">${(monthData.families.total_deaths || 0).toLocaleString()}</td>
        `;
    });
    
    familyComparisonHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // جدول العمليات الشهرية (مدمج)
    var surgeryComparisonHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات العمليات (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">نوع العملية</th>
    `;
    
    // إضافة أعمدة الأشهر
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `<th class="text-center">${monthData.month_name}</th>`;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">عمليات خاصة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `
                                    <td class="text-center">${(monthData.surgeries.total_special || 0).toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عمليات كبرى معقدة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `
                                    <td class="text-center">${(monthData.surgeries.total_major_complex || 0).toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عمليات كبرى</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `
                                    <td class="text-center">${(monthData.surgeries.total_major || 0).toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عمليات متوسطة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `
                                    <td class="text-center">${(monthData.surgeries.total_medium || 0).toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عمليات صغرى</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        surgeryComparisonHtml += `
                                    <td class="text-center">${(monthData.surgeries.total_minor || 0).toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                                <tr class="table-success">
                                    <td class="font-weight-bold">الإجمالي</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        var total = monthData.surgeries.total_all || 0;
        surgeryComparisonHtml += `
                                    <td class="text-center font-weight-bold">${total.toLocaleString()}</td>
        `;
    });
    
    surgeryComparisonHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#statisticsResults').append(familyComparisonHtml);
    $('#statisticsResults').append(surgeryComparisonHtml);
    
    // جدول الحوادث الشهرية (مدمج)
    var incidentComparisonHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الحوادث والولادات (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">البيان</th>
    `;
    
    // إضافة أعمدة الأشهر
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `<th class="text-center">${monthData.month_name}</th>`;
    });
    
    incidentComparisonHtml += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">ولادات طبيعية حية</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `
                                    <td class="text-center">${(monthData.incidents.total_live_births_natural || 0).toLocaleString()}</td>
        `;
    });
    
    incidentComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">ولادات قيصرية حية</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `
                                    <td class="text-center">${(monthData.incidents.total_live_births_cesarean || 0).toLocaleString()}</td>
        `;
    });
    
    incidentComparisonHtml += `
                                </tr>
                                <tr class="table-warning">
                                    <td class="font-weight-bold">إجمالي الولادات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `
                                    <td class="text-center font-weight-bold">${(monthData.incidents.total_births || 0).toLocaleString()}</td>
        `;
    });
    
    incidentComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">وفيات الأمهات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `
                                    <td class="text-center">${(monthData.incidents.total_maternal_deaths || 0).toLocaleString()}</td>
        `;
    });
    
    incidentComparisonHtml += `
                                </tr>
                                <tr class="table-danger">
                                    <td class="font-weight-bold">إجمالي الوفيات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        incidentComparisonHtml += `
                                    <td class="text-center font-weight-bold">${(monthData.incidents.total_deaths || 0).toLocaleString()}</td>
        `;
    });
    
    incidentComparisonHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // جدول الاستشارية الشهرية - الملخص (مدمج)
    var consultationComparisonHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الاستشارية (ملخص شهري)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">البيان</th>
    `;
    
    // إضافة أعمدة الأشهر
    data.monthly_data.forEach(function(monthData) {
        consultationComparisonHtml += `<th class="text-center">${monthData.month_name}</th>`;
    });
    
    consultationComparisonHtml += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">إجمالي الصالات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        consultationComparisonHtml += `
                                    <td class="text-center">${(monthData.consultations.total_halls || 0).toLocaleString()}</td>
        `;
    });
    
    consultationComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">إجمالي الأسرة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        consultationComparisonHtml += `
                                    <td class="text-center">${(monthData.consultations.total_hall_beds || 0).toLocaleString()}</td>
        `;
    });
    
    consultationComparisonHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // جدول الاستشارية المدمج مع الأشهر كأعمدة
    var consultationDetailHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الاستشارية التفصيلية (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">نوع الصالة</th>
    `;
    
    // إنشاء رؤوس الأعمدة للأشهر
    data.monthly_data.forEach(function(monthData) {
        consultationDetailHtml += `<th colspan="2" class="text-center">${monthData.month_name}</th>`;
    });
    
    consultationDetailHtml += `
                                </tr>
                                <tr>
    `;
    
    // إنشاء العناوين الفرعية (صالات/أسرة) لكل شهر
    data.monthly_data.forEach(function(monthData) {
        consultationDetailHtml += `
                                    <th class="text-center">صالات</th>
                                    <th class="text-center">أسرة</th>
        `;
    });
    
    consultationDetailHtml += `
                                </tr>
                            </thead>
                            <tbody>
    `;
    
    // جمع جميع أنواع الصالات من جميع الأشهر
    var allConsultationTypes = new Set();
    data.monthly_data.forEach(function(monthData) {
        if (monthData.consultation_details && monthData.consultation_details.length > 0) {
            monthData.consultation_details.forEach(function(detail) {
                allConsultationTypes.add(detail.type_name);
            });
        }
    });
    
    // تحويل إلى مصفوفة وترتيب أبجدياً
    var sortedConsultationTypes = Array.from(allConsultationTypes).sort();
    
    // إنشاء صف لكل نوع صالة
    sortedConsultationTypes.forEach(function(consultationType) {
        consultationDetailHtml += `
                                <tr>
                                    <td class="font-weight-bold">${consultationType}</td>
        `;
        
        // إضافة البيانات لكل شهر
        data.monthly_data.forEach(function(monthData) {
            var typeData = null;
            if (monthData.consultation_details && monthData.consultation_details.length > 0) {
                typeData = monthData.consultation_details.find(detail => detail.type_name === consultationType);
            }
            
            if (typeData) {
                consultationDetailHtml += `
                                    <td class="text-center">${typeData.halls.toLocaleString()}</td>
                                    <td class="text-center">${typeData.beds.toLocaleString()}</td>
                `;
            } else {
                consultationDetailHtml += `
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                `;
            }
        });
        
        consultationDetailHtml += `
                                </tr>
        `;
    });
    
    // إضافة صف الإجماليات
    consultationDetailHtml += `
                                <tr class="table-info font-weight-bold">
                                    <td class="text-center">الإجمالي</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        var totalHalls = 0;
        var totalBeds = 0;
        
        if (monthData.consultation_details && monthData.consultation_details.length > 0) {
            monthData.consultation_details.forEach(function(detail) {
                totalHalls += parseInt(detail.halls) || 0;
                totalBeds += parseInt(detail.beds) || 0;
            });
        }
        
        consultationDetailHtml += `
                                    <td class="text-center">${totalHalls.toLocaleString()}</td>
                                    <td class="text-center">${totalBeds.toLocaleString()}</td>
        `;
    });
    
    consultationDetailHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // جدول الردهات التفصيلي حسب النوع (مدمج)
    var wardDetailHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الردهات حسب النوع (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">نوع الردهة</th>
    `;
    
    // إنشاء رؤوس الأعمدة للأشهر
    data.monthly_data.forEach(function(monthData) {
        wardDetailHtml += `<th colspan="4" class="text-center">${monthData.month_name}</th>`;
    });
    
    wardDetailHtml += `
                                </tr>
                                <tr>
    `;
    
    // إنشاء العناوين الفرعية لكل شهر
    data.monthly_data.forEach(function(monthData) {
        wardDetailHtml += `
                                    <th class="text-center">وحدات</th>
                                    <th class="text-center">أسرة</th>
                                    <th class="text-center">خارجي</th>
                                    <th class="text-center">داخلي</th>
        `;
    });
    
    wardDetailHtml += `
                                </tr>
                            </thead>
                            <tbody>
    `;
    
    // جمع جميع أنواع الردهات من جميع الأشهر
    var allWardTypes = new Set();
    data.monthly_data.forEach(function(monthData) {
        if (monthData.ward_details && monthData.ward_details.length > 0) {
            monthData.ward_details.forEach(function(ward) {
                allWardTypes.add(ward.ward_name);
            });
        }
    });
    
    // تحويل إلى مصفوفة وترتيب أبجدياً
    var sortedWardTypes = Array.from(allWardTypes).sort();
    
    // إنشاء صف لكل نوع ردهة
    sortedWardTypes.forEach(function(wardType) {
        wardDetailHtml += `
                                <tr>
                                    <td class="font-weight-bold">${wardType}</td>
        `;
        
        // إضافة البيانات لكل شهر
        data.monthly_data.forEach(function(monthData) {
            var wardData = null;
            if (monthData.ward_details && monthData.ward_details.length > 0) {
                wardData = monthData.ward_details.find(ward => ward.ward_name === wardType);
            }
            
            if (wardData) {
                wardDetailHtml += `
                                    <td class="text-center">${wardData.total_units.toLocaleString()}</td>
                                    <td class="text-center">${wardData.total_beds.toLocaleString()}</td>
                                    <td class="text-center">${wardData.total_outpatients.toLocaleString()}</td>
                                    <td class="text-center">${wardData.total_inpatients.toLocaleString()}</td>
                `;
            } else {
                wardDetailHtml += `
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                `;
            }
        });
        
        wardDetailHtml += `
                                </tr>
        `;
    });
    
    // إضافة صف الإجماليات
    wardDetailHtml += `
                                <tr class="table-info font-weight-bold">
                                    <td class="text-center">الإجمالي</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        var totalUnits = 0;
        var totalBeds = 0;
        var totalOutpatients = 0;
        var totalInpatients = 0;
        
        if (monthData.ward_details && monthData.ward_details.length > 0) {
            monthData.ward_details.forEach(function(ward) {
                totalUnits += parseInt(ward.total_units) || 0;
                totalBeds += parseInt(ward.total_beds) || 0;
                totalOutpatients += parseInt(ward.total_outpatients) || 0;
                totalInpatients += parseInt(ward.total_inpatients) || 0;
            });
        }
        
        wardDetailHtml += `
                                    <td class="text-center">${totalUnits.toLocaleString()}</td>
                                    <td class="text-center">${totalBeds.toLocaleString()}</td>
                                    <td class="text-center">${totalOutpatients.toLocaleString()}</td>
                                    <td class="text-center">${totalInpatients.toLocaleString()}</td>
        `;
    });
    
    wardDetailHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#statisticsResults').append(incidentComparisonHtml);
    $('#statisticsResults').append(consultationComparisonHtml);
    
    // عرض الجداول التفصيلية فقط إذا كانت هناك بيانات تفصيلية
    var hasConsultationDetails = data.monthly_data.some(monthData => 
        monthData.consultation_details && monthData.consultation_details.length > 0);
    var hasWardDetails = data.monthly_data.some(monthData => 
        monthData.ward_details && monthData.ward_details.length > 0);
        
    if (hasConsultationDetails) {
        $('#statisticsResults').append(consultationDetailHtml);
    }
    
    if (hasWardDetails) {
        $('#statisticsResults').append(wardDetailHtml);
    }
    
    // جدول الحالات والفحوصات (مدمج)
    var casesComparisonHtml = `
        <div class="comparison-section">
            <h4 class="section-title">${title} - إحصائيات الحالات والفحوصات (مقارنة شهرية)</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">البيان</th>
    `;
    
    // إضافة أعمدة الأشهر
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `<th class="text-center">${monthData.month_name}</th>`;
    });
    
    casesComparisonHtml += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">عدد الحالات المرضية في العيادات الاستشارية</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_emergency_cases || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الحالات المرضية في العيادات الخافة او العامة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_light_cases || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الحالات المرضية في عيادات الطوارئ</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_ambulance_cases || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr class="table-secondary">
                                    <td class="font-weight-bold text-center" colspan="${data.monthly_data.length + 1}">الفحوصات</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الفحوصات الساندة للمراجعين</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_outpatient_clinical || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الفحوصات المختبرية للمراجعين</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_outpatient_lab || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الفحوصات الساندة للراقدين</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_inpatient_clinical || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الفحوصات المختبرية للراقدين</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_inpatient_lab || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr class="table-secondary">
                                    <td class="font-weight-bold text-center" colspan="${data.monthly_data.length + 1}">بيانات الأسرة</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الاسرة في المخزن</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_available_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الاسرة في الأخرى</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_other_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الاسرة في الاستشاريات</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_emergency_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الاسرة في الردهات المغلقة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_closed_ward_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">عدد الاسرة الكلية المهيئة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_moh_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr class="table-info">
                                    <td class="font-weight-bold">عدد الاسرة الكلي</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center font-weight-bold">${(monthData.cases.total_all_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">إجمالي الاسرة</td>
    `;
    
    data.monthly_data.forEach(function(monthData) {
        casesComparisonHtml += `
                                    <td class="text-center">${(monthData.cases.total_facility_beds || 0).toLocaleString()}</td>
        `;
    });
    
    casesComparisonHtml += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#statisticsResults').append(casesComparisonHtml);
    
    // إضافة رسوم بيانية
    // createInstitutionMonthlyCharts(data);
}





function displayFamilyStatistics(families) {
    var headerHtml = '';
    var bodyHtml = '';
    
    if (families && families.length > 0) {
        headerHtml = `
            <th>المؤسسة/التخصص</th>
            <th>عدد الوحدات</th>
            <th>عدد الأسرة</th>
            <th>المرضى الخارجيون</th>
            <th>المرضى الداخليون</th>
            <th>أيام المكوث</th>
            <th>الوفيات</th>
        `;
        
        families.forEach(function(family) {
            var name = family.fck ? family.fck.Fckname : (family.rdhs ? family.rdhs.Spcuname : 'غير محدد');
            bodyHtml += `
                <tr>
                    <td>${name}</td>
                    <td>${family.total_units || 0}</td>
                    <td>${family.total_beds || 0}</td>
                    <td>${family.total_outpatients || 0}</td>
                    <td>${family.total_inpatients || 0}</td>
                    <td>${family.total_stay_days || 0}</td>
                    <td>${family.total_deaths || 0}</td>
                </tr>
            `;
        });
    } else {
        headerHtml = '<th colspan="7">لا توجد بيانات متاحة</th>';
        bodyHtml = '<tr><td colspan="7" class="text-center">لا توجد بيانات للفترة المحددة</td></tr>';
    }
    
    $('#familyTableHeader').html(headerHtml);
    $('#familyTableBody').html(bodyHtml);
}

function displaySurgeryStatistics(surgeries) {
    var headerHtml = '';
    var bodyHtml = '';
    
    if (surgeries && surgeries.length > 0) {
        headerHtml = `
            <th>المؤسسة/النوع</th>
            <th>خاصة</th>
            <th>كبرى معقدة</th>
            <th>كبرى</th>
            <th>متوسطة</th>
            <th>صغرى</th>
            <th>الإجمالي</th>
        `;
        
        surgeries.forEach(function(surgery) {
            var name = surgery.fck ? surgery.fck.Fckname : (surgery.surg ? surgery.surg.surgtyp : 'غير محدد');
            var total = surgery.total_all || ((surgery.total_special || 0) + (surgery.total_major_complex || 0) + 
                       (surgery.total_major || 0) + (surgery.total_medium || 0) + (surgery.total_minor || 0));
            
            bodyHtml += `
                <tr>
                    <td>${name}</td>
                    <td>${surgery.total_special || 0}</td>
                    <td>${surgery.total_major_complex || 0}</td>
                    <td>${surgery.total_major || 0}</td>
                    <td>${surgery.total_medium || 0}</td>
                    <td>${surgery.total_minor || 0}</td>
                    <td><strong>${total}</strong></td>
                </tr>
            `;
        });
    } else {
        headerHtml = '<th colspan="7">لا توجد بيانات متاحة</th>';
        bodyHtml = '<tr><td colspan="7" class="text-center">لا توجد بيانات للفترة المحددة</td></tr>';
    }
    
    $('#surgeryTableHeader').html(headerHtml);
    $('#surgeryTableBody').html(bodyHtml);
}

function displayIncidentStatistics(incidents) {
    var headerHtml = '';
    var bodyHtml = '';
    
    if (incidents && (incidents.length > 0 || incidents.total_births)) {
        headerHtml = `
            <th>المؤسسة</th>
            <th>ولادات طبيعية حية</th>
            <th>ولادات قيصرية حية</th>
            <th>إجمالي الولادات</th>
            <th>وفيات الأمهات</th>
            <th>إجمالي الوفيات</th>
        `;
        
        if (Array.isArray(incidents)) {
            incidents.forEach(function(incident) {
                var name = incident.fck ? incident.fck.Fckname : 'غير محدد';
                bodyHtml += `
                    <tr>
                        <td>${name}</td>
                        <td>${incident.total_live_births_natural || 0}</td>
                        <td>${incident.total_live_births_cesarean || 0}</td>
                        <td>${incident.total_births || 0}</td>
                        <td>${incident.total_maternal_deaths || 0}</td>
                        <td>${incident.total_deaths || 0}</td>
                    </tr>
                `;
            });
        } else {
            // Single institution data
            bodyHtml = `
                <tr>
                    <td>البيانات الإجمالية</td>
                    <td>${incidents.total_live_births_natural || 0}</td>
                    <td>${incidents.total_live_births_cesarean || 0}</td>
                    <td>${incidents.total_births || 0}</td>
                    <td>${incidents.total_maternal_deaths || 0}</td>
                    <td>${incidents.total_deaths || 0}</td>
                </tr>
            `;
        }
    } else {
        headerHtml = '<th colspan="6">لا توجد بيانات متاحة</th>';
        bodyHtml = '<tr><td colspan="6" class="text-center">لا توجد بيانات للفترة المحددة</td></tr>';
    }
    
    $('#incidentTableHeader').html(headerHtml);
    $('#incidentTableBody').html(bodyHtml);
}

function displayConsultationStatistics(consultations) {
    var headerHtml = '';
    var bodyHtml = '';
    
    if (consultations && consultations.length > 0) {
        headerHtml = `
            <th>المؤسسة/النوع</th>
            <th>عدد الصالات</th>
            <th>عدد أسرة الصالات</th>
        `;
        
        consultations.forEach(function(consultation) {
            var name = consultation.fck ? consultation.fck.Fckname : 
                      (consultation.salsurs ? consultation.salsurs.sursname : 'غير محدد');
            
            bodyHtml += `
                <tr>
                    <td>${name}</td>
                    <td>${consultation.total_halls || 0}</td>
                    <td>${consultation.total_hall_beds || 0}</td>
                </tr>
            `;
        });
    } else {
        headerHtml = '<th colspan="3">لا توجد بيانات متاحة</th>';
        bodyHtml = '<tr><td colspan="3" class="text-center">لا توجد بيانات للفترة المحددة</td></tr>';
    }
    
    $('#consultationTableHeader').html(headerHtml);
    $('#consultationTableBody').html(bodyHtml);
}

function exportStatistics() {
    var formData = $('#statisticsForm').serialize();
    
    // Create a temporary form and submit it to download the file
    var form = $('<form>', {
        'method': 'GET',
        'action': '{{ route("statistics.export") }}'
    });
    
    // Add form data as hidden inputs
    var formDataArray = $('#statisticsForm').serializeArray();
    $.each(formDataArray, function(i, field) {
        form.append($('<input>', {
            'type': 'hidden',
            'name': field.name,
            'value': field.value
        }));
    });
    
    // Append form to body and submit
    $('body').append(form);
    form.submit();
    
    // Remove form after submission
    setTimeout(function() {
        form.remove();
    }, 100);
    
    // Show success message
    setTimeout(function() {
        alert('بدء تنزيل ملف الإكسل...');
    }, 500);
}

function resetForm() {
    $('#statisticsForm')[0].reset();
    $('#moh_id').val('all').trigger('change');
    $('#fctype_id').val('all').trigger('change');
    $('#fck_id').val('all').trigger('change');
    $('#year').val(new Date().getFullYear()).trigger('change');
    $('#statisticsResults').hide();
    $('.print-btn').hide();
}

function printStatistics() {
    // تحديث عنوان الطباعة
    updatePrintTitle();
    
    // إظهار رأس الطباعة
    $('.print-header').removeClass('d-none');
    
    // تطبيق تحسينات إضافية للطباعة الأفقية
    $('body').addClass('printing');
    
    // إضافة CSS ديناميكي للطباعة الأفقية
    var printStyle = `
        <style id="print-landscape-style">
            @media print {
                @page { 
                    size: A4 landscape; 
                    margin: 0.5cm; 
                }
                body { 
                    transform: rotate(0deg); 
                    transform-origin: left top; 
                    width: 100% !important; 
                    height: 100% !important; 
                }
                .table { 
                    font-size: 8px !important; 
                    width: 100% !important;
                }
                .table th, .table td { 
                    padding: 1px 2px !important; 
                    font-size: 8px !important;
                    word-break: break-word;
                }
                .section-title { 
                    font-size: 11px !important; 
                    margin: 8px 0 4px 0 !important;
                }
            }
        </style>
    `;
    
    // إضافة الستايل للصفحة مؤقتاً
    $('head').append(printStyle);
    
    // الطباعة مع تأخير قصير للتأكد من تطبيق الستايلات
    setTimeout(function() {
        window.print();
        
        // تنظيف بعد الطباعة
        setTimeout(function() {
            $('.print-header').addClass('d-none');
            $('body').removeClass('printing');
            $('#print-landscape-style').remove();
        }, 100);
    }, 200);
}

function updatePrintTitle() {
    var mohSelect = $('#moh_id');
    var fckSelect = $('#fck_id');
    var fctypeSelect = $('#fctype_id');
    var year = $('#year').val();
    
    var title = 'تقرير الإحصائيات الشهرية لعام ' + year;
    
    if (fckSelect.val() && fckSelect.val() !== 'all') {
        title += ' - ' + fckSelect.find('option:selected').text();
    } else if (fctypeSelect.val() && fctypeSelect.val() !== 'all') {
        title += ' - ' + fctypeSelect.find('option:selected').text();
        if (mohSelect.val() && mohSelect.val() !== 'all') {
            title += ' (' + mohSelect.find('option:selected').text() + ')';
        }
    } else if (mohSelect.val() && mohSelect.val() !== 'all') {
        title += ' - ' + mohSelect.find('option:selected').text();
    } else {
        title += ' - جميع الدوائر والمؤسسات';
    }
    
    $('#print-title').text(title);
}
</script>
@endsection
