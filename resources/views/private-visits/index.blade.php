@extends('layouts.master')
@section('title')
حسابات الزيارات والخدمات الخارجية
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<style>
    @media print {
        .no-print, .breadcrumb-header, .btn, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
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
                        <button type="button" class="btn btn-info ms-2" data-toggle="modal" data-target="#searchModal" onclick="$('#searchModal').modal('show');">
                            <i class="fas fa-search me-1"></i> بحث متقدم
                        </button>
                        <a href="{{ route('private-visits.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> إضافة زيارة جديدة
                        </a>
                        <a href="{{ route('private-visits.export', request()->query()) }}" class="btn btn-success ms-2">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل
                        </a>
                        <a href="{{ route('private-visit-details.print', request()->query()) }}" target="_blank" class="btn btn-secondary ms-2">
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

                    @if(request()->hasAny(['patient_name', 'patient_phone', 'sequential_number', 'annual_number', 'receipt_number', 'payment_status', 'payment_type', 'created_at_from', 'created_at_to', 'visit_date_from', 'visit_date_to', 'moh_id', 'fck_id', 'hospital']))
                    <div class="alert alert-info alert-dismissible fade show d-flex justify-content-between align-items-center mb-3 no-print" role="alert">
                        <div>
                            <i class="fas fa-filter me-1"></i> <strong>نتائج البحث المتقدم مفعلة</strong>
                            <span class="text-muted mr-2">
                                @if(request('patient_name')) | الزائر/المريض: {{ request('patient_name') }} @endif
                                @if(request('patient_phone')) | الهاتف: {{ request('patient_phone') }} @endif
                                @if(request('sequential_number')) | التسلسلي: {{ request('sequential_number') }} @endif
                                @if(request('annual_number')) | السنوي: {{ request('annual_number') }} @endif
                                @if(request('receipt_number')) | رقم الوصل: {{ request('receipt_number') }} @endif
                                @if(request('payment_type')) | الدفع: {{ request('payment_type') }} @endif
                                @if(request('visit_date_from')) | من تاريخ: {{ request('visit_date_from') }} @endif
                                @if(request('visit_date_to')) | إلى تاريخ: {{ request('visit_date_to') }} @endif
                            </span>
                        </div>
                        <a href="{{ route('private-visits.index') }}" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i> إلغاء الفلتر
                        </a>
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
                                    <td>{{ ($visits->currentPage() - 1) * $visits->perPage() + $loop->iteration }}</td>
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

<!-- Modal للبحث المتقدم -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel"><i class="fas fa-search me-1"></i> البحث المتقدم - حسابات الزيارات والخدمات الخارجية</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('private-visits.index') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-2">
                            <label for="patient_name" class="form-label font-weight-bold">اسم الزائر / المريض</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ request('patient_name') }}" placeholder="ابحث باسم الزائر أو المريض">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="patient_phone" class="form-label font-weight-bold">رقم الهاتف</label>
                            <input type="text" class="form-control" id="patient_phone" name="patient_phone" value="{{ request('patient_phone') }}" placeholder="ابحث برقم الهاتف">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="sequential_number" class="form-label font-weight-bold">الترقيم التسلسلي (باركود)</label>
                            <input type="text" class="form-control" id="sequential_number" name="sequential_number" value="{{ request('sequential_number') }}" placeholder="ابحث بالترقيم التسلسلي">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="annual_number" class="form-label font-weight-bold">الترقيم السنوي</label>
                            <input type="text" class="form-control" id="annual_number" name="annual_number" value="{{ request('annual_number') }}" placeholder="ابحث بالترقيم السنوي">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="receipt_number" class="form-label font-weight-bold">رقم الوصل</label>
                            <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="{{ request('receipt_number') }}" placeholder="ابحث برقم الوصل">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="payment_type" class="form-label font-weight-bold">طريقة الدفع</label>
                            <select class="form-control" id="payment_type" name="payment_type">
                                <option value="">الكل</option>
                                <option value="كاش" {{ request('payment_type') == 'كاش' ? 'selected' : '' }}>كاش</option>
                                <option value="إلكتروني" {{ request('payment_type') == 'إلكتروني' ? 'selected' : '' }}>إلكتروني</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="visit_date_from" class="form-label font-weight-bold">تاريخ الزيارة من</label>
                            <input type="date" class="form-control" id="visit_date_from" name="visit_date_from" value="{{ request('visit_date_from') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="visit_date_to" class="form-label font-weight-bold">تاريخ الزيارة إلى</label>
                            <input type="date" class="form-control" id="visit_date_to" name="visit_date_to" value="{{ request('visit_date_to') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="created_at_from" class="form-label font-weight-bold">تاريخ تنظيم الاستمارة من</label>
                            <input type="date" class="form-control" id="created_at_from" name="created_at_from" value="{{ request('created_at_from') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="created_at_to" class="form-label font-weight-bold">تاريخ تنظيم الاستمارة إلى</label>
                            <input type="date" class="form-control" id="created_at_to" name="created_at_to" value="{{ request('created_at_to') }}">
                        </div>
                        @if((in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name)) && isset($availableMohs))
                        <div class="col-md-6 mb-2">
                            <label for="moh_id" class="form-label font-weight-bold">الدائرة</label>
                            <select class="form-control" id="moh_id" name="moh_id">
                                <option value="">الكل</option>
                                @foreach($availableMohs as $moh)
                                    <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if((in_array("Admin", auth()->user()->roles_name) || in_array("mohst", auth()->user()->roles_name) || auth()->user()->roles_name === ["stat-doh"]) && isset($availableFcks))
                        <div class="col-md-6 mb-2">
                            <label for="fck_id" class="form-label font-weight-bold">المستشفى</label>
                            <select class="form-control" id="fck_id" name="fck_id">
                                <option value="">الكل</option>
                                @foreach($availableFcks as $fck)
                                    <option value="{{ $fck->id }}" data-moh-id="{{ $fck->moh_id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <a href="{{ route('private-visits.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
                    <button type="submit" class="btn btn-primary">بحث</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
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
                $fckSelect.append('<option value="">الكل</option>');
                
                allOptions.each(function() {
                    var mohId = $(this).data('moh-id');
                    if (!selectedMoh || mohId == selectedMoh) {
                        if ($(this).val() !== '') {
                            $fckSelect.append($(this).clone());
                        }
                    }
                });
                
                if (currentSelectedFck && $fckSelect.find('option[value="' + currentSelectedFck + '"]').length) {
                    $fckSelect.val(currentSelectedFck);
                }
            }
            
            $mohSelect.on('change', filterHospitals);
            if ($mohSelect.val()) {
                filterHospitals();
            }
        }
    });
</script>
@endsection
