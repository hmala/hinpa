@extends('layouts.master')
@section('title', 'السجلات المبطلات')
@section('css')
<!-- Internal Data table css -->
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">السجلات المبطلات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">قائمة السجلات المبطلات للأجنحة الخاصة</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="pr-1 mb-3 mb-xl-0">
            <a href="{{ route('private-wings.index') }}" class="btn btn-primary btn-icon-text">
                <i class="fas fa-arrow-left"></i> العودة للقائمة الرئيسية
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-warning text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-ban"></i> السجلات المبطلات
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="trashed-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th><i class="fas fa-user"></i> اسم المريض</th>
                                    <th><i class="fas fa-building"></i> اسم المؤسسة</th>
                                    <th><i class="fas fa-hospital"></i> دائرة صحة</th>
                                    <th><i class="fas fa-file-alt"></i> رقم قائمة الحساب</th>
                                    <th><i class="fas fa-calendar-alt"></i> تاريخ الدخول</th>
                                    <th><i class="fas fa-calendar-times"></i> تاريخ الإبطال</th>
                                    <th><i class="fas fa-comment"></i> سبب الإبطال</th>
                                    <th><i class="fas fa-info-circle"></i> الحالة</th>
                                    <th class="text-center"><i class="fas fa-cogs"></i> الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($privateWings as $index => $wing)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $wing->patient_name }}</strong>
                                    </td>
                                    <td>{{ $wing->hospital ?? 'غير محدد' }}</td>
                                    <td>{{ $wing->health_department ?? 'غير محدد' }}</td>
                                    <td><span class="badge badge-primary">{{ $wing->file_number }}</span></td>
                                    <td>{{ $wing->entry_date ? $wing->entry_date->format('Y-m-d') : 'غير محدد' }}</td>
                                    <td>{{ $wing->cancelled_at ? $wing->cancelled_at->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                    <td>
                                        <span class="badge badge-warning" title="{{ $wing->cancellation_reason }}">
                                            {{ Str::limit($wing->cancellation_reason, 30) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($wing->trashed())
                                            <span class="badge badge-danger">محذوف ومبطل</span>
                                        @else
                                            <span class="badge badge-warning">مبطل</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('private-wings.show', $wing->id) }}" class="btn btn-info btn-sm" title="عرض الفاتورة">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد سجلات مبطلات</h5>
                                        <p class="text-muted">جميع السجلات النشطة في القائمة الرئيسية</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($privateWings->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $privateWings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<!-- Internal Data table js -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#trashed-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            },
            "pageLength": 10,
            "responsive": true,
            "order": [[ 4, "desc" ]] // ترتيب حسب تاريخ الحذف تنازلياً
        });
    });
</script>
@endsection
