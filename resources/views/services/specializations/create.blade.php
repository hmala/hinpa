@extends('layouts.master')
@section('css')
<!-- Internal Select2 css -->
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">ربط الخدمات بالتخصصات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة جديد</span>
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
                    <form action="{{ route('service-specializations.store') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="service_id" class="required">الخدمة</label>
                                    <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror" required>
                                        <option value="">-- اختر الخدمة --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->sername }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="type_specialization_id" class="required">التخصص</label>
                                    <select name="type_specialization_id" id="type_specialization_id" class="form-control @error('type_specialization_id') is-invalid @enderror" required>
                                        <option value="">-- اختر التخصص --</option>
                                        @foreach($typeSpecializations as $specialization)
                                            <option value="{{ $specialization->id }}" {{ old('type_specialization_id') == $specialization->id ? 'selected' : '' }}>
                                                {{ $specialization->tsname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_specialization_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="tscode" class="required">رمز التخصص</label>
                                    <input type="text" class="form-control @error('tscode') is-invalid @enderror" 
                                           id="tscode" name="tscode" value="{{ old('tscode') }}" required>
                                    @error('tscode')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="sercode" class="required">رمز الخدمة</label>
                                    <input type="text" class="form-control @error('sercode') is-invalid @enderror" 
                                           id="sercode" name="sercode" value="{{ old('sercode') }}" required>
                                    @error('sercode')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="codesv" class="required">رمز الخدمة في التخصص</label>
                                    <input type="text" class="form-control @error('codesv') is-invalid @enderror" 
                                           id="codesv" name="codesv" value="{{ old('codesv') }}" required>
                                    @error('codesv')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="namesv" class="required">اسم الخدمة في التخصص</label>
                                    <input type="text" class="form-control @error('namesv') is-invalid @enderror" 
                                           id="namesv" name="namesv" value="{{ old('namesv') }}" required>
                                    @error('namesv')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="price" class="required">السعر</label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="notes">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('service-specializations.index') }}" class="btn btn-secondary">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
<!-- Internal Select2.min js -->
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
@endsection