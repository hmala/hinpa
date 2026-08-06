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
<!-- QuaggaJS CSS -->
<style>
    #interactive.viewport {
        position: relative;
        width: 100%;
        height: 400px;
        overflow: hidden;
        border: 1px solid #ccc;
    }
    #interactive.viewport > canvas, #interactive.viewport > video {
        max-width: 100%;
        width: 100%;
    }
    canvas.drawing, canvas.drawingBuffer {
        position: absolute;
        left: 0;
        top: 0;
    }
</style>
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">قائمة حسابات الأجنحة الخاصة
            </span>
        </div>
    </div>

</div>
<!-- breadcrumb -->
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">قائمة حسابات الأجنحة الخاصة</h2>
                    <div>
                        <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#searchModal" onclick="$('#searchModal').modal('show');">
                            <i class="fas fa-search"></i> بحث متقدم
                        </button>
                        <button type="button" class="btn btn-success ms-2" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <a href="{{ route('private-wings.export', request()->query()) }}" class="btn btn-primary ms-2">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#selectPatientTypeModal">
                            <i class="fas fa-plus"></i> إضافة جديد
                        </button>
                        <a href="{{ route('private-wings.trashed') }}" class="btn btn-warning ms-2">
                            <i class="fas fa-archive"></i> عرض المبطلات
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @php
                        $debtWingsCount = $debtWingsCount ?? 0;
                    @endphp
                    @if($debtWingsCount > 0)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>تنبيه!</strong> يوجد {{ $debtWingsCount }} حساب جناح خاص يحتاج لتسديد المبالغ المستحقة.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                       <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='20' style="text-align: center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المريض</th>
                                    <th>جنسية المريض</th>
                                    <th>المستشفى</th>
                                    <th>الترقيم السنوي</th>
                                   <th>تاريخ الدخول</th>
                                    <th>تاريخ الخروج</th>
                                    <th>حالة الدفع</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($privateWings as $wing)
                                @php
                                    $remaining = $wing->deposit_amount - $wing->total_amount;
                                    $hasDebt = $remaining < 0;
                                    $hasRefund = $remaining > 0;
                                    $hasRefundPayment = ($wing->refund_amount ?? 0) > 0; // تم دفع استرداد
                                @endphp
                                <tr class="{{ $hasRefundPayment ? 'table-info' : ($hasDebt ? 'table-danger' : ($hasRefund ? 'table-success' : ($wing->cancelled_at ? 'table-warning' : ''))) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $wing->patient_name }}</td>
                                    <td>{{ $wing->patient_nationality ?: '-' }}</td>
                                    <td>{{ $wing->hospital }}</td>
                                    <td><span class="badge badge-primary">سنوي: {{ $wing->annual_number }}/{{ $wing->fiscal_year }}</span></td>
                                    <td>{{ $wing->entry_date }}</td>
                                    <td>{{ $wing->exit_date }}</td>
                                    <td>
                                        @if($hasRefundPayment)
                                            <span class="badge badge-info">تم استرداد {{ number_format($wing->refund_amount, 0) }} د.ع</span>
                                        @elseif($hasDebt)
                                            <span class="badge badge-danger">يحتاج تسديد {{ number_format(abs($remaining), 0) }} د.ع</span>
                                        @elseif($hasRefund)
                                            <span class="badge badge-success">له مبلغ {{ number_format($remaining, 0) }} د.ع</span>
                                        @else
                                            <span class="badge badge-info">مدفوع بالكامل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($wing->cancelled_at)
                                            <span class="badge badge-warning" title="{{ $wing->cancellation_reason }}">ملغى</span>
                                        @else
                                            <span class="badge badge-success">نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('private-wings.show', $wing) }}" class="btn btn-info btn-sm">عرض</a>
                                        @if(!$wing->cancelled_at)
                                            <a href="{{ route('private-wings.edit', $wing) }}" class="btn btn-primary btn-sm">تعديل</a>
                                            @if($hasRefund && ($wing->refund_amount ?? 0) > 0)
                                                <a href="{{ route('private-wings.refund-receipt', $wing->id) }}" class="btn btn-success btn-sm" target="_blank">
                                                    <i class="fas fa-receipt"></i> وصل
                                                </a>
                                            @elseif($hasRefund)
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#refundModal{{ $wing->id }}">
                                                    <i class="fas fa-hand-holding-usd"></i> استرداد
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-warning btn-sm" onclick="openCancelModal({{ $wing->id }}, '{{ $wing->patient_name }}')">إبطال</button>
                                        @else
                                            <span class="text-muted">مبطل</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $privateWings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للبحث المتقدم -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">البحث المتقدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('private-wings.index') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="patient_name" class="form-label">اسم المريض</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ request('patient_name') }}" placeholder="ابحث باسم المريض">
                        </div>
                        <div class="col-md-6">
                            <label for="hospital" class="form-label">المستشفى</label>
                            <input type="text" class="form-control" id="hospital" name="hospital" value="{{ request('hospital') }}" placeholder="ابحث بالمستشفى">
                        </div>
                        <div class="col-md-6">
                            <label for="created_at_from" class="form-label">تاريخ تنظيم الاستمارة من</label>
                            <input type="date" class="form-control" id="created_at_from" name="created_at_from" value="{{ request('created_at_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="created_at_to" class="form-label">تاريخ تنظيم الاستمارة إلى</label>
                            <input type="date" class="form-control" id="created_at_to" name="created_at_to" value="{{ request('created_at_to') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="payment_status" class="form-label">حالة الدفع</label>
                            <select class="form-control" id="payment_status" name="payment_status">
                                <option value="">الكل</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <a href="{{ route('private-wings.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
                    <button type="submit" class="btn btn-primary">بحث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals للاسترداد -->
