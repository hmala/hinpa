@extends('layouts.master')
@section('title')
تعديل الطبيب
@stop
@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">تعديل الطبيب
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
                    <h2>تعديل الطبيب</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('doctors.update', $doctor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $doctor->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <textarea class="form-control" id="address" name="address">{{ $doctor->address }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="job_title">المسمى الوظيفي</label>
                            <input type="text" class="form-control" id="job_title" name="job_title" value="{{ $doctor->job_title }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث</button>
                        <a href="{{ route('doctors.index') }}" class="btn btn-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    $('.select2').select2();
</script>
@endsection