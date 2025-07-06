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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">                <div class="card-header">
                    <div class="text-center mb-3">
                        <h2 class="mb-2">جمهورية العراق</h2>
                        <h3 class="mb-2">وزارة الصحة</h3>
                        <h3 class="mb-3">دائرة صحة بغداد الرصافة</h3>
                        <h2 class="invoice-title mb-4">تعديل حساب الجناح الخاص</h2>
                        <div class="invoice-number mt-3">
                            <span>رقم الوصل: {{ $privateWing->receipt_number }}</span>
                            <span class="mx-4">|</span>
                            <span>التاريخ: {{ $privateWing->receipt_date }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('private-wings.update', $privateWing) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="hospital">المستشفى</label>
                                <input type="text" class="form-control" id="hospital" name="hospital" value="{{ $privateWing->hospital }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="health_department">دائرة صحة</label>
                                <input type="text" class="form-control" id="health_department" name="health_department" value="{{ $privateWing->health_department }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="patient_name">اسم المريض</label>
                                <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ $privateWing->patient_name }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="file_number">رقم الملف</label>
                                <input type="text" class="form-control" id="file_number" name="file_number" value="{{ $privateWing->file_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statistical_number">الرقم الإحصائي</label>
                                <input type="text" class="form-control" id="statistical_number" name="statistical_number" value="{{ $privateWing->statistical_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="days_count">عدد الأيام</label>
                                <input type="number" class="form-control" id="days_count" name="days_count" value="{{ $privateWing->days_count }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="entry_date">تاريخ الدخول</label>                                <input type="date" class="form-control" id="entry_date" name="entry_date" value="{{ $privateWing->entry_date ? $privateWing->entry_date->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exit_date">تاريخ الخروج</label>
                                <input type="date" class="form-control" id="exit_date" name="exit_date" value="{{ $privateWing->exit_date ? $privateWing->exit_date->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                     
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="deposit_amount">مبلغ التأمينات</label>
                                <input type="number" step="0.01" class="form-control" id="deposit_amount" name="deposit_amount" value="{{ $privateWing->deposit_amount }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="receipt_number">رقم الوصل</label>
                                <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="{{ $privateWing->receipt_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="receipt_date">تاريخ الوصل</label>                                <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="{{ $privateWing->receipt_date ? $privateWing->receipt_date->format('Y-m-d') : '' }}" required>
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
                                                                <th colspan="2"></th>
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
        // تهيئة المتغيرات العامة
        window.servicesData = [];
        var totalAmount = 0;

        // دالة لتنسيق الأرقام
        function formatNumber(number) {
            return number.toLocaleString('ar-IQ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // دالة لتحديث المجموع الكلي
        function updateTotal() {
            var daysCount = parseInt($('#days_count').val()) || 1;
            totalAmount = window.servicesData.reduce((sum, service) => {
                var serviceTotal = service.isDaily ?
                    parseFloat(service.fee) * daysCount :
                    parseFloat(service.fee);
                return sum + serviceTotal;
            }, 0);

            // تحديث عرض المجموع في الجدول
            $('#total_amount').text(formatNumber(totalAmount) + ' د.ع');
            $('#total_services').val(formatNumber(totalAmount));
        }

        // دالة لتحديث جدول الخدمات
        function updateServicesTable() {
            var tbody = $('#services_table tbody');
            tbody.empty();
            var daysCount = parseInt($('#days_count').val()) || 1;

            window.servicesData.forEach((service, index) => {
                var row = $('<tr>');
                row.append($('<td>').text(service.name));
                row.append($('<td>').text(formatNumber(service.fee) + ' د.ع'));

                var totalForService = service.isDaily ?
                    service.fee * daysCount :
                    service.fee;
                row.append($('<td>').text(formatNumber(totalForService) + ' د.ع'));

                row.append($('<td>').text(service.isDaily ? 'نعم' : 'لا'));

                var deleteButton = $('<button>')
                    .addClass('btn btn-danger btn-sm')
                    .html('<i class="fas fa-trash"></i>')
                    .on('click', () => {
                        window.servicesData.splice(index, 1);
                        updateServicesTable();
                        updateTotal();
                    });

                row.append($('<td>').addClass('text-center').append(deleteButton));
                tbody.append(row);
            });
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
                            console.warn('Invalid price data received:', data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching price:', textStatus, errorThrown);
                        $('#service_fee').val('0');
                        alert('حدث خطأ أثناء جلب السعر.');
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

            if (!serviceId || !serviceName || isNaN(serviceFee)) {
                alert('الرجاء اختيار الخدمة والتأكد من السعر.');
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

        // تحديث المجاميع عند تغيير عدد الأيام
        $('#days_count').on('input', function() {
            updateServicesTable();
            updateTotal();
        });

        // معالجة إرسال النموذج
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // التحقق من وجود خدمات
            if (window.servicesData.length === 0) {
                alert('الرجاء إضافة خدمة واحدة على الأقل');
                return;
            }
            
            // إضافة بيانات الخدمات إلى النموذج
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
            });
            
            this.submit();
        });

        // Load existing services on page load
        @if(isset($privateWing->services))
            @foreach($privateWing->services as $service)
                window.servicesData.push({
                    id: {{ $service->id }},
                    name: '{{ $service->namesv }}',
                    fee: {{ $service->pivot->service_fee }},
                    isDaily: {{ $service->pivot->is_daily ? 'true' : 'false' }}
                });
            @endforeach
            updateServicesTable();
            updateTotal();
        @endif
    });
</script>
@endsection