@foreach($privateWings as $wing)
    @php
        $remaining = $wing->deposit_amount - $wing->total_amount;
        $hasRefund = $remaining > 0;
        $availableForRefund = $remaining - ($wing->refund_amount ?? 0);
    @endphp
    
    @if($hasRefund && $availableForRefund > 0 && !$wing->cancelled_at)
    <div class="modal fade" id="refundModal{{ $wing->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('private-wings.add-refund', $wing->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة وصل استرداد - {{ $wing->patient_name }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <strong>المبلغ المتاح للاسترداد:</strong> {{ number_format($availableForRefund, 0) }} د.ع
                        </div>
                        <div class="form-group">
                            <label>المبلغ المسترد (د.ع) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required min="0" 
                                   max="{{ $availableForRefund }}" step="0.01" value="{{ $availableForRefund }}">
                        </div>
                        <div class="form-group">
                            <label>رقم وصل الاسترداد <span class="text-danger">*</span></label>
                            <input type="text" name="receipt_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>تاريخ الاسترداد <span class="text-danger">*</span></label>
                            <input type="date" name="refund_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>اسم المستلم <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control" required value="{{ $wing->patient_name }}">
                        </div>
                        <div class="form-group">
                            <label>رقم هوية المستلم</label>
                            <input type="text" name="receiver_id_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ وطباعة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!-- Modal لإبطال القائمة -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">إبطال قائمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancellation_reason">سبب الإبطال <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required placeholder="يرجى إدخال سبب الإبطال..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>تنبيه:</strong> بعد الإبطال لن يمكن تعديل أو حذف القائمة إلا من قبل المدير.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">إبطال القائمة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal اختيار نوع المريض عند الإضافة -->
<div class="modal fade" id="selectPatientTypeModal" tabindex="-1" role="dialog" aria-labelledby="selectPatientTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-purple text-white">
                <h5 class="modal-title text-white mb-0" id="selectPatientTypeModalLabel"><i class="fas fa-user-plus me-2"></i> اختيار مسار التسجيل</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted fw-bold mb-4">يرجى تحديد نوع مسار المريض للمتابعة:</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('private-wings.create', ['patient_type' => 'inpatient', 'clear' => 1]) }}" class="card text-decoration-none border-primary h-100 shadow-sm hover-card">
                            <div class="card-body p-4 text-center">
                                <div class="mb-3 text-primary">
                                    <i class="fas fa-bed fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-primary mb-2">مريض راقد</h5>
                                <p class="small text-muted mb-0">إقامة كاملة في الأجنحة مع أجور السرير والتغذية</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('private-wings.create', ['patient_type' => 'outpatient', 'clear' => 1]) }}" class="card text-decoration-none border-success h-100 shadow-sm hover-card">
                            <div class="card-body p-4 text-center">
                                <div class="mb-3 text-success">
                                    <i class="fas fa-user-clock fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-success mb-2">مريض غير راقد / زيارة</h5>
                                <p class="small text-muted mb-0">خدمة خارجية / عيادة / فحوصات مباشرة دون إقامة</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
</style>
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
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
<!-- QuaggaJS for barcode scanning -->
<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>

<script>
function openCancelModal(wingId, patientName) {
    $('#cancelModalLabel').text('إبطال قائمة - ' + patientName);
    $('#cancelForm').attr('action', '{{ url("private-wings/cancel") }}/' + wingId);
    $('#cancellation_reason').val('');
    $('#cancelModal').modal('show');
}

// إعادة تحميل الصفحة بعد الإبطال الناجح
@if(session('success'))
    $(document).ready(function() {
        // إعادة تحميل الصفحة بعد ثانيتين لإظهار التحديث
        setTimeout(function() {
            location.reload();
        }, 2000);
    });
@endif

// Barcode scanning functionality
$('#scan-barcode').on('click', function() {
    // Create a modal for camera
    var modalHtml = `
        <div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">مسح الباركود</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="interactive" class="viewport"></div>
                        <div id="result">يرجى توجيه الكاميرا نحو الباركود</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('body').append(modalHtml);
    $('#barcodeModal').modal('show');

    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#interactive'),
            constraints: {
                width: 640,
                height: 480,
                facingMode: "environment"
            },
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        numOfWorkers: 2,
        decoder: {
            readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "upc_reader", "upc_e_reader"]
        },
        locate: true
    }, function(err) {
        if (err) {
            console.log(err);
            return;
        }
        Quagga.start();
    });

    Quagga.onDetected(function(result) {
        var code = result.codeResult.code;
        $('#sequential_number').val(code);
        $('#barcodeModal').modal('hide');
        Quagga.stop();
        $('#barcodeModal').remove();
    });

    $('#barcodeModal').on('hidden.bs.modal', function() {
        Quagga.stop();
        $('#barcodeModal').remove();
    });
});

// إغلاق modal البحث عند الإرسال
$('#searchModal form').on('submit', function() {
    $('#searchModal').modal('hide');
});
</script>

@endsection