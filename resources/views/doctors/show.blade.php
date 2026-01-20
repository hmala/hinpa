@extends('layouts.master')
@section('title')
تفاصيل الطبيب
@stop
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تفاصيل الطبيب
            </span>
        </div>
    </div>

</div>
<!-- breadcrumb -->
@endsection
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>تفاصيل الطبيب</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>الاسم:</strong> {{ $doctor->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>العنوان:</strong> {{ $doctor->address ?: 'غير محدد' }}
                        </div>
                    </div>
                  
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>دائرة الصحة:</strong> {{ $doctor->moh ? $doctor->moh->mohname : 'غير محدد' }}
                        </div>
                        <div class="col-md-6">
                            <strong>اسم المؤسسة:</strong> {{ $doctor->fck ? $doctor->fck->Fckname : 'غير محدد' }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('doctors.index') }}" class="btn btn-secondary">العودة إلى القائمة</a>
                    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary">تعديل</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection