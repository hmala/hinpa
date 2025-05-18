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
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">ألاسرة المهيئة 
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
                <div class="card-header">ربط خدمة جديدة بتخصص</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('service-specializations.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="service_id" class="col-md-4 col-form-label text-md-end">الخدمة</label>
                            <div class="col-md-6">
                                <select id="service_id" name="service_id" class="form-control @error('service_id') is-invalid @enderror" required>
                                    <option value="">اختر الخدمة</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->sername }}</option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="type_specialization_id" class="col-md-4 col-form-label text-md-end">التخصص</label>                            <div class="col-md-6">
                                <select id="type_specialization_id" name="type_specialization_id" class="form-control @error('type_specialization_id') is-invalid @enderror" required>
                                    <option value="">اختر التخصص</option>
                                    @foreach ($typeSpecializations as $specialization)
                                        <option value="{{ $specialization->id }}">{{ $specialization->tsname }}</option>
                                    @endforeach
                                </select>
                                @error('type_specialization_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       

                        <div class="form-group row mb-3">
                            <label for="codesv" class="col-md-4 col-form-label text-md-end">رمز الخدمة في التخصص</label>
                            <div class="col-md-6">
                                <input id="codesv" type="text" class="form-control @error('codesv') is-invalid @enderror" name="codesv" value="{{ old('codesv') }}" required>
                                @error('codesv')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="namesv" class="col-md-4 col-form-label text-md-end">اسم الخدمة في التخصص</label>
                            <div class="col-md-6">
                                <input id="namesv" type="text" class="form-control @error('namesv') is-invalid @enderror" name="namesv" value="{{ old('namesv') }}" required>
                                @error('namesv')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="price" class="col-md-4 col-form-label text-md-end">السعر</label>
                            <div class="col-md-6">
                                <input id="price" type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-end">ملاحظات</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    حفظ
                                </button>
                                <a href="{{ route('service-specializations.index') }}" class="btn btn-secondary">
                                    رجوع
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
