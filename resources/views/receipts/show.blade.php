@extends('layouts.master')
@section('title')
تفاصيل الوصل رقم {{ $receipt->receipt_number }}
@stop
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تفاصيل الوصل رقم {{ $receipt->receipt_number }}
            </span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('receipts.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">تفاصيل الوصل</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">رقم الوصل:</label>
                                <p class="form-control-plaintext">{{ $receipt->receipt_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">تاريخ الوصل:</label>
                                <p class="form-control-plaintext">{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">المبلغ:</label>
                                <p class="form-control-plaintext font-weight-bold text-success">{{ number_format($receipt->amount, 2) }} دينار عراقي</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">نوع الدفع:</label>
                                <p class="form-control-plaintext">
                                    @if($receipt->payment_type == 'cash')
                                        نقدي
                                    @elseif($receipt->payment_type == 'bank')
                                        بنكي
                                    @elseif($receipt->payment_type == 'check')
                                        شيك
                                    @else
                                        {{ $receipt->payment_type ?? '-' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($receipt->interest_rate && $receipt->interest_amount)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">معدل الفائدة:</label>
                                <p class="form-control-plaintext">{{ $receipt->interest_rate }}%</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">مبلغ الفائدة:</label>
                                <p class="form-control-plaintext">{{ number_format($receipt->interest_amount, 2) }} دينار عراقي</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <h4>معلومات المريض والجناح</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">اسم المريض:</label>
                                <p class="form-control-plaintext">{{ $receipt->privateWing->patient_name ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">رقم الحساب:</label>
                                <p class="form-control-plaintext">{{ $receipt->privateWing->file_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">تاريخ الدخول:</label>
                                <p class="form-control-plaintext">{{ $receipt->privateWing->entry_date ? $receipt->privateWing->entry_date->format('Y-m-d') : '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">تاريخ الخروج:</label>
                                <p class="form-control-plaintext">{{ $receipt->privateWing->exit_date ? $receipt->privateWing->exit_date->format('Y-m-d') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">عدد الأيام:</label>
                                <p class="form-control-plaintext">{{ $receipt->privateWing->days_count ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold">مبلغ التأمينات:</label>
                                <p class="form-control-plaintext">{{ number_format($receipt->privateWing->deposit_amount, 2) }} دينار عراقي</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection