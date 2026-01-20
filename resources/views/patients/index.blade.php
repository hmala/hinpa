@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users"></i> قائمة المرضى
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> رقم تسلسلي</th>
                                    <th><i class="fas fa-user"></i> اسم المريض</th>
                                    <th><i class="fas fa-file-alt"></i> رقم الحساب</th>
                                    <th><i class="fas fa-shield-alt"></i> مبلغ التأمينات</th>
                                    <th><i class="fas fa-dollar-sign"></i> مبلغ الخدمة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                <tr>
                                    <td>{{ $patient->id }}</td>
                                    <td>{{ $patient->patient_name }}</td>
                                    <td>{{ $patient->file_number }}</td>
                                    <td>{{ number_format($patient->deposit_amount, 2) }} دينار عراقي</td>
                                    <td>{{ number_format($patient->total_amount, 2) }} دينار عراقي</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد بيانات متاحة
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
@endsection