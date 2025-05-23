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
<!-- Internal Select2 css -->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@section('script')
<!-- Internal Select2 js-->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
@endsection
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
                        </div>                        <!-- الرسوم والأجور -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-green text-center">
                                <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> الرسوم والأجور</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">الخدمات المقدمة</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-8">                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">                                                                <select id="service_select" class="form-control select2">
                                                                    <option value="">اختر الخدمة...</option>
                                                                    @foreach($services as $service)
                                                                        <option value="{{ $service->id }}">{{ $service->sername }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <select id="specialization_select" class="form-control select2">
                                                                    <option value="">اختر التخصص...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <select id="service_specialization_select" class="form-control select2">
                                                                    <option value="">اختر الخدمة المتخصصة...</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-primary" id="add_service">
                                                            <i class="fas fa-plus-circle"></i> إضافة خدمة
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="services_table">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>رمز الخدمة</th>
                                                                <th>اسم الخدمة</th>
                                                                <th width="150">السعر (د.ع)</th>
                                                                <th width="100">حذف</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="2" class="text-left">المجموع الكلي</th>
                                                                <th colspan="2" id="total_amount">0.00 د.ع</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- أجور الأسرّة والإقامة -->
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_bed_fee" class="form-label fw-bold">أجور رقود المريض</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm fee-input" id="patient_bed_fee" name="patient_bed_fee" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="companion_bed_fee" class="form-label fw-bold">أجور رقود المرافق</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm fee-input" id="companion_bed_fee" name="companion_bed_fee">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="nutrition_fee" class="form-label fw-bold">أجور التغذية</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="number" step="0.01" class="form-control shadow-sm fee-input" id="nutrition_fee" name="nutrition_fee">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input hidden للخدمات -->
                        <input type="hidden" name="services" id="services_input" value="[]">

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
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + 0.75rem);
        padding-right: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem);
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
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        width: '100%',
        dir: 'rtl'
    });    // Service selection change event
    $('#service_select').on('change', function() {
        var serviceId = $(this).val();
        var specializationSelect = $('#specialization_select');
        var serviceSpecializationSelect = $('#service_specialization_select');
        
        // Reset dependent dropdowns
        specializationSelect.empty().append('<option value="">اختر التخصص...</option>');
        serviceSpecializationSelect.empty().append('<option value="">اختر الخدمة المتخصصة...</option>');
        
        if (serviceId) {
            specializationSelect.prop('disabled', false);
            specializationSelect.html('<option value="">جاري التحميل...</option>');
            // Fetch specializations
            $.ajax({
                url: '/get-specializations/' + serviceId,
                type: 'GET',
                success: function(data) {                    console.log('Received specializations:', data);
                    specializationSelect.empty().append('<option value="">اختر التخصص...</option>');
                    
                    if (data && data.length > 0) {
                        data.forEach(function(specialization) {
                            specializationSelect.append(
                                $('<option></option>')
                                    .val(specialization.id)
                                    .text(specialization.tsname)
                            );
                        });
                        specializationSelect.prop('disabled', false);
                    } else {
                        specializationSelect.append('<option value="">لا توجد تخصصات متاحة</option>');
                        console.log('No specializations found for service:', serviceId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching specializations:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    alert('حدث خطأ أثناء جلب التخصصات. الرجاء المحاولة مرة أخرى.');
                }
            });
        }
    });    // Specialization selection change event
    $('#specialization_select').on('change', function() {
        var serviceId = $('#service_select').val();
        var specializationId = $(this).val();
        var serviceSpecializationSelect = $('#service_specialization_select');
        
        // Reset service specialization dropdown
        serviceSpecializationSelect.empty().append('<option value="">اختر الخدمة المتخصصة...</option>').prop('disabled', false);
        
        if (serviceId && specializationId) {
            // Fetch service specializations            $.ajax({
                url: '/get-service-specializations/' + serviceId + '/' + specializationId,
                type: 'GET',
                success: function(data) {                    console.log('Received service specializations:', data);
                    serviceSpecializationSelect.empty().append('<option value="">اختر الخدمة المتخصصة...</option>');
                    
                    if (data && data.length > 0) {
                        data.forEach(function(specialization) {
                            var optionText = specialization.namesv + ' (' + specialization.codesv + ')';
                            var $option = $('<option></option>')
                                .val(specialization.id)
                                .text(optionText)
                                .data({
                                    'price': specialization.price,
                                    'code': specialization.codesv
                                });
                            serviceSpecializationSelect.append($option);
                        });
                        serviceSpecializationSelect.prop('disabled', false);
                    } else {
                        serviceSpecializationSelect.append('<option value="">لا توجد خدمات متخصصة متاحة</option>');
                        console.log('No service specializations found for service:', serviceId, 'and specialization:', specializationId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching service specializations:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    alert('حدث خطأ أثناء جلب الخدمات المتخصصة. الرجاء المحاولة مرة أخرى.');
                }
            });
        }
    });

    // Add service button click event
    $('#add_service').on('click', function(e) {
        e.preventDefault();
        
        var serviceId = $('#service_select').val();
        var specializationId = $('#specialization_select').val();
        var serviceSpecializationId = $('#service_specialization_select').val();
        var serviceSpecializationOption = $('#service_specialization_select option:selected');
        
        if (!serviceId || !specializationId || !serviceSpecializationId) {
            alert('الرجاء اختيار جميع الخدمات المطلوبة');
            return;
        }

        var combinedId = serviceId + '_' + specializationId + '_' + serviceSpecializationId;
        var serviceName = serviceSpecializationOption.text();
        var servicePrice = serviceSpecializationOption.data('price') || 0;

        // Check if service already exists
        if ($('#services_table tbody').find('tr[data-id="' + combinedId + '"]').length === 0) {
            // Add row to table
            var newRow = `
                <tr data-id="${combinedId}">
                    <td>${serviceSpecializationOption.data('code') || '-'}</td>
                    <td>${serviceName}</td>
                    <td>
                        <input type="number" step="0.01" class="form-control service-price" value="${servicePrice}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-service">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#services_table tbody').append(newRow);
            updateTotalAmount();
            updateServicesInput();

            // Reset selections
            $('#service_select, #specialization_select, #service_specialization_select').val('').trigger('change');
            $('#specialization_select, #service_specialization_select').prop('disabled', true);
        } else {
            alert('هذه الخدمة مضافة مسبقاً');
        }
    });

    // Delete service click event
    $(document).on('click', '.delete-service', function() {
        $(this).closest('tr').remove();
        updateTotalAmount();
        updateServicesInput();
    });

    // Service price change event
    $(document).on('change', '.service-price', function() {
        updateTotalAmount();
        updateServicesInput();
    });

    // Update total amount
    function updateTotalAmount() {
        var total = 0;
        $('.service-price').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_amount').text(total.toFixed(2) + ' د.ع');
    }

    // Update hidden services input
    function updateServicesInput() {
        var services = [];
        $('#services_table tbody tr').each(function() {
            services.push({
                id: $(this).data('id'),
                price: $(this).find('.service-price').val()
            });
        });
        $('#services_input').val(JSON.stringify(services));
    }
});
</script>
@endpush

@endsection
