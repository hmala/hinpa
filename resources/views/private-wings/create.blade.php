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
@section('script')
<!-- Internal Select2 js-->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
           

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
                                    <div class="col-md-4 mb-3">                                        <label for="hospital" class="form-label fw-bold">المستشفى</label>
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
                                                    <div class="col-md-8">                                                      
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">                                       
                                                                <select name="section_id" id="section_id" class="form-control select2">
                                                                    <option value="">اختر التخصص...</option>
                                                                    @foreach($typeSpecializations as $typeSpecialization)
                                                                        <option value="{{ $typeSpecialization->id }}">{{ $typeSpecialization->tsname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">   
                                                                <select id="fckn" name="fckn" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد التخصص الفرعي--</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">    
                                                                <select id="fckr" name="fckr" class="form-control select2">
                                                                    <option value="" disabled selected>--حدد الخدمة--</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text">د.ع</span>
                                                            <input type="number" step="0.01" class="form-control shadow-sm" id="service_fee" readonly>
                                                            <button type="button" class="btn btn-primary" id="add_service">
                                                                <i class="fas fa-plus-circle"></i> إضافة
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered" id="services_table">                                                        <thead class="bg-light">                                                            <tr>
                                                                <th>الخدمة</th>
                                                                <th>التكلفة اليومية</th>
                                                                <th>الإجمالي</th>
                                                                <th>احتساب الأيام</th>
                                                                <th>العمليات</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>المجموع</th>
                                                                <th colspan="2" id="total_amount">0 د.ع</th>
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
                        <div class="card mb-4">                            <div class="card-header bg-gradient-orange text-center">
                                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> معلومات التأمينات</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">                                        <label for="deposit_amount" class="form-label fw-bold">مبلغ التأمينات</label>
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
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">إجمالي الخدمات</label>
                                        <div class="input-group">
                                            <span class="input-group-text">د.ع</span>
                                            <input type="text" class="form-control shadow-sm" id="total_services" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
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
                        $('#section_id').change(function() {
                            var section_id = $('#section_id').val();
                            
                            if (section_id) {
                                $.ajax({
                                    url: "{{ route('getSpecializations') }}",
                                    type: "POST",
                                       data: {
                                        _token: '{{ csrf_token() }}',
                                        section_id: section_id,
                                        
                                    },
                                    success: function(data) {
                                        $('#fckn').empty();
                                        $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
                                        $.each(data, function(key, value) {
                                            $('#fckn').append('<option value="' + value.sercode + '">' + value.sername + '</option>');
                                        });
                                    },
                                    error: function() {
                                        alert('حدث خطأ أثناء جلب البيانات.');
                                    }
                                });
                            } else {
                                $('#fckn').empty();
                                $('#fckn').append('<option value="" disabled selected>--حدد المؤسسة--</option>');
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
                    });
                </script>
<script>
    $(document).ready(function() {
        // دالة لإضافة قيمة عددية
        function formatNumber(number) {
            return number.toLocaleString('ar-IQ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }        // تهيئة المتغيرات العامة
        window.servicesData = [];
        var totalAmount = 0;        // دالة لحساب المجموع الكلي
        function updateTotal() {
            var daysCount = parseInt($('#days_count').val()) || 1;            totalAmount = window.servicesData.reduce((sum, service) => {
                // إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var serviceTotal = service.isDaily ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                return sum + serviceTotal;
            }, 0);
            $('#total_amount').text(formatNumber(totalAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalAmount) + ' د.ع');
            
            // حساب المبلغ المتبقي
            var depositAmount = parseFloat($('#deposit_amount').val()) || 0;
            var remainingAmount = totalAmount - depositAmount;
            $('#remaining_amount').val(formatNumber(remainingAmount) + ' د.ع');
        }

        // دالة لتحديث جدول الخدمات
        function updateServicesTable() {
            var tbody = $('#services_table tbody');
            tbody.empty();            var daysCount = parseInt($('#days_count').val()) || 1;
            window.servicesData.forEach((service, index) => {
                // حساب الإجمالي - إذا كانت الخدمة تحتسب بالأيام نضرب في عدد الأيام
                var totalFee = service.isDaily ? service.fee * daysCount : service.fee;                tbody.append(`
                    <tr>
                        <td>${service.name}</td>
                        <td>${formatNumber(service.fee)} د.ع</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input multiply-days" type="checkbox" 
                                    ${service.isDaily ? 'checked' : ''} 
                                    data-index="${index}">
                            </div>
                        </td>
                        <td>${formatNumber(totalFee)} د.ع</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-service" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
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
                    $('#days_count').val('');
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
                        $('#service_fee').val(data.price);
                    },
                    error: function() {
                        alert('حدث خطأ أثناء جلب سعر الخدمة');
                        $('#service_fee').val('');
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

            if (!serviceId || !serviceName || isNaN(serviceFee)) {
                alert('الرجاء اختيار الخدمة وتحديد السعر');
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
                specialization: specialization,
                subSpecialization: subSpecialization
            });

            // تحديث الجدول والمجاميع
            updateServicesTable();
            updateTotal();

            // إعادة تعيين الحقول
            $('#section_id').val('').trigger('change');
            $('#fckn').val('').trigger('change');
            $('#fckr').val('').trigger('change');
            $('#service_fee').val('');
        });

        // معالجة حدث النقر على زر الحذف
        $(document).on('click', '.delete-service', function() {
            var index = $(this).data('index');
            window.servicesData.splice(index, 1);
            updateServicesTable();
            updateTotal();
        });

        // تحديث الجدول عند تغيير عدد الأيام
        $('#days_count').on('change', function() {
            updateServicesTable();
            updateTotal();
        });        // معالجة تغيير خانة احتساب الأيام
        $(document).on('change', '.multiply-days', function() {
            var index = $(this).data('index');
            window.servicesData[index].isDaily = $(this).prop('checked');
            updateServicesTable();
            updateTotal();
        });

        // تحديث المبلغ المتبقي عند تغيير مبلغ التأمينات
        $('#deposit_amount').on('input', function() {
            var depositAmount = parseFloat($(this).val()) || 0;
            var totalServices = window.servicesData.reduce((sum, service) => {
                var daysCount = parseInt($('#days_count').val()) || 1;
                var serviceTotal = service.isDaily ? 
                    parseFloat(service.fee) * daysCount : 
                    parseFloat(service.fee);
                return sum + serviceTotal;
            }, 0);
            var remainingAmount = totalServices - depositAmount;
            $('#remaining_amount').val(formatNumber(remainingAmount) + ' د.ع');
        });

        // معالجة إرسال النموذج
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // إظهار البيانات قبل الإرسال للتأكد من صحتها
            console.log('Services data before submit:', window.servicesData);
            
            if (window.servicesData.length === 0) {
                alert('الرجاء إضافة خدمة واحدة على الأقل');
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
            });
            
            // طباعة جميع البيانات المرسلة
            console.log('Form data:', $(this).serialize());
            
            // إرسال النموذج
            this.submit();
        });
    });
</script>
</div>

 


@endsection
