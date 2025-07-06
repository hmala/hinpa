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
<link href="{{ URL::asset('assets/css/print.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/css/private-wings.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">                <div class="card-header">
                    <div class="text-center mb-3">
                        <h2 class="mb-2">جمهورية العراق</h2>
                        <h3 class="mb-2">وزارة الصحة</h3>
                        <h2 class="invoice-title">وصل حساب الجناح الخاص</h2>
                        <div class="invoice-number mt-3">
                            <span>رقم الوصل: {{ $privateWing->receipt_number }}</span>
                            <span class="mx-4">|</span>
                            <span>التاريخ: {{ $privateWing->receipt_date }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>المستشفى:</strong>
                            <p>{{ $privateWing->hospital }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>دائرة صحة:</strong>
                            <p>{{ $privateWing->health_department }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>اسم المريض:</strong>
                            <p>{{ $privateWing->patient_name }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>رقم الملف:</strong>
                            <p>{{ $privateWing->file_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>الرقم الإحصائي:</strong>
                            <p>{{ $privateWing->statistical_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>عدد الأيام:</strong>
                            <p>{{ $privateWing->days_count }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>تاريخ الدخول:</strong>
                            <p>{{ $privateWing->entry_date }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الخروج:</strong>
                            <p>{{ $privateWing->exit_date }}</p>
                        </div>
                    </div>

                             <h4 class="mt-4 mb-3">الخدمات المقدمة</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>اسم الخدمة</th>
                                    <th>الأجور</th>
                                    <th>احتساب الأيام</th>
                                    <th>المبلغ الإجمالي</th>
                                </tr>
                            </thead>                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->service_name }}</td>
                                    <td>{{ number_format($service->service_fee, 2) }}</td>
                                    <td>{{ $service->is_daily ? 'نعم' : 'لا' }}</td>
                                    <td>{{ number_format($service->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-left"><strong>المجموع الكلي:</strong></td>
                                    <td><strong>{{ number_format($privateWing->total_amount, 2) }}</strong></td>
                                </tr>                                <tr class="table-info">
                                    <td colspan="2" class="text-left"><strong>المبلغ المودع:</strong></td>
                                    <td colspan="2"><strong>{{ number_format($privateWing->deposit_amount, 2) }} د.ع</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="2" class="text-left"><strong>المبلغ المتبقي:</strong></td>
                                    <td colspan="2"><strong>{{ number_format($privateWing->total_amount - $privateWing->deposit_amount, 2) }} د.ع</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                    <div class="row mt-4">
                        <div class="col-md-6">
                            <strong>رقم الوصل:</strong>
                            <p>{{ $privateWing->receipt_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الوصل:</strong>
                            <p>{{ $privateWing->receipt_date }}</p>
                        </div>
                       
                    </div>

                    <hr>
                    
                    <div class="row mt-4 print-section">
                        <div class="col-md-4">
                            <p><strong>توقيع المحاسب</strong></p>
                            <div class="signature-line"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>توقيع مدير المستشفى</strong></p>
                            <div class="signature-line"></div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>الختم</strong></p>
                            <div class="stamp-box"></div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button onclick="window.print()" class="btn btn-primary no-print">طباعة</button>
                            <a href="{{ route('private-wings.index') }}" class="btn btn-secondary no-print">رجوع</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
