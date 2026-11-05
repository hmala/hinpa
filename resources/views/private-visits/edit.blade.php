@extends('layouts.master')
@section('title')
تعديل سجل الزيارة والخدمة
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .section-title {
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
        margin-bottom: 20px;
        color: #007bff;
        font-weight: bold;
    }
    .calculation-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #ced4da;
        border-radius: 8px;
    }
</style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الزيارات الخارجية</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل سجل الزيارة</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('private-visits.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
        </a>
    </div>
</div>
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i> تعديل بيانات وصل الزيارة رقم #{{ $visit->sequential_number }} (السنوي: {{ $visit->annual_number }})
                    </h5>
                    <span class="badge badge-light text-dark font-weight-bold px-3 py-2">
                        {{ $visit->hospital ?: 'المستشفى' }}
                    </span>
                </div>
                <div class="card-body p-4 bg-light">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading font-weight-bold"><i class="fas fa-exclamation-triangle me-1"></i> تنبيهات الإدخال:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('private-visits.update', $visit->id) }}" method="POST" id="visitEditForm">
                        @csrf
                        @method('PUT')

                        <!-- 1. تحديد الخدمة -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 text-primary font-weight-bold">
                                    <i class="fas fa-stethoscope me-2"></i> 1. تحديد الخدمة المقدمة
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fckn" class="form-label font-weight-bold">التخصص الفرعي / الخدمة العامة <span class="text-danger">*</span></label>
                                        <select id="fckn" name="fckn" class="form-control select2" required>
                                            <option value="" disabled>-- اختر التخصص الفرعي --</option>
                                            @foreach($subSpecializations as $sub)
                                                <option value="{{ $sub->id }}" {{ $selectedSubSpecId == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->sercode ? $sub->sercode . ' - ' : '' }}{{ $sub->sername }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="fckr" class="form-label font-weight-bold">الخدمة التفصيلية <span class="text-danger">*</span></label>
                                        <select id="fckr" name="service_id" class="form-control select2" required>
                                            <option value="" disabled>-- اختر الخدمة التفصيلية --</option>
                                            @foreach($detailServices as $detail)
                                                <option value="{{ $detail->id }}" data-price="{{ $detail->price ?? 0 }}" {{ $visit->service_id == $detail->id ? 'selected' : '' }}>
                                                    {{ $detail->codesv ? $detail->codesv . ' - ' : '' }}{{ $detail->namesv }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. بيانات المريض والوصل -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 text-primary font-weight-bold">
                                    <i class="fas fa-user-check me-2"></i> 2. بيانات المريض والوصل المالي
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="visitor_name" class="form-label font-weight-bold">اسم المريض / الزائر <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control font-weight-bold" id="visitor_name" name="visitor_name" value="{{ old('visitor_name', $visit->visitor_name ?? $visit->patient_name) }}" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="visitor_nationality" class="form-label font-weight-bold">الجنسية</label>
                                        <select class="form-control" id="visitor_nationality" name="visitor_nationality">
                                            <option value="عراقي" {{ old('visitor_nationality', $visit->visitor_nationality ?? $visit->patient_nationality) == 'عراقي' ? 'selected' : '' }}>عراقي</option>
                                            <option value="عربي" {{ old('visitor_nationality', $visit->visitor_nationality ?? $visit->patient_nationality) == 'عربي' ? 'selected' : '' }}>عربي</option>
                                            <option value="أجنبي" {{ old('visitor_nationality', $visit->visitor_nationality ?? $visit->patient_nationality) == 'أجنبي' ? 'selected' : '' }}>أجنبي</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="doctor_id" class="form-label font-weight-bold">الطبيب المعالج</label>
                                        <select class="form-control select2" id="doctor_id" name="doctor_id">
                                            <option value="">-- اختياري --</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $visit->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                    {{ $doctor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="visit_date" class="form-label font-weight-bold">تاريخ الزيارة <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="visit_date" name="visit_date" value="{{ old('visit_date', $visit->visit_date ? $visit->visit_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="payment_type" class="form-label font-weight-bold">طريقة الدفع <span class="text-danger">*</span></label>
                                        <select class="form-control font-weight-bold" id="payment_type" name="payment_type" required>
                                            <option value="كاش" {{ old('payment_type', $visit->payment_type) == 'كاش' ? 'selected' : '' }}>كاش</option>
                                            <option value="إلكتروني" {{ old('payment_type', $visit->payment_type) == 'إلكتروني' ? 'selected' : '' }}>إلكتروني</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="receipt_number" class="form-label font-weight-bold">رقم الوصل <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="{{ old('receipt_number', $visit->receipt_number) }}" required>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="receipt_date" class="form-label font-weight-bold">تاريخ الوصل <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="{{ old('receipt_date', $visit->receipt_date ? $visit->receipt_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_fee" class="form-label font-weight-bold">المبلغ الأساسي للخدمة (د.ع) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control font-weight-bold text-success fs-18" id="service_fee" name="service_fee" value="{{ old('service_fee', $visit->service_fee ?? 0) }}" placeholder="0.00" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="interest_rate" class="form-label font-weight-bold">نسبة الفائدة %</label>
                                        @php
                                            $currRate = old('interest_rate', $visit->interest_rate);
                                            $isElectronic = old('payment_type', $visit->payment_type) == 'إلكتروني';
                                        @endphp
                                        <input type="number" step="0.01" min="0" max="100" class="form-control font-weight-bold text-center fs-18" id="interest_rate" name="interest_rate" value="{{ $isElectronic ? '0' : ($currRate > 0 ? (float)$currRate : '') }}" placeholder="0" {{ $isElectronic ? 'readonly style=background-color:#e9ecef;' : '' }}>
                                    </div>
                                </div>

                                <!-- بطاقة ملخص الحساب -->
                                <div class="calculation-card p-3 mt-3">
                                    <div class="row text-center align-items-center">
                                        <div class="col-md-4 border-left">
                                            <span class="text-muted fs-14">المبلغ الأساسي:</span>
                                            <h5 class="mb-0 font-weight-bold text-dark" id="display_base_fee">0.00 د.ع</h5>
                                        </div>
                                        <div class="col-md-4 border-left">
                                            <span class="text-muted fs-14">مبلغ الفائدة / الزيادة:</span>
                                            <h5 class="mb-0 font-weight-bold text-warning" id="display_interest_amount">0.00 د.ع</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="text-muted fs-14">المجموع الكلي النهائي:</span>
                                            <h4 class="mb-0 font-weight-bold text-success" id="display_grand_total">0.00 د.ع</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="text-center mt-4 mb-3">
                            <button type="submit" class="btn btn-success btn-lg px-5 font-weight-bold shadow">
                                <i class="fas fa-save me-1"></i> حفظ وتحديث التعديلات
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
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: 'ابحث أو اختر...',
            dropdownAutoWidth: true,
            minimumResultsForSearch: 0
        });

        // 1. عند تغيير التخصص الفرعي (fckn) جلب الخدمات التفصيلية (fckr)
        $('#fckn').on('change', function() {
            var serviceId = $(this).val();
            var fckrSelect = $('#fckr');

            fckrSelect.empty().append('<option value="" disabled selected>جاري التحميل...</option>');

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
                                fckrSelect.append('<option value="' + value.id + '">' + text + '</option>');
                            });
                        } else {
                            fckrSelect.append('<option value="" disabled>لا توجد خدمات تفصيلية مضافة لهذا التخصص</option>');
                        }
                        fckrSelect.select2({ width: '100%', minimumResultsForSearch: 0 }).trigger('change.select2');
                    }
                });
            }
        });

        // 2. التحكم في حقل الفائدة عند تغيير نوع الدفع
        $('#payment_type').on('change', function() {
            var pType = $(this).val();
            var interestInput = $('#interest_rate');

            if (pType === 'إلكتروني') {
                interestInput.val(0).prop('readonly', true).css('background-color', '#e9ecef');
            } else {
                if (interestInput.val() === '0') {
                    interestInput.val('');
                }
                interestInput.prop('readonly', false).css('background-color', '#fff');
            }
            recalculateSummary();
        });

        // 3. حساب المبالغ تلقائياً
        $('#service_fee, #interest_rate').on('input change', function() {
            recalculateSummary();
        });

        function recalculateSummary() {
            var fee = parseFloat($('#service_fee').val()) || 0;
            var paymentType = $('#payment_type').val();
            var rate = (paymentType === 'كاش') ? (parseFloat($('#interest_rate').val()) || 0) : 0;
            var interestAmount = Math.round((fee * (rate / 100)) * 100) / 100;
            var grandTotal = fee + interestAmount;

            $('#display_base_fee').text(fee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' د.ع');
            $('#display_interest_amount').text(interestAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' د.ع');
            $('#display_grand_total').text(grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' د.ع');
        }

        recalculateSummary();
    });
</script>
@endsection
