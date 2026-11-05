@extends('layouts.master')
@section('title')
حسابات الزيارات والخدمات الخارجية
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الزيارات الخارجية</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة حسابات الزوار</span>
        </div>
    </div>
</div>
@endsection
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h4 class="mb-0 text-primary"><i class="fas fa-user-clock me-2"></i> قائمة حسابات الزيارات والخدمات الخارجية</h4>
                    <div>
                        <a href="{{ route('private-visits.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> إضافة زيارة جديدة
                        </a>
                        <a href="{{ route('private-visits.export', request()->query()) }}" class="btn btn-success ms-2">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل
                        </a>
                        <a href="{{ route('private-visit-details.print', request()->query()) }}" target="_blank" class="btn btn-info ms-2">
                            <i class="fas fa-print me-1"></i> طباعة كشف معتمد
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @php
                        $visits = $visits ?? $privateVisits ?? collect();
                    @endphp
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">الزائر</th>
                                    <th style="width: 30%">الخدمة والتاريخ</th>
                                    <th style="width: 20%">الحساب والوصل</th>
                                    <th style="width: 20%">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-right">
                                        <strong class="text-dark fs-15">{{ $visit->visitor_name }}</strong>
                                        <div class="text-muted small mt-1">
                                            <span><i class="fas fa-phone-alt ml-1 text-primary"></i>{{ $visit->visitor_phone ?: '-' }}</span>
                                            <span class="mx-1">|</span>
                                            <span><i class="fas fa-flag ml-1 text-secondary"></i>{{ $visit->visitor_nationality ?: 'عراقي' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div>
                                            <strong class="text-primary">{{ $visit->serviceSpecialization ? $visit->serviceSpecialization->namesv : '-' }}</strong>
                                        </div>
                                        <div class="text-muted small mt-1">
                                            @if($visit->doctor)
                                                <span><i class="fas fa-user-md ml-1 text-info"></i>{{ $visit->doctor->name }}</span>
                                                <span class="mx-1">|</span>
                                            @endif
                                            <span><i class="far fa-calendar-alt ml-1 text-secondary"></i>{{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-' }} {{ $visit->visit_time }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success fs-15 d-block">{{ number_format($visit->total_amount, 0) }} د.ع</strong>
                                        <div class="mt-1">
                                            <span class="badge badge-secondary">وصل: {{ $visit->receipt_number }}</span>
                                            <span class="badge {{ $visit->payment_type == 'كاش' ? 'badge-success' : 'badge-info' }}">{{ $visit->payment_type }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('private-visits.invoice', $visit->id) }}" class="btn btn-info" target="_blank" title="طباعة الفاتورة">
                                                <i class="fas fa-print"></i> فاتورة
                                            </a>
                                            <a href="{{ route('private-visits.show', $visit->id) }}" class="btn btn-secondary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                            <a href="{{ route('private-visits.edit', $visit->id) }}" class="btn btn-primary" title="تعديل">
                                                <i class="fas fa-edit"></i> تعديل
                                            </a>
                                            <form action="{{ route('private-visits.destroy', $visit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="حذف"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-muted py-4">لا توجد زيارات مسجلة حالياً</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $visits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
