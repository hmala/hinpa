@extends('layouts.master')

@section('title')
إضافة مستخدم جديد
@stop

@section('css')
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المستخدمين</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة مستخدم جديد</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection


@section('content')
@php $isAdmin = auth()->check() && in_array('Admin', auth()->user()->roles_name ?? []); @endphp

@if($isAdmin)
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>خطأ!</strong> يوجد مشاكل في المدخلات.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إضافة مستخدم جديد</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'users.store', 'method' => 'POST']) !!}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>اسم المستخدم <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['placeholder' => 'اسم المستخدم', 'class' => 'form-control', 'required']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                                {!! Form::email('email', '', ['placeholder' => 'البريد الإلكتروني', 'class' => 'form-control', 'required', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>كلمة المرور <span class="text-danger">*</span></label>
                                {!! Form::password('password', ['placeholder' => 'كلمة المرور', 'class' => 'form-control', 'required']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                {!! Form::password('confirm-password', ['placeholder' => 'تأكيد كلمة المرور', 'class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الدائرة الصحية <span class="text-danger">*</span></label>
                                <select name="moh_id" id="moh_id" class="form-control select2" required>
                                    <option value="">اختر الدائرة</option>
                                    @foreach($mohs as $moh)
                                        <option value="{{ $moh->id }}">{{ $moh->mohname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المؤسسة <span class="text-danger">*</span></label>
                                <select name="fck_id" id="fck_id" class="form-control select2" required>
                                    <option value="">اختر المؤسسة</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الصلاحية <span class="text-danger">*</span></label>
                                {!! Form::select('roles[]', $roles, [], ['class' => 'form-control select2', 'required']) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>حالة المستخدم <span class="text-danger">*</span></label>
                                <select name="Status" class="form-control" required>
                                    <option value="مفعل">مفعل</option>
                                    <option value="غير مفعل">غير مفعل</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> رجوع</a>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-danger mt-5 text-center">
        <h4>غير مصرح لك بالوصول لهذه الصفحة.</h4>
    </div>
@endif
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        // عند تغيير الدائرة، تحديث قائمة المؤسسات
        $('#moh_id').on('change', function() {
            var mohId = $(this).val();
            if (mohId) {
                $.ajax({
                    url: '{{ route("getInstitutions") }}',
                    type: 'POST',
                    data: {
                        moh_id: mohId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $('#fck_id').empty();
                        $('#fck_id').append('<option value="">اختر المؤسسة</option>');
                        $.each(data, function(key, value) {
                            $('#fck_id').append('<option value="' + value.id + '">' + value.Fckname + '</option>');
                        });
                    }
                });
            } else {
                $('#fck_id').empty();
                $('#fck_id').append('<option value="">اختر المؤسسة</option>');
            }
        });
    });
</script>
@endsection