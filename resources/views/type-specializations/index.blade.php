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
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <div>
                        <h3 class="mb-0 text-primary">
                            <i class="fas fa-laptop-medical fa-fw me-2"></i>
                            قائمة التخصصات
                        </h3>
                        <p class="text-secondary mb-0">إدارة التخصصات الطبية في النظام</p>
                    </div>
                    <a href="{{ route('type-specializations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>
                        إضافة تخصص جديد
                    </a>
                </div>
                
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary text-center px-2">كود التخصص</th>
                                    <th class="text-secondary">اسم التخصص</th>
                                    <th class="text-secondary text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($specializations as $specialization)
                                    <tr>
                                        <td class="text-center px-2">
                                            <span class="badge bg-gradient-primary">{{ $specialization->tscode }}</span>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 text-sm">{{ $specialization->tsname }}</h6>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('type-specializations.show', $specialization) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('type-specializations.edit', $specialization) }}" 
                                                   class="btn btn-warning btn-sm mx-1" 
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('type-specializations.destroy', $specialization) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا التخصص؟')"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            لا توجد تخصصات مضافة بعد
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

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 15px;
    }
    .card-header {
        background: none;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .table > thead {
        background-color: #f8f9fa;
    }
    .table > :not(caption) > * > * {
        padding: 1rem;
    }
    .badge {
        padding: 0.5em 1em;
        font-size: 0.75em;
    }
    .btn-group .btn {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
    }
    .table thead th {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .btn-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.3);
    }
    .alert {
        border: none;
        border-radius: 10px;
    }
    .alert-success {
        background: linear-gradient(45deg, #1cc88a, #169a6f);
        color: white;
    }
</style>
@endpush
@endsection
