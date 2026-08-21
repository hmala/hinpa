@extends('layouts.master')
@section('title')
تفاصيل وصولات الزيارات (غير الراقدين)
@stop
@section('css')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
    }
    .print-header { display: none; }
</style>
<style>
    select option {
        color: #000 !important;
        background-color: #fff !important;
    }
</style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الأجنحة الخاصة</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل وصولات الزيارات</span>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">تفاصيل وصولات الزيارات (غير الراقدين)</h2>
                    <div>
                        <a href="{{ route('private-visit-details.export', request()->query()) }}" class="btn btn-success no-print mr-2">
                            <i class="fas fa-file-excel"></i> تصدير إكسل
                        </a>
                        <button onclick="window.print()" class="btn btn-primary no-print">
                            <i class="fas fa-print"></i> طباعة التقرير
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- عنوان الطباعة -->
                    <div class="print-header">
                        <h2>تفاصيل وصولات الزيارات (غير الراقدين)</h2>
                        <p>تاريخ الطباعة: {{ date('Y-m-d H:i') }}</p>
                        @if(request()->hasAny(['patient_name', 'receipt_number', 'receipt_date_from', 'receipt_date_to', 'payment_type']))
                        <p>البحث المطبق: 
                            @if(request('patient_name')) اسم المريض: {{ request('patient_name') }} | @endif
                            @if(request('receipt_number')) رقم الوصل: {{ request('receipt_number') }} | @endif
                            @if(request('receipt_date_from')) من: {{ request('receipt_date_from') }} | @endif
                            @if(request('receipt_date_to')) إلى: {{ request('receipt_date_to') }} | @endif
                            @if(request('payment_type')) نوع الدفع: {{ request('payment_type') }} @endif
                        </p>
                        @endif
                    </div>
                    <!-- فلتر البحث -->
                    <form method="GET" action="{{ route('private-visit-details.index') }}" class="mb-4 no-print">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="patient_name" class="form-label">اسم المريض</label>
                                <input type="text" name="patient_name" id="patient_name" class="form-control"
                                       value="{{ request('patient_name') }}" placeholder="ابحث باسم المريض">
                            </div>
                            <div class="col-md-3">
                                <label for="receipt_number" class="form-label">رقم الوصل</label>
                                <input type="text" name="receipt_number" id="receipt_number" class="form-control"
                                       value="{{ request('receipt_number') }}" placeholder="ابحث برقم الوصل">
                            </div>
                            <div class="col-md-2">
                                <label for="receipt_date_from" class="form-label">تاريخ الوصل من</label>
                                <input type="date" name="receipt_date_from" id="receipt_date_from" class="form-control"
                                       value="{{ request('receipt_date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="receipt_date_to" class="form-label">إلى</label>
                                <input type="date" name="receipt_date_to" id="receipt_date_to" class="form-control"
                                       value="{{ request('receipt_date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                @if(request()->hasAny(['patient_name', 'receipt_number', 'receipt_date_from', 'receipt_date_to', 'payment_type', 'moh_id', 'fck_id']))
                                <a href="{{ route('private-visit-details.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إزالة
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="payment_type" class="form-label">نوع الدفع</label>
                                <select name="payment_type" id="payment_type" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="كاش" {{ request('payment_type') == 'كاش' ? 'selected' : '' }}>كاش</option>
                                    <option value="إلكتروني" {{ request('payment_type') == 'إلكتروني' ? 'selected' : '' }}>إلكتروني</option>
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
                       <table class="table table-bordered table-striped text-md-nowrap" dir="rtl" style="text-align: right">
                            <thead>
                                <tr>
                                    <th style="text-align: right;">تسلسل</th>
                                    <th style="text-align: right;">اسم المريض</th>
                                    <th style="text-align: right;">رقم الوصل</th>
                                    <th style="text-align: right;">التاريخ</th>
                                    <th style="text-align: right;">نوع الدفع</th>
                                    <th style="text-align: right;">مبلغ الوصل</th>
                                    <th style="text-align: right;">مبلغ الغرامة 5%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                <tr>
                                    <td>{{ $loop->iteration + ($receipts->currentPage() - 1) * $receipts->perPage() }}</td>
                                    <td>{{ $receipt->privateWing->patient_name ?? '-' }}</td>
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $receipt->payment_type == 'كاش' ? 'warning' : 'info' }}">
                                            {{ $receipt->payment_type }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($receipt->amount, 0) }} د.ع</td>
                                    <td>{{ number_format($receipt->interest_amount, 0) }} د.ع</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد بيانات متاحة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 no-print">
                        {{ $receipts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
