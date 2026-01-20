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
    }
</style>
@endsection
@section('content')
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
                                    <div class="col-md-6 mb-3">
                                        <label for="table_number" class="form-label fw-bold">رقم الطبلة</label>
                                        <input type="text" class="form-control shadow-sm" id="table_number" name="table_number" value="{{ $privateWing->table_number }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="annual_number" class="form-label fw-bold">الترقيم السنوي ({{ $privateWing->fiscal_year }}) - على مستوى المؤسسة</label>
                                        <input type="text" class="form-control shadow-sm" id="annual_number" name="annual_number" value="{{ $privateWing->annual_number }}" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="statistical_number" class="form-label fw-bold">الرقم الإحصائي</label>
                                        <input type="text" class="form-control shadow-sm" id="statistical_number" name="statistical_number" value="{{ $privateWing->statistical_number }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="entry_date" class="form-label fw-bold">تاريخ الدخول</label>
                                        <input type="date" class="form-control shadow-sm" id="entry_date" name="entry_date" value="{{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="exit_date" class="form-label fw-bold">تاريخ الخروج</label>
                                        <input type="date" class="form-control shadow-sm" id="exit_date" name="exit_date" value="{{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}">
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
                                                            @foreach(\App\Models\Doctor::all() as $doctor)
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
                                                                <th style="text-align: center;">احتساب الأيام</th>
                                                                <th style="text-align: center;">مشمول بالضمان</th>
                                                                <th style="text-align: center;">نسبة المريض %</th>
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
                                                                $patientAmount = $service->is_insured ? $totalFee * ($insurancePercentage / 100) : $totalFee;
                                                                $insuranceAmount = $service->is_insured ? $totalFee * ((100 - $insurancePercentage) / 100) : 0;
                                                            @endphp
                                                            <tr class="existing-service bg-light" data-service-id="{{ $service->id }}">
                                                                <td>{{ $service->service_name }}</td>
                                                                <td>{{ $service->doctor_name ?? '' }}</td>
                                                                <td>{{ $service->operation_degree ?? '' }}</td>
                                                                <td>{{ number_format($service->service_fee, 2) }} د.ع</td>
                                                                <td style="text-align: center;">{{ $service->is_daily ? 'نعم' : 'لا' }}</td>
                                                                <td style="text-align: center;">{{ $service->is_insured ? 'نعم' : 'لا' }}</td>
                                                                <td style="text-align: center;">{{ $insurancePercentage }}%</td>
                                                                <td id="existing-service-total-{{ $service->id }}">{{ number_format($totalFee, 2) }} د.ع</td>
                                                                <td>{{ number_format($patientAmount, 2) }} د.ع</td>
                                                                <td>{{ number_format($insuranceAmount, 2) }} د.ع</td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="لا يمكن حذف الخدمات الموجودة"><i class="fas fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="7">المجموع</th>
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

                        <!-- وصولات التأمين -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-success text-center">
                                <h4 class="mb-0"><i class="fas fa-receipt"></i> وصولات التأمين</h4>
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
                                        <label class="form-label fw-bold">إجمالي الخدمات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_services" value="{{ $privateWing->total_amount ?? 0 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الإيداعات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_deposits" value="{{ $privateWing->deposit_amount ?? 0 }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الفائدة</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_interest" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">المبلغ المتبقي</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="remaining_amount" value="{{ ($privateWing->deposit_amount ?? 0) - ($privateWing->total_amount ?? 0) }}" readonly>
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
$(document).ready(function() {
    // متغير لتتبع الوصولات المحذوفة - لم يعد مطلوباً
    // let deletedReceipts = [];

    // متغير لتتبع عدد الوصولات القديمة
    const oldReceiptsCount = {{ $privateWing->receipts->count() }};
    
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
    function updateTotalDeposit() {
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
        
        totalDepositsAmount = totalDeposits;
        $('#total_deposits').val(formatNumber(totalDeposits));
        $('#total_interest').val(formatNumber(totalInterest));
        updateRemaining();
    }

    // تحديث المبلغ المتبقي
    function updateRemaining() {
        const remaining = totalDepositsAmount - totalAmount;
        $('#remaining_amount').val(formatNumber(remaining));
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

    // تحديث الإجمالي عند تحميل الصفحة
    updateTotalDeposit();
});</script>
<script>
    $(document).ready(function() {
        // استرجاع البيانات المحفوظة أولاً
        loadFromLocalStorage();
        
        // تهيئة المتغيرات العامة
        window.servicesData = [];
        var totalAmount = 0;
        var totalDepositsAmount = 0;

        // دالة لحفظ البيانات في localStorage
        function saveToLocalStorage() {
            try {
                localStorage.setItem('privateWing_edit_services', JSON.stringify(window.servicesData));
                localStorage.setItem('privateWing_edit_isInsured', $('#is_insured').is(':checked') ? '1' : '0');
                localStorage.setItem('privateWing_edit_daysCount', $('#days_count').val());
            } catch (e) {
                console.error('خطأ في حفظ البيانات:', e);
            }
        }

        // دالة لاسترجاع البيانات من localStorage
        function loadFromLocalStorage() {
            try {
                var savedServices = localStorage.getItem('privateWing_edit_services');
                var savedIsInsured = localStorage.getItem('privateWing_edit_isInsured');
                var savedDaysCount = localStorage.getItem('privateWing_edit_daysCount');
                
                // استرجاع حالة الضمان
                if (savedIsInsured) {
                    $('#is_insured').prop('checked', savedIsInsured === '1');
                }
                
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
        function clearLocalStorage() {
            localStorage.removeItem('privateWing_edit_services');
            localStorage.removeItem('privateWing_edit_isInsured');
            localStorage.removeItem('privateWing_edit_daysCount');
            console.log('✓ تم مسح البيانات المحفوظة');
        }

        // دالة لتنسيق الأرقام
        function formatNumber(number) {
            return number.toFixed(2);
        }

        // دالة لتحديث المجموع الكلي
        function updateTotal() {
            var daysCount = parseInt($('#days_count').val()) || 1;
            
            totalAmount = 0;
            var totalPatientAmount = 0;
            var totalInsuranceAmount = 0;
            
            // حساب الخدمات الموجودة
            window.existingServices.forEach(function(service) {
                var serviceTotal = service.isDaily ?
                    parseFloat(service.fee) * daysCount :
                    parseFloat(service.fee);
                
                totalAmount += serviceTotal;
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
                var patientPercentage = service.insurancePercentage || 10;
                
                if (service.isInsured) {
                    totalPatientAmount += serviceTotal * (patientPercentage / 100);
                    totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
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
                
                if (service.isInsured) {
                    totalPatientAmount += serviceTotal * (patientPercentage / 100);
                    totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
                } else {
                    totalPatientAmount += serviceTotal;
                }
            });

            // تحديث عرض المجموع في الجدول - الإجمالي يعتمد على مبلغ المريض فقط
            $('#total_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_patient_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_insurance_amount').text(formatNumber(totalInsuranceAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalPatientAmount));
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
            // حذف الصفوف الجديدة فقط، والاحتفاظ بالصفوف الموجودة
            tbody.find('tr:not(.existing-service)').remove();

            var daysCount = parseInt($('#days_count').val()) || 1;
            var isPatientInsured = $('#is_insured').is(':checked');

            // تحديث الإجماليات للخدمات الموجودة
            tbody.find('.existing-service').each(function() {
                var serviceId = $(this).data('service-id');
                var existingService = window.existingServices.find(s => s.id === serviceId);
                if (existingService) {
                    var totalForService = existingService.isDaily ?
                        existingService.fee * daysCount :
                        existingService.fee;
                    $('#existing-service-total-' + serviceId).text(formatNumber(totalForService) + ' د.ع');
                }
            });

            // إضافة الخدمات الجديدة
            window.servicesData.forEach((service, index) => {
                var totalFee = service.isDaily ? service.fee * daysCount : service.fee;
                
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

        // معالجة تغيير التخصص
        $('#section_id').change(function() {
            var section_id = $(this).val();
            if (section_id) {
                $.ajax({
                    url: "{{ route('getSpecializations') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        section_id: section_id
                    },
                    success: function(data) {
                        $('#fckn').empty();
                        $('#fckn').append('<option value="" disabled selected>--حدد التخصص الفرعي--</option>');
                        $.each(data, function(key, value) {
                            $('#fckn').append('<option value="' + value.sercode + '">' + value.sername + '</option>');
                        });
                        $('#service_fee').val('');
                    },
                    error: function() {
                        alert('حدث خطأ أثناء جلب البيانات.');
                    }
                });
            } else {
                $('#fckn').empty();
                $('#fckn').append('<option value="" disabled selected>--حدد التخصص الفرعي--</option>');
                $('#service_fee').val('');
            }
        });

        // معالجة تغيير التخصص الفرعي
        $('#section_id, #fckn').change(function() {
            var section_id = $('#section_id').val();
            var fckn = $('#fckn').val();
            $('#service_fee').val('');

            if (section_id && fckn) {
                $.ajax({
                    url: "{{ route('getInstitutions') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        section_id: section_id,
                        fckn: fckn
                    },
                    success: function(data) {
                        $('#fckr').empty();
                        $('#fckr').append('<option value="" disabled selected>--حدد الخدمة--</option>');
                        $.each(data, function(key, value) {
                            $('#fckr').append('<option value="' + value.id + '">' + value.namesv + '</option>');
                        });
                    },
                    error: function() {
                        alert('حدث خطأ أثناء جلب البيانات.');
                    }
                });
            } else {
                $('#fckr').empty();
                $('#fckr').append('<option value="" disabled selected>--حدد الخدمة--</option>');
            }
        });

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
            
            // إضافة بيانات الخدمات الجديدة فقط إلى النموذج
            window.servicesData.forEach((service, index) => {
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
                    value: service.insurancePercentage || 10
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
            console.log('Loaded existing services:', window.existingServices);
        @else
            console.log('No existing services to load');
        @endif
        
        updateServicesTable();
        updateTotal();
        updateTotalDeposit();
        updateRemaining();
    });
</script>
@endsection
