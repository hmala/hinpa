@extends('layouts.master')
@section('css')
<!--Internal  Font Awesome -->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!--Internal  treeview -->
<link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@section('title')
عرض الصلاحيات - مورا سوفت للادارة القانونية
@stop


@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الصلاحيات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض
                الصلاحيات</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-user-shield"></i> صلاحيات الدور: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        @if(!empty($rolePermissions))
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fas fa-key"></i> اسم الصلاحية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rolePermissions as $permission)
                    <tr>
                        <td class="text-center">
                            <i class="fas fa-check-circle text-success"></i> {{ $permission->name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted text-center"><i class="fas fa-exclamation-circle"></i> لا توجد صلاحيات متاحة.</p>
        @endif
    </div>
</div>
<!-- main-content closed -->
@endsection
@section('js')
<script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>

@endsection