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
    
    /* أنماط الطباعة */
    @media print {
        .no-print, 
        .card-header,
        .btn,
        button,
        .modal,
        .breadcrumb-header,
        th:last-child,
        td:last-child {
            display: none !important;
        }
        
        .table {
            font-size: 10pt;
        }
        
        .table th,
        .table td {
            padding: 5px;
        }
        
        body {
            direction: rtl;
        }
        
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    }
</style>
<style>
    select option {
        color: #000 !important;
        background-color: #fff !important;
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
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h2 class="mb-0">قائمة حسابات الأجنحة الخاصة</h2>
                    <div>
                        <button type="button" class="btn btn-info ms-2" data-toggle="modal" data-target="#searchModal" onclick="$('#searchModal').modal('show');">
                            <i class="fas fa-search"></i> بحث متقدم
                        </button>
                        <button type="button" class="btn btn-success ms-2" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <a href="{{ route('private-wings.export', request()->query()) }}" class="btn btn-primary ms-2">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectPatientTypeModal">
                            <i class="fas fa-plus"></i> إضافة جديد
                        </button>
                        <a href="{{ route('private-wings.trashed') }}" class="btn btn-warning ms-2">
                            <i class="fas fa-archive"></i> عرض المبطلات
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- عنوان الطباعة - يظهر فقط عند الطباعة -->
                    <div class="d-none" style="display: none;">
                        <style>
                            @media print {
                                .print-header {
                                    display: block !important;
                                    text-align: center;
                                    margin-bottom: 20px;
                                }
                                .print-header h2 {
                                    font-size: 20pt;
                                    font-weight: bold;
                                }
                                .print-header p {
                                    font-size: 12pt;
                                    margin: 5px 0;
                                }
                            }
                        </style>
                        <div class="print-header">
                            <h2>قائمة حسابات الأجنحة الخاصة</h2>
                            <p>تاريخ الطباعة: {{ date('Y-m-d H:i') }}</p>
                            @if(request()->hasAny(['patient_name', 'hospital', 'created_at_from', 'created_at_to', 'patient_type', 'annual_number', 'statistical_number', 'payment_status', 'patient_phone']))
                            <p>البحث المطبق: 
                                @if(request('patient_name')) اسم المريض: {{ request('patient_name') }} | @endif
                                @if(request('patient_phone')) الهاتف: {{ request('patient_phone') }} | @endif
                                @if(request('hospital')) المستشفى: {{ request('hospital') }} | @endif
                                @if(request('patient_type')) نوع المريض: {{ request('patient_type') == 'outpatient' ? 'غير راقد' : 'راقد' }} | @endif
                                @if(request('annual_number')) الترقيم السنوي: {{ request('annual_number') }} | @endif
                                @if(request('statistical_number')) الرقم الإحصائي: {{ request('statistical_number') }} | @endif
                                @if(request('payment_status')) حالة الدفع: {{ request('payment_status') == 'paid' ? 'مدفوع' : 'غير مدفوع' }} | @endif
                                @if(request('created_at_from')) من: {{ request('created_at_from') }} | @endif
                                @if(request('created_at_to')) إلى: {{ request('created_at_to') }} @endif
                            </p>
                            @endif
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success no-print">
                            {{ session('success') }}
                        </div>
                    @endif
                    @php
                        $debtWingsCount = $debtWingsCount ?? 0;
                    @endphp
                    @if($debtWingsCount > 0)
                    <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>تنبيه!</strong> يوجد {{ $debtWingsCount }} حساب جناح خاص يحتاج لتسديد المبالغ المستحقة.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="table-responsive">
                       <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='20' style="text-align: center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المريض</th>
                                    <th>رقم الهاتف</th>
                                    <th>جنسية المريض</th>
                                    <th>نوع المريض</th>
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
                                    // حساب المتبقي بدون مبلغ الفائدة (مطابق للطباعة)
                                    $totalDepositsWithoutInterest = $wing->receipts->sum('amount');
                                    $remaining = $totalDepositsWithoutInterest - $wing->total_amount;
                                    
                                    // خصم المبلغ المسترد من المتبقي
                                    $refundedAmount = $wing->refund_amount ?? 0;
                                    $remainingAfterRefund = $remaining - $refundedAmount;
                                    
                                    $hasDebt = $remainingAfterRefund < 0;
                                    $hasRefund = $remainingAfterRefund > 0;
                                    $hasRefundPayment = $refundedAmount > 0; // تم دفع استرداد
                                @endphp
                                <tr class="{{ $hasRefundPayment ? 'table-info' : ($hasDebt ? 'table-danger' : ($hasRefund ? 'table-success' : ($wing->cancelled_at ? 'table-warning' : ''))) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $wing->patient_name }}</td>
                                    <td>{{ $wing->patient_phone ?: '-' }}</td>
                                    <td>{{ $wing->patient_nationality ?: '-' }}</td>
                                    <td>
                                        @if(($wing->patient_type ?? 'inpatient') == 'outpatient')
                                            <span class="badge badge-success">غير راقد</span>
                                        @else
                                            <span class="badge badge-primary">راقد</span>
                                        @endif
                                    </td>
                                    <td>{{ $wing->hospital }}</td>
                                    <td><span class="badge badge-primary">سنوي: {{ $wing->annual_number }}/{{ $wing->fiscal_year }}</span></td>
                                    <td>
                                        {{ $wing->entry_date }}
                                        @if($wing->entry_time)
                                            <br><small>{{ $wing->entry_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $wing->exit_date }}
                                        @if($wing->exit_time)
                                            <br><small>{{ $wing->exit_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasRefundPayment)
                                            @if($remainingAfterRefund > 0)
                                                <span class="badge badge-success">تم استرداد {{ number_format($refundedAmount, 0) }} د.ع - الباقي له {{ number_format($remainingAfterRefund, 0) }} د.ع</span>
                                            @elseif($remainingAfterRefund < 0)
                                                <span class="badge badge-warning">تم استرداد {{ number_format($refundedAmount, 0) }} د.ع - عليه {{ number_format(abs($remainingAfterRefund), 0) }} د.ع</span>
                                            @else
                                                <span class="badge badge-info">تم استرداد {{ number_format($refundedAmount, 0) }} د.ع - مسدد بالكامل</span>
                                            @endif
                                        @elseif($hasDebt)
                                            <span class="badge badge-danger">يحتاج تسديد {{ number_format(abs($remainingAfterRefund), 0) }} د.ع</span>
                                        @elseif($hasRefund)
                                            <span class="badge badge-success">له مبلغ {{ number_format($remainingAfterRefund, 0) }} د.ع</span>
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

                    <div class="no-print">
                        {{ $privateWings->links() }}
                    </div>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('private-wings.index') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="patient_name" class="form-label">اسم المريض</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ request('patient_name') }}" placeholder="ابحث باسم المريض">
                        </div>
                        <div class="col-md-6">
                            <label for="patient_phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" id="patient_phone" name="patient_phone" value="{{ request('patient_phone') }}" placeholder="ابحث برقم الهاتف">
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
                        <div class="col-md-6">
                            <label for="patient_type" class="form-label">نوع المريض</label>
                            <select class="form-control" id="patient_type" name="patient_type">
                                <option value="">الكل</option>
                                <option value="inpatient" {{ request('patient_type') == 'inpatient' ? 'selected' : '' }}>راقد (إقامة كاملة)</option>
                                <option value="outpatient" {{ request('patient_type') == 'outpatient' ? 'selected' : '' }}>غير راقد (خدمة خارجية)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="annual_number" class="form-label">الترقيم السنوي</label>
                            <input type="text" class="form-control" id="annual_number" name="annual_number" value="{{ request('annual_number') }}" placeholder="ابحث بالترقيم السنوي">
                        </div>
                        <div class="col-md-6">
                            <label for="statistical_number" class="form-label">الرقم الإحصائي</label>
                            <input type="text" class="form-control" id="statistical_number" name="statistical_number" value="{{ request('statistical_number') }}" placeholder="ابحث بالرقم الإحصائي">
                        </div>
                        @if(in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name))
                        <div class="col-md-6">
                            <label for="moh_id" class="form-label">الدائرة</label>
                            <select class="form-control" id="moh_id" name="moh_id">
                                <option value="">الكل</option>
                                @foreach($availableMohs as $moh)
                                    <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name) || auth()->user()->roles_name === ["stat-doh"])
                        <div class="col-md-6">
                            <label for="fck_id" class="form-label">المستشفى</label>
                            <select class="form-control" id="fck_id" name="fck_id">
                                <option value="">الكل</option>
                                @foreach($availableFcks as $fck)
                                    <option value="{{ $fck->id }}" data-moh-id="{{ $fck->moh_id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
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
        // حساب المتبقي بدون مبلغ الفائدة (مطابق للطباعة)
        $totalDepositsWithoutInterest = $wing->receipts->sum('amount');
        $remaining = $totalDepositsWithoutInterest - $wing->total_amount;
        $refundedAmount = $wing->refund_amount ?? 0;
        $availableForRefund = $remaining - $refundedAmount;
        $hasRefund = $availableForRefund > 0;
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
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

<script>
    $(document).ready(function() {
        var $mohSelect = $('#moh_id');
        var $fckSelect = $('#fck_id');
        
        if ($mohSelect.length && $fckSelect.length) {
            var allOptions = $fckSelect.find('option').clone();
            
            function filterHospitals() {
                var selectedMoh = $mohSelect.val();
                var currentSelectedFck = $fckSelect.val();
                
                $fckSelect.empty();
                
                allOptions.each(function() {
                    var optMohId = $(this).attr('data-moh-id');
                    if (!$(this).val() || !selectedMoh || optMohId == selectedMoh) {
                        $fckSelect.append($(this).clone());
                    }
                });
                
                if (currentSelectedFck && $fckSelect.find('option[value="'+currentSelectedFck+'"]').length) {
                    $fckSelect.val(currentSelectedFck);
                } else if (currentSelectedFck) {
                    $fckSelect.val('');
                }
            }
            
            $mohSelect.on('change', filterHospitals);
            filterHospitals();
        }
    });
</script>
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