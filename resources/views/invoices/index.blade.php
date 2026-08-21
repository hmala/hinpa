@extends('layouts.master')
@section('title')
إدارة الفواتير
@stop
@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">إدارة الفواتير
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
                    <h2 class="mb-0">قائمة الفواتير</h2>
                </div>

                <div class="card-body">
                    @php
                        $debtInvoices = $invoices->filter(function($invoice) {
                            return ($invoice->deposit_amount - $invoice->total_amount) < 0;
                        });
                    @endphp
                    @if($debtInvoices->count() > 0)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>تنبيه!</strong> يوجد {{ $debtInvoices->count() }} فاتورة تحتاج لتسديد المبالغ المستحقة.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- فلتر البحث -->
                    <form method="GET" action="{{ route('invoices.index') }}" class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <input type="date" name="date_from" id="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <input type="date" name="date_to" id="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="receipt_number" class="form-label">رقم القائمة</label>
                                <input type="text" name="receipt_number" id="receipt_number" class="form-control"
                                       value="{{ request('receipt_number') }}" placeholder="ابحث برقم القائمة">
                            </div>
                            <div class="col-md-2">
                                <label for="patient_name" class="form-label">اسم المريض</label>
                                <input type="text" name="patient_name" id="patient_name" class="form-control"
                                       value="{{ request('patient_name') }}" placeholder="ابحث باسم المريض">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                 @if(request()->hasAny(['date_from', 'date_to', 'receipt_number', 'patient_name', 'month', 'year', 'moh_id', 'fck_id']))
                                <a href="{{ route('invoices.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> إزالة الفلتر
                                </a>
                                <a href="{{ route('invoices.export', request()->query()) }}" class="btn btn-success">
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
                            @if(in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name))
                            <div class="col-md-3">
                                <label for="moh_id" class="form-label">الدائرة</label>
                                <select name="moh_id" id="moh_id" class="form-control">
                                    <option value="">جميع الدوائر</option>
                                    @foreach($availableMohs as $moh)
                                    <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if(in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name) || auth()->user()->roles_name === ["stat-doh"])
                            <div class="col-md-3">
                                <label for="fck_id" class="form-label">المستشفى</label>
                                <select name="fck_id" id="fck_id" class="form-control">
                                    <option value="">جميع المستشفيات</option>
                                    @foreach($availableFcks as $fck)
                                    <option value="{{ $fck->id }}" data-moh-id="{{ $fck->moh_id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-sm key-buttons" data-page-length='10' dir="rtl" style="text-align: center; font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">رقم القائمة</th>
                                    <th style="text-align: center;">اسم المريض</th>
                                    <th style="text-align: center;">تاريخ تنظيم القائمة</th>
                                    <th style="text-align: center;">الوصولات</th>
                                    <th style="text-align: center;">إجمالي الخدمات</th>
                                    <th style="text-align: center;">المتبقي</th>
                                    <th style="text-align: center;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                @php
                                    $remainingAmount = $invoice->deposit_amount - $invoice->total_amount;
                                    $hasDebt = $remainingAmount < 0;
                                @endphp
                                <tr class="{{ $hasDebt ? 'table-danger' : '' }}">
                                    <td style="font-weight: bold;">
                                        {{ $invoice->receipt_number ?? $invoice->id }}
                                        @if($hasDebt)
                                            <i class="fas fa-exclamation-triangle text-danger" title="يحتاج تسديد"></i>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->patient_name ?? 'غير محدد' }}</td>
                                    <td>{{ $invoice->receipt_date ? $invoice->receipt_date->format('Y-m-d') : ($invoice->entry_date ? $invoice->entry_date->format('Y-m-d') : '-') }}</td>
                                    <td style="font-weight: bold; color: #28a745;">
                                        {{ number_format($invoice->receipts->sum('amount'), 2) }} دينار
                                    </td>
                                    <td style="font-weight: bold; color: #007bff;">
                                        {{ number_format($invoice->total_amount, 2) }} دينار
                                    </td>
                                    <td style="font-weight: bold; {{ $invoice->deposit_amount - $invoice->total_amount >= 0 ? 'color: #28a745;' : 'color: #dc3545;' }}">
                                        {{ number_format($invoice->deposit_amount - $invoice->total_amount, 2) }} دينار
                                    </td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد فواتير متاحة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $invoices->appends(request()->query())->links() }}
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
@endsection