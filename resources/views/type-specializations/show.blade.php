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
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-info">
                                <i class="fas fa-info-circle fa-fw me-2"></i>
                                تفاصيل التخصص
                            </h3>
                            <p class="text-secondary mb-0">عرض بيانات التخصص: {{ $specialization->tsname }}</p>
                        </div>
                        <div>
                            <a href="{{ route('specializations.services.manage', $specialization) }}" class="btn btn-info me-2">
                                <i class="fas fa-link me-2"></i>
                                إدارة الخدمات
                            </a>
                            <a href="{{ route('type-specializations.edit', $specialization) }}" class="btn btn-primary">
                                تعديل
                            </a>
                            <a href="{{ route('type-specializations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>
                                رجوع للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card mb-4">
                                <div class="info-card-header">
                                    <i class="fas fa-hashtag text-info me-2"></i>
                                    كود التخصص
                                </div>
                                <div class="info-card-body">
                                    {{ $specialization->tscode }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card mb-4">
                                <div class="info-card-header">
                                    <i class="fas fa-pen text-info me-2"></i>
                                    اسم التخصص
                                </div>
                                <div class="info-card-body">
                                    {{ $specialization->tsname }}
                                </div>                    </div>
                        </div>
                    </div>

                    <!-- قسم الخدمات المرتبطة -->
                    <div class="mt-5">
                        <h4 class="mb-4 text-info">
                            <i class="fas fa-link fa-fw me-2"></i>
                            الخدمات المرتبطة
                        </h4>
                        
                        @if($specialization->services->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>الخدمة</th>
                                            <th>التكلفة</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($specialization->services as $service)
                                            <tr>
                                                <td>{{ $service->sername }}</td>
                                                <td>{{ number_format($service->pivot->cost, 2) }}</td>
                                                <td>{{ $service->pivot->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                لا توجد خدمات مرتبطة بهذا التخصص
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection