@extends('layouts.master')
@section('css')
<!-- Internal Font Awesome -->
<link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
<!-- Internal Custom CSS for Cards -->
<style>
    .custom-card {
        background-color:rgb(11, 122, 234); /* لون خلفية افتراضي */
        border: 1px solid #ddd; /* حدود الكارت */
        border-radius: 8px;
        margin-bottom: 15px;
        padding: 15px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* تأثيرات الحركة */
    }

    .custom-card:hover {
        transform: scale(1.05); /* تكبير الكارت قليلاً */
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* إضافة ظل عند التفاعل */
    }

    .custom-card:nth-child(1) {
        background-color:rgb(188, 223, 229); /* لون وردي */
    }

    .custom-card:nth-child(2) {
        background-color: #e6ffe1; /* لون أخضر فاتح */
    }

    .custom-card:nth-child(3) {
        background-color: #e1f0ff; /* لون أزرق فاتح */
    }
</style>
@section('title')
تعديل الصلاحيات - مورا سوفت للادارة القانونية
@stop
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الصلاحيات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل الصلاحيات</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')

@if (count($errors) > 0)
<div class="alert alert-danger">
    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>خطأ</strong>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">تعديل الصلاحية</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>اسم الصلاحية:</label>
                    {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                </div>
                <div class="row">
                    @foreach($permission as $value)
                    <div class="col-md-4">
                        <div class="custom-card">
                            <label>
                                {{ Form::checkbox('permission[]', $value->name, in_array($value->id, $rolePermissions) ? true : false) }}
                                {{ $value->name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
@section('js')
<!-- No additional JS required for cards -->
@endsection