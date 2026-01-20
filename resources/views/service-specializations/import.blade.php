@extends('layouts.master')

@section('title')
    استيراد بيانات الخدمات والتخصصات
@stop

@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الخدمات والتخصصات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ استيراد البيانات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('service-specializations.index') }}">رجوع</a>
                        </div>
                    </div><br>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ session()->get('success') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ session()->get('error') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card-body">
                        <form action="{{ route('service-specializations.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>ملف Excel</label>
                                        <input type="file" name="file" accept=".xlsx,.xls" class="form-control" required>
                                        @error('file')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">استيراد البيانات</button>
                            </div>
                        </form>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>تعليمات استيراد البيانات</h4>
                        </div>
                        <div class="card-body">
                            <p>يجب أن يحتوي ملف Excel على الأعمدة التالية:</p>                            <ul>
                                <li>codesv - رمز الخدمة في التخصص</li>
                                <li>namesv - اسم الخدمة في التخصص</li>
                                <li>price - السعر</li>
                                <li>notes - ملاحظات (اختياري)</li>
                                <li>service_id - معرف الخدمة</li>
                                <li>type_specializations_id - معرف التخصص</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection
