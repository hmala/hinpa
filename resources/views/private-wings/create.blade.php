@extends('layouts.master')
@section('title')
ألاسرة المهيئة 
@stop
@section('css')
<!-- Custom CSS -->
<link href="{{ URL::asset('assets/css/custom/                                                        <div class="input-group">
                                                            <span class="input-group-text">د.ع</span>
                                                            <input type="number" step="0.01" class="form-control shadow-sm" id="service_fee">
                                                            <button type="button" class="btn btn-primary" id="add_service">
                                                                <i class="fas fa-plus-circle"></i> إضافة
                                                            </button>ss') }}" rel="stylesheet" />
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
    /* تصغير حجم الخلايا في الجداول */
    #services_table th, #services_table td,
    #deposits_table th, #deposits_table td {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.2;
    }
    /* تصغير حجم الحقول في النموذج */
    .form-control, .form-select {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    /* تصغير حجم الأزرار */
    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-purple text-center">
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
                        <!-- تنبيه استرجاع البيانات -->
                        <div id="restore-notification" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <strong>تم استرجاع البيانات المحفوظة</strong> من الجلسة السابقة
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <!-- حقول مخفية للترقيمات -->
                        <input type="hidden" name="sequential_number" value="{{ $nextSequentialNumber }}">
                        <input type="hidden" name="annual_number" value="{{ $nextAnnualNumber }}">
                        <input type="hidden" name="fiscal_year" value="{{ $currentYear }}">
                        
                        <div class="card mb-4">                            <div class="card-header bg-gradient-blue text-center">
                                <h4 class="mb-0"><i class="fas fa-user-injured"></i> معلومات المريض</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 bg-light p-3 rounded border mx-1">
                                    <div class="col-md-12 text-center">
                                        <label class="form-label fw-bold d-block text-primary">مسار / نوع المريض</label>
                                        <input type="hidden" name="patient_type" id="patient_type" value="{{ request('patient_type', 'inpatient') }}">
                                        @if(request('patient_type') == 'outpatient')
                                            <span class="badge bg-success p-2 fs-6 text-white"><i class="fas fa-user-clock me-1"></i> مريض غير راقد (خدمة خارجية / عيادة)</span>
                                        @else
                                            <span class="badge bg-primary p-2 fs-6 text-white"><i class="fas fa-bed me-1"></i> مريض راقد (إقامة كاملة)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="hospital" class="form-label fw-bold">المستشفى</label>
                                        <input type="text" class="form-control shadow-sm" id="hospital" name="hospital" value="{{ Auth::user()->fck ? Auth::user()->fck->Fckname : '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="health_department" class="form-label fw-bold">دائرة صحة</label>
                                        <input type="text" class="form-control shadow-sm" id="health_department" name="health_department" value="{{ Auth::user()->mohcode ? \App\Models\mohs::find(Auth::user()->mohcode)->mohname : '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_name" class="form-label fw-bold">اسم المريض</label>
                                        <input type="text" class="form-control shadow-sm" id="patient_name" name="patient_name" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="table_number" class="form-label fw-bold">رقم الطبلة</label>
                                        <input type="text" class="form-control shadow-sm" id="table_number" name="table_number">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="annual_number" class="form-label fw-bold">الترقيم السنوي ({{ $currentYear }})</label>
                                        <input type="text" class="form-control shadow-sm" id="annual_number" name="annual_number" value="{{ $nextAnnualNumber }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="statistical_number" class="form-label fw-bold">الرقم الإحصائي</label>
                                        <input type="text" class="form-control shadow-sm" id="statistical_number" name="statistical_number" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="entry_date" class="form-label fw-bold">تاريخ الدخول</label>
                                        <input type="date" class="form-control shadow-sm" id="entry_date" name="entry_date" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="exit_date" class="form-label fw-bold">تاريخ الخروج</label>
                                        <input type="date" class="form-control shadow-sm" id="exit_date" name="exit_date">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="days_count" class="form-label fw-bold">عدد الأيام</label>
                                        <input type="number" class="form-control shadow-sm" id="days_count" name="days_count" value="1" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="list_date" class="form-label fw-bold">تاريخ تنظيم القائمة</label>
                                        <input type="date" class="form-control shadow-sm" id="list_date" name="created_at" required value="{{ date('Y-m-d') }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="patient_nationality" class="form-label fw-bold">جنسية المريض</label>
                                        <select class="form-control shadow-sm" id="patient_nationality" name="patient_nationality">
                                            <option value="">اختر الجنسية</option>
                                            <option value="عراقي">عراقي</option>
                                            <option value="عربي">عربي</option>
                                            <option value="أجنبي">أجنبي</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="is_insured" class="form-label fw-bold">مشمول بالضمان</label>
                                        <div style="text-align: center; margin-top: 0.5rem;">
                                            <input type="hidden" name="is_insured" value="0">
                                            <input class="form-check-input" type="checkbox" id="is_insured" name="is_insured" value="1" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>
                                </div>
                             
                            </div>
                        </div>

                        <!-- الرسوم والأجور والخدمات -->
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
                                                    <div class="col-md-12">                                                      
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
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
                                                            <div class="col-md-4 mb-3">
                                                                <label for="section_id" class="form-label fw-bold">التخصص</label>
                                                                <select name="section_id" id="section_id" class="form-control select2">
                                                                    <option value="">اختر التخصص...</option>
                                                                    @foreach($typeSpecializations as $typeSpecialization)
                                                                        <option value="{{ $typeSpecialization->id }}">{{ $typeSpecialization->tsname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="fckn" class="form-label fw-bold">التخصص الفرعي</label>
                                                                <select id="fckn" name="fckn" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد التخصص الفرعي--</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label for="fckr" class="form-label fw-bold">الخدمة</label>
                                                                <select id="fckr" name="fckr" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد الخدمة--</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="service_fee" class="form-label fw-bold">السعر</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">د.ع</span>
                                                                    <input type="number" step="0.01" class="form-control shadow-sm" id="service_fee" placeholder="أدخل السعر">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-bold">&nbsp;</label>
                                                                <button type="button" class="btn btn-primary w-100" id="add_service">
                                                                    <i class="fas fa-plus-circle"></i> إضافة
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <label for="doctor_id" class="form-label fw-bold">اسم الطبيب</label>
                                                        <select id="doctor_id" name="doctor_id" class="form-control select2" disabled>
                                                            <option value="">اختر الطبيب...</option>
                                                            @foreach($doctors as $doctor)
                                                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered" id="services_table">                                                        <thead class="bg-light">                                                            <tr>
                                                                <th>الخدمة</th>
                                                                <th>الطبيب</th>
                                                                <th>درجة العملية</th>
                                                                <th>التكلفة اليومية</th>
                                                                <th>احتساب الأيام</th>
                                                                <th>مشمول بالضمان</th>
                                                                <th>نسبة المريض %</th>
                                                                <th>الإجمالي</th>
                                                                <th>مبلغ المريض</th>
                                                                <th>مبلغ الضمان</th>
                                                                <th>العمليات</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
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

                        <!-- معلومات التأمينات -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-orange text-center">
                                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> وصولات التأمينات</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="deposits_table">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>المبلغ (د.ع)</th>
                                                <th>رقم الوصل</th>
                                                <th>تاريخ الوصل</th>
                                                <th>نوع الدفع</th>
                                                <th>النسبة (%)</th>
                                                <th>الفائدة (د.ع)</th>
                                                <th>الإجمالي (د.ع)</th>
                                                <th>العمليات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <div class="row g-2">
                                                        <div class="col-md-2">
                                                            <div class="input-group">
                                                                <span class="input-group-text">د.ع</span>
                                                                <input type="number" class="form-control" id="new_deposit_amount" placeholder="المبلغ" min="0" step="0.01">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control" id="new_deposit_number" placeholder="رقم الوصل">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="date" class="form-control" id="new_deposit_date">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-control" id="new_deposit_payment_type">
                                                                <option value="كاش" selected>كاش</option>
                                                                <option value="إلكتروني">إلكتروني</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <input type="number" class="form-control" id="new_deposit_interest_rate" placeholder="%" readonly>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <input type="number" class="form-control" id="new_deposit_interest_amount" placeholder="د.ع" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-success w-100" id="add_deposit_btn">
                                                                <i class="fas fa-plus-circle"></i> إضافة
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">إجمالي الخدمات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_services" readonly>
                                        </div>
                                    </div>
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
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg mx-2">
                                <i class="fas fa-save"></i> حفظ
                            </button>
                            <button type="button" class="btn btn-warning btn-lg mx-2" id="clear_form_btn">
                                <i class="fas fa-eraser"></i> مسح النموذج
                            </button>
                            <a href="{{ route('private-wings.index') }}" class="btn btn-secondary btn-lg mx-2">
                                <i class="fas fa-arrow-right"></i> رجوع
                            </a>
                        </div>
                    </form>                </div>
            </div>
        </div>
    </div>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>                    $(document).ready(function() {
                        // تفعيل الحفظ التلقائي للنموذج
                        FormAutoSave.init('form', {
                            excludeFields: ['_token'],
                            showRestoreMessage: true
                        });

                        // تحسين تحميل التخصصات مع معالجة ضعف الإنترنت
                        $('#section_id').change(function() {
                            var section_id = $('#section_id').val();
                            
                            if (section_id) {
                                loadSelectDataWithRetry(
                                    "{{ route('getSpecializations') }}",
                                    '#fckn',
                                    {
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            section_id: section_id
                                        },
                                        defaultText: '--حدد المؤسسة--',
                                        valueField: 'sercode',
                                        textField: 'sername',
                                        retries: 3,
                                        timeout: 10000,
                                        onSuccess: function() {
                                            // إعادة تعيين حقل الطبيب عند تغيير التخصص الرئيسي
                                            $('#doctor_id').prop('disabled', true).val('').trigger('change');
                                            $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
                                        },
                                        onError: function() {
                                            $('#fckn').empty();
                                            $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                                        }
                                    }
                                );
                            } else {
                                $('#fckn').empty();
                                $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                                // إعادة تعيين حقل الطبيب عند عدم اختيار تخصص رئيسي
                                $('#doctor_id').prop('disabled', true).val('').trigger('change');
                                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
                            }
                        });
                    });
                </script>
 <script>   
                  $(document).ready(function() {
                        $('#section_id, #fckn').change(function() {
                            var section_id = $('#section_id').val();
                            var fckn = $('#fckn').val();
                            $('#service_fee').val('');

                            if (section_id && fckn) {
                                loadSelectDataWithRetry(
                                    "{{ route('getInstitutions') }}",
                                    '#fckr',
                                    {
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            section_id: section_id,
                                            fckn: fckn
                                        },
                                        defaultText: '--حدد الخدمة--',
                                        valueField: 'id',
                                        textField: 'namesv',
                                        retries: 3,
                                        timeout: 10000,
                                        onSuccess: function() {
                                            // تحديث حالة حقل الطبيب عند تغيير التخصص الفرعي
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
                                // تعطيل حقل الطبيب عند عدم اختيار التخصص الفرعي
                                $('#doctor_id').prop('disabled', true).val('').trigger('change');
                                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
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
        
        // دالة للتحكم في تفعيل/تعطيل حقل الطبيب بناءً على التخصص الفرعي
        function toggleDoctorField() {
            var subSpecialization = $('#fckn option:selected').text();
            
            // التخصصات التي تتطلب اختيار طبيب
            var doctorRequiredSpecializations = [
                'اجور عمليات',
                'اجورالخدمات الطبية المقدمة للعرب والأجانب غير المقيمين',
                'اجور زيارات علاجية',
                'العيون',
                'تمريض وتداوي'
            ];
            
            if (doctorRequiredSpecializations.includes(subSpecialization)) {
                $('#doctor_id').prop('disabled', false);
                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب (مطلوب)');
            } else {
                $('#doctor_id').prop('disabled', true);
                $('#doctor_id').val('').trigger('change');
                $('#doctor_id').closest('.row').find('label').text('اسم الطبيب');
            }
        }

        // دالة لإضافة قيمة عددية
        function formatNumber(number) {
            return number.toLocaleString('ar-IQ', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // دالة لحفظ البيانات في localStorage
        function saveToLocalStorage() {
            try {
                // حفظ معلومات المريض
                localStorage.setItem('privateWing_patientName', $('#patient_name').val() || '');
                localStorage.setItem('privateWing_tableNumber', $('#table_number').val() || '');
                localStorage.setItem('privateWing_statisticalNumber', $('#statistical_number').val() || '');
                localStorage.setItem('privateWing_entryDate', $('#entry_date').val() || '');
                localStorage.setItem('privateWing_exitDate', $('#exit_date').val() || '');
                localStorage.setItem('privateWing_patientNationality', $('#patient_nationality').val() || '');
                localStorage.setItem('privateWing_isInsured', $('#is_insured').is(':checked') ? '1' : '0');
                localStorage.setItem('privateWing_daysCount', $('#days_count').val() || '1');
                
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
                var savedTableNumber = localStorage.getItem('privateWing_tableNumber');
                var savedStatisticalNumber = localStorage.getItem('privateWing_statisticalNumber');
                var savedEntryDate = localStorage.getItem('privateWing_entryDate');
                var savedExitDate = localStorage.getItem('privateWing_exitDate');
                var savedPatientNationality = localStorage.getItem('privateWing_patientNationality');
                var savedIsInsured = localStorage.getItem('privateWing_isInsured');
                var savedDaysCount = localStorage.getItem('privateWing_daysCount');
                
                if (savedPatientName) $('#patient_name').val(savedPatientName);
                if (savedTableNumber) $('#table_number').val(savedTableNumber);
                if (savedStatisticalNumber) $('#statistical_number').val(savedStatisticalNumber);
                if (savedEntryDate) $('#entry_date').val(savedEntryDate);
                if (savedExitDate) $('#exit_date').val(savedExitDate);
                if (savedPatientNationality) $('#patient_nationality').val(savedPatientNationality);
                if (savedIsInsured) $('#is_insured').prop('checked', savedIsInsured === '1');
                if (savedDaysCount) $('#days_count').val(savedDaysCount);
                
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
            localStorage.removeItem('privateWing_tableNumber');
            localStorage.removeItem('privateWing_statisticalNumber');
            localStorage.removeItem('privateWing_entryDate');
            localStorage.removeItem('privateWing_exitDate');
            localStorage.removeItem('privateWing_patientNationality');
            localStorage.removeItem('privateWing_services');
            localStorage.removeItem('privateWing_deposits');
            localStorage.removeItem('privateWing_isInsured');
            localStorage.removeItem('privateWing_daysCount');
            console.log('✓ تم مسح البيانات المحفوظة');
        }
        
        // دالة لجلب الترقيمات الجديدة من السيرفر
        function fetchNewNumbers() {
            $.ajax({
                url: '{{ route("private-wings.get-next-numbers") }}',
                type: 'GET',
                success: function(response) {
                    $('#annual_number').val(response.nextAnnualNumber);
                    $('#sequential_number').val(response.nextSequentialNumber);
                    $('input[name="annual_number"]').val(response.nextAnnualNumber);
                    $('input[name="sequential_number"]').val(response.nextSequentialNumber);
                    console.log('✓ تم تحديث الترقيمات:', response);
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
            $('#table_number').val('');
            $('#statistical_number').val('');
            $('#entry_date').val('');
            $('#exit_date').val('');
            $('#patient_nationality').val('');
            $('#is_insured').prop('checked', false);
            $('#days_count').val('1');
            
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
            var daysCount = parseInt($('#days_count').val()) || 1;
            totalAmount = 0;
            var totalPatientAmount = 0;
            var totalInsuranceAmount = 0;
            
            window.servicesData.forEach(function(service) {
                // إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var serviceTotal = service.isDaily ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                
                totalAmount += serviceTotal;
                
                // حساب مبالغ الضمان بناءً على النسبة المحددة
                var patientPercentage = service.insurancePercentage || 100;
                
                if (service.isInsured) {
                    totalPatientAmount += serviceTotal * (patientPercentage / 100);
                    totalInsuranceAmount += serviceTotal * ((100 - patientPercentage) / 100);
                } else {
                    totalPatientAmount += serviceTotal;
                }
            });
            
            // الإجمالي يعتمد على مبلغ المريض فقط
            $('#total_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_patient_amount').text(formatNumber(totalPatientAmount) + ' د.ع');
            $('#total_insurance_amount').text(formatNumber(totalInsuranceAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalPatientAmount) + ' د.ع');
            
            // حساب المبلغ المتبقي - بناءً على المبلغ الذي يدفعه المريض فقط
            var depositAmount = parseFloat($('#deposit_amount').val()) || 0;
            var remainingAmount = totalPatientAmount - depositAmount;
            $('#remaining_amount').val(formatNumber(remainingAmount) + ' د.ع');
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
            var daysCount = parseInt($('#days_count').val()) || 1;
            var isPatientInsured = $('#is_insured').is(':checked');
            
            window.servicesData.forEach((service, index) => {
                // حساب الإجمالي - إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var totalFee = service.isDaily ? service.fee * daysCount : service.fee;
                
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

        // دالة لحساب الفرق بين التواريخ
        function calculateDays() {
            var entryDate = $('#entry_date').val();
            var exitDate = $('#exit_date').val();
            
            if (entryDate && exitDate) {
                var entry = new Date(entryDate);
                var exit = new Date(exitDate);
                
                // حساب الفرق بالأيام
                var diffTime = Math.abs(exit - entry);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                // التحقق من أن تاريخ الخروج لا يسبق تاريخ الدخول
                if (exit < entry) {
                    alert('تاريخ الخروج لا يمكن أن يكون قبل تاريخ الدخول');
                    $('#exit_date').val('');
                    $('#days_count').val(1);
                } else {
                    $('#days_count').val(diffDays);
                }
            }
        }

        // تفعيل حساب الأيام عند تغيير أي من التاريخين
        $('#entry_date, #exit_date').change(function() {
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
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
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

            // التحقق من اختيار الطبيب إذا كان الحقل مفعلاً
            var doctorRequiredSpecializations = [
                'اجور عمليات',
                'اجورالخدمات الطبية المقدمة للعرب والأجانب غير المقيمين',
                'اجور زيارات علاجية',
                'العيون',
                'تمريض وتداوي'
            ];
            
            if (doctorRequiredSpecializations.includes(subSpecialization) && !doctorId) {
                alert('الرجاء اختيار الطبيب لهذا التخصص');
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

        // حفظ تلقائي عند تغيير معلومات المريض
        $('#patient_name, #table_number, #statistical_number, #entry_date, #exit_date, #patient_nationality').on('input change', function() {
            saveToLocalStorage();
        });

        // حفظ البيانات قبل مغادرة الصفحة أو إعادة تحميلها
        $(window).on('beforeunload', function() {
            saveToLocalStorage();
        });

        // حساب الفائدة تلقائياً
        function calculateInterest() {
            var amount = parseFloat($('#new_deposit_amount').val()) || 0;
            var paymentType = $('#new_deposit_payment_type').val();
            var interestRate = paymentType === 'كاش' ? 5.00 : 0.00;
            var interestAmount = (amount * interestRate) / 100;

            $('#new_deposit_interest_rate').val(interestRate.toFixed(2));
            $('#new_deposit_interest_amount').val(interestAmount.toFixed(2));
        }

        // ربط الأحداث لحساب الفائدة
        $('#new_deposit_amount').on('input', function() {
            calculateInterest();
        });
        $('#new_deposit_payment_type').on('change', function() {
            calculateInterest();
        });

        // تهيئة حساب الفائدة عند تحميل الصفحة
        $(document).ready(function() {
            calculateInterest();
        });

        function updateDepositsTable() {
            var tbody = $('#deposits_table tbody');
            tbody.empty();

            window.deposits.forEach((deposit, idx) => {
                tbody.append(`
                    <tr>
                        <td>${formatNumber(deposit.amount)} د.ع</td>
                        <td>${deposit.number}</td>
                        <td>${deposit.date}</td>
                        <td>${deposit.payment_type}</td>
                        <td>${deposit.interest_rate}%</td>
                        <td>${formatNumber(deposit.interest_amount)} د.ع</td>
                        <td>${formatNumber(deposit.amount + deposit.interest_amount)} د.ع</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-deposit" data-index="${idx}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            // تحديث المبلغ المتبقي
            updateRemaining();
        }

        function updateRemaining() {
            // حساب إجمالي مبالغ الوصولات فقط (بدون الفائدة)
            var totalDeposits = window.deposits.reduce((sum, deposit) => sum + parseFloat(deposit.amount), 0);
            
            // حساب إجمالي الفائدة بشكل منفصل
            var totalInterest = window.deposits.reduce((sum, deposit) => sum + parseFloat(deposit.interest_amount), 0);
            
            var totalServices = window.servicesData.reduce((sum, service) => {
                var daysCount = parseInt($('#days_count').val()) || 1;
                var serviceTotal = service.isDaily ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                return sum + serviceTotal;
            }, 0);

            var remainingAmount = totalServices - totalDeposits;
            $('#remaining_amount').val(formatNumber(remainingAmount) + ' د.ع');
            $('#total_deposits').val(formatNumber(totalDeposits) + ' د.ع');
            $('#total_interest').val(formatNumber(totalInterest) + ' د.ع');
        }

        // إضافة وصل جديد
        $('#add_deposit_btn').click(function() {
            var amount = parseFloat($('#new_deposit_amount').val());
            var number = $('#new_deposit_number').val();
            var date = $('#new_deposit_date').val();
            var paymentType = $('#new_deposit_payment_type').val();
            var interestRate = paymentType === 'كاش' ? 5.00 : 0.00;
            var interestAmount = (amount * interestRate) / 100;

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
                interest_rate: interestRate,
                interest_amount: interestAmount
            });

            // تحديث الجدول والمجاميع
            updateDepositsTable();
            saveToLocalStorage();

            // تنظيف الحقول
            $('#new_deposit_amount').val('');
            $('#new_deposit_number').val('');
            $('#new_deposit_date').val('');
            $('#new_deposit_interest_rate').val('');
            $('#new_deposit_interest_amount').val('');

            // تحديث الجدول
            updateDepositsTable();
        });

        // حذف وصل
        $(document).on('click', '.delete-deposit', function() {
            var index = $(this).data('index');
            window.deposits.splice(index, 1);
            updateDepositsTable();
            saveToLocalStorage();
        });

        // معالجة إرسال النموذج
        $('form').on('submit', function(e) {
            e.preventDefault();
            
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
            
            if (!window.servicesData || window.servicesData.length === 0) {
                alert('الرجاء إضافة خدمة واحدة على الأقل');
                return;
            }

            if (!window.deposits || window.deposits.length === 0) {
                alert('الرجاء إضافة وصل تأمين واحد على الأقل');
                return;
            }
            
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
</div>

 


@endsection

@section('js')
<!-- jQuery must be loaded first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Internal Select2 js-->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
<!-- Form autosave and utilities -->
<script src="{{ URL::asset('assets/js/form-autosave.js') }}"></script>
@endsection