@extends('layouts.master')
@section('title')
إدارة الوصولات
@stop
@section('css')
<!-- Internal Data table css -->
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">إدارة الوصولات
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
                    <h2 class="mb-0">قائمة الوصولات</h2>
                </div>

                <div class="card-body">
                    <!-- فلتر البحث -->
                    <form method="GET" action="{{ route('receipts.index') }}" class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="receipt_number" class="form-label">رقم الوصل</label>
                                <input type="text" name="receipt_number" id="receipt_number" class="form-control"
                                       value="{{ request('receipt_number') }}" placeholder="ابحث برقم الوصل">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                @if(request()->hasAny(['date_from', 'date_to', 'receipt_number', 'month', 'year']))
                                <a href="{{ route('receipts.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> إزالة الفلتر
                                </a>
                                <a href="{{ route('receipts.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> تصدير Excel
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label for="month" class="form-label">الشهر</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="">جميع الأشهر</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->locale('ar')->monthName }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="year" class="form-label">السنة</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">جميع السنوات</option>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-sm key-buttons" data-page-length='10' dir="rtl" style="text-align: center; font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">رقم الوصل</th>
                                    <th style="text-align: center;">تاريخ الوصل</th>
                                    <th style="text-align: center;">المبلغ</th>
                                    <th style="text-align: center;">اسم المريض</th>
                                    <th style="text-align: center;">رقم الحساب</th>
                                    <th style="text-align: center;">نوع الدفع</th>
                                    <th style="text-align: center;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                <tr>
                                    <td style="font-weight: bold;">{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-' }}</td>
                                    <td style="font-weight: bold; color: #28a745;">
                                        {{ number_format($receipt->amount, 2) }} دينار عراقي
                                    </td>
                                    <td>{{ $receipt->privateWing->patient_name ?? 'غير محدد' }}</td>
                                    <td>{{ $receipt->privateWing->file_number ?? '-' }}</td>
                                    <td>
                                        @if($receipt->payment_type == 'cash')
                                            نقدي
                                        @elseif($receipt->payment_type == 'bank')
                                            بنكي
                                        @elseif($receipt->payment_type == 'check')
                                            شيك
                                        @else
                                            {{ $receipt->payment_type ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('receipts.show', $receipt->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد وصولات متاحة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $receipts->appends(request()->query())->links() }}
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