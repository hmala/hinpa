@extends('layouts.master')
@section('title')
تفاصيل الأجنحة الخاصة
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تفاصيل الأجنحة الخاصة
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
                    <h2 class="mb-0">تفاصيل الأجنحة الخاصة</h2>
                    <div>
                        <a href="{{ route('private-wing-details.export') }}" class="btn btn-success">
                            <i class="fas fa-download"></i> تصدير إلى Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- فلتر البحث -->
                    <form method="GET" action="{{ route('private-wing-details.index') }}" class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="patient_name" class="form-label">اسم المريض</label>
                                <input type="text" name="patient_name" id="patient_name" class="form-control"
                                       value="{{ request('patient_name') }}" placeholder="ابحث باسم المريض">
                            </div>
                            <div class="col-md-3">
                                <label for="file_number" class="form-label">رقم الحساب</label>
                                <input type="text" name="file_number" id="file_number" class="form-control"
                                       value="{{ request('file_number') }}" placeholder="ابحث برقم الحساب">
                            </div>
                            <div class="col-md-2">
                                <label for="entry_date_from" class="form-label">تاريخ الدخول من</label>
                                <input type="date" name="entry_date_from" id="entry_date_from" class="form-control"
                                       value="{{ request('entry_date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="entry_date_to" class="form-label">إلى</label>
                                <input type="date" name="entry_date_to" id="entry_date_to" class="form-control"
                                       value="{{ request('entry_date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                @if(request()->hasAny(['patient_name', 'file_number', 'entry_date_from', 'entry_date_to', 'month', 'year']))
                                <a href="{{ route('private-wing-details.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إزالة
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
                       <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='10' dir="rtl" style="text-align: right">
                            <thead>
                                <tr>
                                    <th style="text-align: right;"><i class="fas fa-hashtag"></i> رقم تسلسلي</th>
                                    <th style="text-align: right;"><i class="fas fa-user"></i> اسم المريض</th>
                                    <th style="text-align: right;"><i class="fas fa-file-alt"></i> رقم الحساب</th>
                                    <th style="text-align: right;"><i class="fas fa-shield-alt"></i> مبلغ التأمينات</th>
                                    <th style="text-align: right;"><i class="fas fa-calculator"></i> مجموع الخدمات</th>
                                    <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> المبلغ المتبقي</th>
                                    @foreach($usedServices as $serviceId => $serviceName)
                                    <th style="text-align: right;"><i class="fas fa-concierge-bell"></i> {{ $serviceName }}</th>
                                    @endforeach
                                    <th style="text-align: right;"><i class="fas fa-money-bill-wave"></i> أجور الأطباء</th>
                                    <th style="text-align: right;"><i class="fas fa-user-md"></i> الأطباء</th>
                                    <th style="text-align: right;"><i class="fas fa-percentage"></i> 5% رسوم عملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($privateWings as $wing)
                                <tr>
                                    <td>{{ $wing->id }}</td>
                                    <td>{{ $wing->patient_name }}</td>
                                    <td>{{ $wing->file_number }}</td>
                                    <td>{{ $wing->deposit_amount == floor($wing->deposit_amount) ? number_format($wing->deposit_amount, 0) : number_format($wing->deposit_amount, 2) }}</td>
                                    <td>{{ $wing->total_amount == floor($wing->total_amount) ? number_format($wing->total_amount, 0) : number_format($wing->total_amount, 2) }}</td>
                                    <td>{{ ($wing->deposit_amount - $wing->total_amount) == floor($wing->deposit_amount - $wing->total_amount) ? number_format($wing->deposit_amount - $wing->total_amount, 0) : number_format($wing->deposit_amount - $wing->total_amount, 2) }}</td>
                                    @php
                                        // استخدم PrivateWingService لأنّها تحتوي على doctor و service_fee
                                        $wingServices = $wing->privateWingServices->keyBy('service_id');
                                        $doctors = $wing->privateWingServices->pluck('doctor.name')->filter()->unique()->implode(', ');
                                    @endphp
                                    @foreach($usedServices as $serviceId => $serviceName)
                                    <td>
                                        @if(isset($wingServices[$serviceId]))
                                            {{ $wingServices[$serviceId]->service_fee == floor($wingServices[$serviceId]->service_fee) ? number_format($wingServices[$serviceId]->service_fee, 0) : number_format($wingServices[$serviceId]->service_fee, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endforeach
                                    <td>{{ $wing->total_amount == floor($wing->total_amount) ? number_format($wing->total_amount, 0) : number_format($wing->total_amount, 2) }}</td>
                                    <td>{{ $doctors ?: '-' }}</td>
                                    <td>{{ ($wing->total_amount * 0.05) == floor($wing->total_amount * 0.05) ? number_format($wing->total_amount * 0.05, 0) : number_format($wing->total_amount * 0.05, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ 8 + $usedServices->count() + 1 }}" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد بيانات متاحة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
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
@endsection