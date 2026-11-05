@extends('layouts.master')
@section('title')
تسجيل قائمة زيارات / خدمات خارجية
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .patient-row td {
        vertical-align: middle !important;
        padding: 8px 6px !important;
    }
    .patient-row .form-control {
        height: 38px;
        font-size: 13px;
    }
</style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الزيارات والخدمات الخارجية</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تسجيل قائمة زيارات لخدمة</span>
        </div>
    </div>
</div>
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i> تسجيل زيارات وخدمات خارجية للمرضى</h4>
                    <span class="badge badge-light fs-14 text-dark px-3 py-2">السنة المالية: {{ $currentYear }}</span>
                </div>
                <div class="card-body bg-light">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('private-visits.store') }}" method="POST" id="bulkVisitForm">
                        @csrf
                        <input type="hidden" name="moh_id" value="{{ Auth::user()->mohcode }}">
                        <input type="hidden" name="fck_id" value="{{ Auth::user()->fckid }}">

                        <!-- بيانات المؤسسة والخدمة المختارة -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-dark text-white py-2">
                                <h6 class="mb-0"><i class="fas fa-hospital-alt me-1"></i> 1. تحديد المؤسسة والخدمة المقدمة</h6>
                            </div>
                            <div class="card-body bg-white">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label font-weight-bold">المستشفى</label>
                                        <input type="text" class="form-control bg-light" id="hospital" name="hospital" value="{{ Auth::user()->fck ? Auth::user()->fck->Fckname : '' }}" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label font-weight-bold">دائرة الصحة</label>
                                        <input type="text" class="form-control bg-light" id="health_department" name="health_department" value="{{ Auth::user()->mohcode ? \App\Models\mohs::find(Auth::user()->mohcode)->mohname : '' }}" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label font-weight-bold">تاريخ الزيارة <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="visit_date" name="visit_date" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label font-weight-bold">التخصص الرئيسي</label>
                                        <select name="section_id_display" id="section_id" class="form-control select2" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                            @foreach($typeSpecializations as $typeSpecialization)
                                                <option value="{{ $typeSpecialization->id }}" {{ ($typeSpecialization->id == 8 || $typeSpecialization->tsname == 'عام') ? 'selected' : '' }}>
                                                    {{ $typeSpecialization->tsname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="section_id" value="8">
                                    </div>
                                </div>

                                <div class="row align-items-end p-3 rounded" style="background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                                    <div class="col-md-5 mb-2">
                                        <label for="fckn" class="form-label font-weight-bold text-primary">التخصص الفرعي / الخدمة العامة <span class="text-danger">*</span></label>
                                        <select id="fckn" name="fckn" class="form-control select2" required>
                                            <option value="" disabled selected>-- حدد التخصص الفرعي أولاً --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="fckr" class="form-label font-weight-bold text-success">اسم الخدمة التفصيلية المطلوبة <span class="text-danger">*</span></label>
                                        <select id="fckr" name="service_id" class="form-control select2" required>
                                            <option value="" disabled selected>-- حدد الخدمة التفصيلية --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="manual_service_price" class="form-label font-weight-bold text-dark">سعر الخدمة (إدخال يدوي) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control font-weight-bold text-success fs-15" id="manual_service_price" placeholder="أدخل السعر">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light font-weight-bold">د.ع</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- جدول إدخال معلومات المرضى للخدمة -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                                <h5 class="mb-0 text-primary font-weight-bold"><i class="fas fa-user-friends me-2"></i> 2. قائمة المرضى المشمولين بالخدمة</h5>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn_add_patient">
                                        <i class="fas fa-user-plus me-1"></i> إضافة سطر مريض
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm ms-1" id="btn_add_5_patients">
                                        <i class="fas fa-plus-circle me-1"></i> +5 مرضى
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center align-middle mb-0" id="patients_table">
                                        <thead class="bg-primary text-white" style="font-size: 13px;">
                                            <tr>
                                                <th style="width: 3%;">#</th>
                                                <th style="width: 22%;">اسم المريض / الزائر <span class="text-warning">*</span></th>
                                                <th style="width: 9%;">الجنسية</th>
                                                <th style="width: 14%;">الطبيب المعالج</th>
                                                <th style="width: 10%;">طريقة الدفع</th>
                                                <th style="width: 11%;">رقم الوصل <span class="text-warning">*</span></th>
                                                <th style="width: 10%;">تاريخ الوصل <span class="text-warning">*</span></th>
                                                <th style="width: 9%;">المبلغ (د.ع) <span class="text-warning">*</span></th>
                                                <th style="width: 9%;">نسبة الفائدة %</th>
                                                <th style="width: 3%;">حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- أسطر المرضى تضاف ديناميكياً بواسطة جافاسكريبت -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <span class="fs-15 text-muted">عدد المرضى: </span>
                                    <strong class="fs-16 text-primary" id="patients_count">0</strong>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fs-16 font-weight-bold me-2 ml-2">المجموع الكلي للمبالغ: </span>
                                    <h3 class="mb-0 text-success font-weight-bold" id="grand_total_display">0.00 د.ع</h3>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="text-center mt-4 mb-4">
                            <button type="submit" class="btn btn-success btn-lg px-5 font-weight-bold shadow">
                                <i class="fas fa-save me-1"></i> حفظ وتأكيد جميع الزيارات
                            </button>
                            <a href="{{ route('private-visits.index') }}" class="btn btn-secondary btn-lg px-4 ms-2">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    var currentServicePrice = 0;
    var patientRowIndex = 0;
    var doctorsList = @json($doctors);

    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: 'ابحث أو اختر...',
            dropdownAutoWidth: true,
            minimumResultsForSearch: 0
        });

        // 1. تحميل التخصصات الفرعية تلقائياً للتخصص العام (section_id)
        function loadGeneralSubSpecializations() {
            var fcknSelect = $('#fckn');
            var fckrSelect = $('#fckr');
            var sectionId = $('#section_id').val() || $('input[name="section_id"]').val() || 8;
            
            fcknSelect.empty().append('<option value="" disabled selected>جاري التحميل...</option>');
            fckrSelect.empty().append('<option value="" disabled selected>-- حدد التخصص الفرعي أولاً --</option>');

            $.ajax({
                url: "{{ route('private-visits.get-specializations') }}",
                type: "GET",
                data: { section_id: sectionId },
                success: function(data) {
                    fcknSelect.empty().append('<option value="" disabled selected>-- حدد التخصص الفرعي --</option>');
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            var text = (value.sercode ? value.sercode + ' - ' : '') + (value.sername || 'تخصص فرعي');
                            fcknSelect.append('<option value="' + value.id + '">' + text + '</option>');
                        });
                    }
                    fcknSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching specializations:', error);
                    fcknSelect.empty().append('<option value="" disabled selected>-- تعذر تحميل التخصصات --</option>');
                    fcknSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                }
            });
        }
        loadGeneralSubSpecializations();

        // 2. عند تغيير التخصص الفرعي (fckn) جلب الخدمات التفصيلية (fckr)
        $('#fckn').on('change', function() {
            var serviceId = $(this).val();
            var fckrSelect = $('#fckr');

            fckrSelect.empty().append('<option value="" disabled selected>جاري التحميل...</option>');
            currentServicePrice = 0;
            $('#manual_service_price').val('');

            if (serviceId) {
                $.ajax({
                    url: "{{ route('private-visits.get-service-specializations') }}",
                    type: "GET",
                    data: { service_id: serviceId },
                    success: function(data) {
                        fckrSelect.empty().append('<option value="" disabled selected>-- حدد الخدمة التفصيلية --</option>');
                        if (data && data.length > 0) {
                            $.each(data, function(key, value) {
                                var text = (value.codesv ? value.codesv + ' - ' : '') + value.namesv;
                                var price = (value.price && parseFloat(value.price) > 0) ? value.price : '';
                                fckrSelect.append('<option value="' + value.id + '" data-price="' + price + '">' + text + '</option>');
                            });
                        } else {
                            fckrSelect.append('<option value="" disabled>لا توجد خدمات تفصيلية مضافة لهذا التخصص</option>');
                        }
                        fckrSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching service specializations:', error);
                        fckrSelect.empty().append('<option value="" disabled selected>-- تعذر تحميل الخدمات التفصيلية --</option>');
                        fckrSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                    }
                });
            } else {
                fckrSelect.empty().append('<option value="" disabled selected>-- حدد التخصص الفرعي أولاً --</option>');
                fckrSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
            }
        });

        // 2.1 عند اختيار الخدمة التفصيلية ملء السعر الافتراضي تلقائياً إن وجد
        $('#fckr').on('change', function() {
            var selectedOpt = $(this).find('option:selected');
            var price = parseFloat(selectedOpt.data('price')) || 0;
            if (price > 0) {
                $('#manual_service_price').val(price).trigger('input');
            }
        });

        // 3. عند تعديل السعر اليدوي في الأعلى ينعكس على صفوف المرضى
        $('#manual_service_price').on('input change', function() {
            currentServicePrice = parseFloat($(this).val()) || 0;
            $('.patient-fee-input').each(function() {
                $(this).val(currentServicePrice > 0 ? currentServicePrice : '');
            });
            calculateGrandTotal();
        });

        // دالة إنشاء سطر مريض
        function createPatientRow() {
            var today = new Date().toISOString().split('T')[0];
            var docOptions = '<option value="">-- اختياري --</option>';
            $.each(doctorsList, function(i, doc) {
                docOptions += '<option value="' + doc.id + '">' + doc.name + '</option>';
            });

            var priceVal = $('#manual_service_price').val() || (currentServicePrice > 0 ? currentServicePrice : '');

            var rowHtml = `
                <tr class="patient-row" data-index="${patientRowIndex}">
                    <td class="row-num font-weight-bold text-muted">${$('#patients_table tbody tr').length + 1}</td>
                    <td>
                        <input type="text" class="form-control font-weight-bold" name="patients[${patientRowIndex}][visitor_name]" placeholder="اسم المريض الكامل" required>
                    </td>
                    <td>
                        <select class="form-control" name="patients[${patientRowIndex}][visitor_nationality]">
                            <option value="عراقي" selected>عراقي</option>
                            <option value="عربي">عربي</option>
                            <option value="أجنبي">أجنبي</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control select-doctor" name="patients[${patientRowIndex}][doctor_id]">
                            ${docOptions}
                        </select>
                    </td>
                    <td>
                        <select class="form-control patient-payment-type" name="patients[${patientRowIndex}][payment_type]">
                            <option value="كاش" selected>كاش</option>
                            <option value="إلكتروني">إلكتروني</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="patients[${patientRowIndex}][receipt_number]" placeholder="رقم الوصل" required>
                    </td>
                    <td>
                        <input type="date" class="form-control" name="patients[${patientRowIndex}][receipt_date]" value="${today}" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control font-weight-bold text-success patient-fee-input" name="patients[${patientRowIndex}][fee]" value="${priceVal}" placeholder="0.00" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" max="100" class="form-control text-center font-weight-bold patient-interest-input" name="patients[${patientRowIndex}][interest_rate]" value="" placeholder="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="حذف السطر">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#patients_table tbody').append(rowHtml);
            patientRowIndex++;
            renumberRows();
            calculateGrandTotal();
        }

        // إضافة سطر مريض أولي تلقائياً
        createPatientRow();

        // زر إضافة سطر مريض
        $('#btn_add_patient').on('click', function() {
            createPatientRow();
        });

        // زر إضافة 5 مرضى
        $('#btn_add_5_patients').on('click', function() {
            for (var i = 0; i < 5; i++) {
                createPatientRow();
            }
        });

        // تغيير نوع الدفع (كاش/إلكتروني) للسطر
        $(document).on('change', '.patient-payment-type', function() {
            var row = $(this).closest('tr');
            var interestInput = row.find('.patient-interest-input');
            var paymentType = $(this).val();

            if (paymentType === 'إلكتروني') {
                interestInput.val(0).prop('readonly', true).css('background-color', '#e9ecef');
            } else {
                if (interestInput.val() === '0') {
                    interestInput.val('');
                }
                interestInput.prop('readonly', false).css('background-color', '#fff');
            }
            calculateGrandTotal();
        });

        // حذف سطر مريض
        $(document).on('click', '.btn-remove-row', function() {
            if ($('#patients_table tbody tr').length <= 1) {
                alert('يجب الإبقاء على سطر مريض واحد على الأقل.');
                return;
            }
            $(this).closest('tr').remove();
            renumberRows();
            calculateGrandTotal();
        });

        // حساب الإجمالي عند تغيير السعر أو نسبة الفائدة
        $(document).on('input', '.patient-fee-input, .patient-interest-input', function() {
            calculateGrandTotal();
        });

        function renumberRows() {
            $('#patients_table tbody tr').each(function(index) {
                $(this).find('.row-num').text(index + 1);
            });
            $('#patients_count').text($('#patients_table tbody tr').length);
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $('#patients_table tbody tr').each(function() {
                var fee = parseFloat($(this).find('.patient-fee-input').val()) || 0;
                var paymentType = $(this).find('.patient-payment-type').val();
                var rate = (paymentType === 'كاش') ? (parseFloat($(this).find('.patient-interest-input').val()) || 0) : 0;
                var interest = Math.round((fee * (rate / 100)) * 100) / 100;
                grandTotal += (fee + interest);
            });
            $('#grand_total_display').text(grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' د.ع');
            $('#patients_count').text($('#patients_table tbody tr').length);
        }

        // التحقق قبل الحفظ
        $('#bulkVisitForm').on('submit', function(e) {
            var serviceId = $('#fckr').val();
            if (!serviceId) {
                e.preventDefault();
                alert('الرجاء اختيار الخدمة التفصيلية أولاً.');
                $('#fckr').focus();
                return false;
            }

            if ($('#patients_table tbody tr').length === 0) {
                e.preventDefault();
                alert('الرجاء إضافة مريض واحد على الأقل.');
                return false;
            }
        });
    });
</script>
@endsection
