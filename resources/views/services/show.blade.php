@extends('layouts.master')
@section('title')
تفاصيل الخدمة
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
                            <h3 class="mb-0 gradient-text">
                                <i class="fas fa-info-circle fa-fw me-2"></i>
                                تفاصيل الخدمة
                            </h3>
                            <p class="text-secondary mb-0">عرض بيانات الخدمة: {{ $service->sername }}</p>
                        </div>
                        <div>                            <a href="{{ route('services.specializations.manage', $service) }}" class="btn btn-info me-2">
                                <i class="fas fa-link me-2"></i>
                                إدارة التخصصات
                            </a>
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-2"></i>
                                تعديل
                            </a>
                            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
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
                                    <i class="fas fa-hashtag text-primary me-2"></i>
                                    كود الخدمة
                                </div>
                                <div class="info-card-body">
                                    <span class="badge bg-gradient-primary">{{ $service->sercode }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-card mb-4">
                                <div class="info-card-header">
                                    <i class="fas fa-pen text-primary me-2"></i>
                                    اسم الخدمة
                                </div>
                                <div class="info-card-body">
                                    {{ $service->sername }}
                                </div>                            </div>
                        </div>
                    </div>

                    <!-- قسم التخصصات المرتبطة -->
                    <div class="mt-5">
                        <h4 class="mb-4 text-primary">
                            <i class="fas fa-link fa-fw me-2"></i>
                            التخصصات المرتبطة
                        </h4>
                        
                        @if($service->typeSpecializations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>التخصص</th>
                                            <th>التكلفة</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($service->typeSpecializations as $specialization)
                                            <tr>
                                                <td>{{ $specialization->tsname }}</td>
                                                <td>{{ number_format($specialization->pivot->cost, 2) }}</td>
                                                <td>{{ $specialization->pivot->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                لا توجد تخصصات مرتبطة بهذه الخدمة
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 15px;
        background: #ffffff;
    }
    .card-header {
        background: none;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .info-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-card-header {
        color: #4a5568;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .info-card-body {
        font-size: 1rem;
        color: #2d3748;
        padding: 0.5rem 0;
    }
    .badge.bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        padding: 0.5em 1em;
        font-size: 0.875rem;
    }
    .text-primary {
        color: #4e73df !important;
    }
    .gradient-text {
        background: linear-gradient(45deg, #36b9cc, #1a8997);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        display: inline-block;
    }
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 8px;
    }
    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: #fff;
    }
    .btn-warning:hover {
        background-color: #dfa408;
        border-color: #dfa408;
        color: #fff;
    }
</style>
@endpush
@endsection
