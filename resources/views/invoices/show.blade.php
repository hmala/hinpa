@extends('layouts.master')
@section('title')
تفاصيل الفاتورة
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تفاصيل الفاتورة
            </span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="pr-1 mb-3 mb-xl-0">
            <a href="{{ route('invoices.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- معلومات الفاتورة الأساسية -->
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">معلومات الفاتورة</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>رقم القائمة:</strong>
                            <p>{{ $invoice->receipt_number ?? $invoice->id }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>اسم المريض:</strong>
                            <p>{{ $invoice->patient_name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>تاريخ تنظيم القائمة:</strong>
                            <p>{{ $invoice->receipt_date ? $invoice->receipt_date->format('Y-m-d') : ($invoice->entry_date ? $invoice->entry_date->format('Y-m-d') : '-') }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>إجمالي الخدمات:</strong>
                            <p class="text-primary font-weight-bold">{{ number_format($invoice->total_amount, 2) }} دينار</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إشعار المدفوعات المستحقة -->
            @php
                $remainingAmount = $invoice->deposit_amount - $invoice->total_amount;
            @endphp
            @if($remainingAmount < 0)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>تنبيه!</strong> المريض {{ $invoice->patient_name ?? 'غير محدد' }} بحاجة لتسديد مبلغ <strong>{{ number_format(abs($remainingAmount), 2) }} دينار</strong> لتغطية أجر الخدمات المقدمة.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- الوصولات -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">الوصولات المدفوعة</h4>
                </div>
                <div class="card-body">
                    @if($invoice->receipts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" dir="rtl" style="text-align: center; font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>رقم الوصل</th>
                                    <th>تاريخ الدفع</th>
                                    <th>المبلغ المدفوع</th>
                                    <th>طريقة الدفع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->receipts as $receipt)
                                <tr>
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-' }}</td>
                                    <td class="text-success font-weight-bold">{{ number_format($receipt->amount, 2) }} دينار</td>
                                    <td>{{ $receipt->payment_method ?? 'نقدي' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td colspan="2" class="font-weight-bold">المجموع</td>
                                    <td class="font-weight-bold text-success">{{ number_format($invoice->receipts->sum('amount'), 2) }} دينار</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> لا توجد وصولات مدفوعة لهذه الفاتورة
                    </div>
                    @endif
                </div>
            </div>

            <!-- الخدمات -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">تفاصيل الخدمات</h4>
                </div>
                <div class="card-body">
                    @if($invoice->privateWingServices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" dir="rtl" style="text-align: center; font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th>اسم الخدمة</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->privateWingServices as $service)
                                <tr>
                                    <td>{{ $service->service->namesv ?? $service->service->service->sername ?? 'خدمة غير محددة' }}</td>
                                    <td>{{ $service->quantity ?? 1 }}</td>
                                    <td>{{ number_format($service->service_fee ?? $service->service->price ?? 0, 2) }} دينار</td>
                                    <td class="text-primary font-weight-bold">{{ number_format($service->total_amount ?? (($service->quantity ?? 1) * ($service->service_fee ?? $service->service->price ?? 0)), 2) }} دينار</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td colspan="3" class="font-weight-bold">إجمالي الخدمات</td>
                                    <td class="font-weight-bold text-primary">{{ number_format($invoice->total_amount, 2) }} دينار</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> لا توجد خدمات مرتبطة بهذه الفاتورة
                    </div>
                    @endif
                </div>
            </div>

            <!-- ملخص المالي -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">الملخص المالي</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">إجمالي الخدمات</h5>
                                    <h3 class="text-primary">{{ number_format($invoice->total_amount, 2) }} دينار</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">المبلغ المدفوع</h5>
                                    <h3 class="text-success">{{ number_format($invoice->receipts->sum('amount'), 2) }} دينار</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title {{ $remainingAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                        المبلغ المتبقي
                                    </h5>
                                    <h3 class="{{ $remainingAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($remainingAmount, 2) }} دينار
                                    </h3>
                                    @if($remainingAmount < 0)
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            مستحق للتسديد
                                        </small>
                                    @else
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            مدفوع بالكامل
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
@endsection