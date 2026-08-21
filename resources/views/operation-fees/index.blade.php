@extends('layouts.master')
@section('title')
تفاصيل أجور العمليات
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تفاصيل أجور العمليات
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
                    <h2 class="mb-0">تفاصيل أجور العمليات حسب الدرجة</h2>
                </div>

                <div class="card-body">
                    <!-- فلتر البحث -->
                    <form method="GET" action="{{ route('operation-fees.index') }}" class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="operation_name" class="form-label">اسم العملية</label>
                                <input type="text" name="operation_name" id="operation_name" class="form-control"
                                       value="{{ request('operation_name') }}" placeholder="ابحث باسم العملية">
                            </div>
                            <div class="col-md-2">
                                <label for="operation_degree" class="form-label">درجة العملية</label>
                                <select name="operation_degree" id="operation_degree" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="الخاصة" {{ request('operation_degree') == 'الخاصة' ? 'selected' : '' }}>الخاصة</option>
                                    <option value="فوق الكبرى" {{ request('operation_degree') == 'فوق الكبرى' ? 'selected' : '' }}>فوق الكبرى</option>
                                    <option value="كبرى" {{ request('operation_degree') == 'كبرى' ? 'selected' : '' }}>كبرى</option>
                                    <option value="متوسطة" {{ request('operation_degree') == 'متوسطة' ? 'selected' : '' }}>متوسطة</option>
                                    <option value="صغرى" {{ request('operation_degree') == 'صغرى' ? 'selected' : '' }}>صغرى</option>
                                </select>
                            </div>
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
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                 @if(request()->hasAny(['operation_name', 'operation_degree', 'date_from', 'date_to', 'month', 'year', 'moh_id', 'fck_id']))
                                <a href="{{ route('operation-fees.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> إزالة
                                </a>
                                <a href="{{ route('operation-fees.export', request()->query()) }}" class="btn btn-success">
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
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">اسم العملية</th>
                                    <th colspan="{{ count($operationDegrees) }}" style="text-align: center; background-color: #e3f2fd; font-weight: bold;">درجات العمليات</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle; background-color: #fff3cd; font-weight: bold;">المجموع الكلي</th>
                                </tr>
                                <tr>
                                    @foreach($operationDegrees as $degree)
                                    <th style="text-align: center; background-color: #f5f5f5;">{{ $degree }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($uniqueServices as $service)
                                <tr>
                                    <td style="font-weight: bold; text-align: right;">{{ $service->service->namesv ?? 'غير محدد' }}</td>
                                    @php
                                        $rowTotal = 0;
                                    @endphp
                                    @foreach($operationDegrees as $degree)
                                        @php
                                            $feeData = $operationFeesByDegree[$degree][$service->service_id] ?? null;
                                        @endphp
                                        @if($feeData)
                                        <td>{{ $feeData->service_fee == floor($feeData->service_fee) ? number_format($feeData->service_fee, 0) : number_format($feeData->service_fee, 2) }}</td>
                                        @php
                                            $rowTotal += $feeData->service_fee;
                                        @endphp
                                        @else
                                        <td style="background-color: #f8f9fa;">-</td>
                                        @endif
                                    @endforeach
                                    <td style="background-color: #fff3cd; font-weight: bold;">
                                        {{ $rowTotal == floor($rowTotal) ? number_format($rowTotal, 0) : number_format($rowTotal, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ 2 + count($operationDegrees) }}" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد بيانات متاحة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #e8f5e8; font-weight: bold;">
                                    <td style="text-align: center; font-weight: bold;">المجموع الكلي</td>
                                    @php
                                        $grandTotal = 0;
                                    @endphp
                                    @foreach($operationDegrees as $degree)
                                    <td style="background-color: #d4edda; font-weight: bold;">
                                        @php
                                            $totalAmount = $operationFeesByDegree[$degree]->sum('service_fee');
                                            $grandTotal += $totalAmount;
                                        @endphp
                                        {{ $totalAmount == floor($totalAmount) ? number_format($totalAmount, 0) : number_format($totalAmount, 2) }}
                                    </td>
                                    @endforeach
                                    <td style="background-color: #ffeaa7; font-weight: bold;">
                                        {{ $grandTotal == floor($grandTotal) ? number_format($grandTotal, 0) : number_format($grandTotal, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
