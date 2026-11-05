@extends('layouts.master')
@section('title')
تفاصيل الزيارة الخارجية
@stop
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0"><i class="fas fa-file-alt me-2"></i> تفاصيل الزيارة والخدمات الخارجية</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-md-6"><strong>المستشفى:</strong> {{ $visit->hospital }}</div>
                        <div class="col-md-6"><strong>دائرة الصحة:</strong> {{ $visit->health_department }}</div>
                    </div>

                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-md-6"><strong>اسم الزائر:</strong> <span class="fs-5 text-primary fw-bold">{{ $visit->visitor_name }}</span></div>
                        <div class="col-md-6"><strong>رقم الهاتف:</strong> {{ $visit->visitor_phone ?: '-' }}</div>
                    </div>

                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-md-6"><strong>الجنسية:</strong> {{ $visit->visitor_nationality ?: '-' }}</div>
                        <div class="col-md-6"><strong>تاريخ الزيارة:</strong> {{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-' }} {{ $visit->visit_time }}</div>
                    </div>

                    <div class="row mb-3 border-bottom pb-2">
                        <div class="col-md-6"><strong>رقم الوصل:</strong> <span class="badge badge-secondary fs-6">{{ $visit->receipt_number }}</span></div>
                        <div class="col-md-6"><strong>طريقة الدفع:</strong> {{ $visit->payment_type }}</div>
                    </div>

                    <!-- قائمة الخدمات المقدمة -->
                    <h5 class="mt-4 mb-3 text-primary"><i class="fas fa-list me-1"></i> الخدمات الطبية المسجلة للزيارة:</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم الخدمة</th>
                                    <th>الطبيب المعالج</th>
                                    <th>درجة العملية</th>
                                    <th>المبلغ المسدد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visit->services as $service)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $service->serviceSpecialization ? $service->serviceSpecialization->namesv : '-' }}</td>
                                    <td>{{ $service->doctor ? $service->doctor->name : '-' }}</td>
                                    <td>{{ $service->operation_degree ?: '-' }}</td>
                                    <td class="fw-bold text-success">{{ number_format($service->service_fee, 2) }} د.ع</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mb-4 bg-light p-3 rounded">
                        <div class="col-md-12 text-center">
                            <h4 class="text-success mb-0">المبلغ الإجمالي المسدد: <strong>{{ number_format($visit->total_amount, 2) }} د.ع</strong></h4>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('private-visits.invoice', $visit->id) }}" class="btn btn-info px-4 me-2" target="_blank"><i class="fas fa-print me-1"></i> طباعة الفاتورة</a>
                        <a href="{{ route('private-visits.index') }}" class="btn btn-secondary px-4">رجوع للقائمة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
