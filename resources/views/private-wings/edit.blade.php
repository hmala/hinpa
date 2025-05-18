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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>تعديل حساب الجناح الخاص</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('private-wings.update', $privateWing) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="hospital">المستشفى</label>
                                <input type="text" class="form-control" id="hospital" name="hospital" value="{{ $privateWing->hospital }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="health_department">دائرة صحة</label>
                                <input type="text" class="form-control" id="health_department" name="health_department" value="{{ $privateWing->health_department }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="patient_name">اسم المريض</label>
                                <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ $privateWing->patient_name }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="file_number">رقم الملف</label>
                                <input type="text" class="form-control" id="file_number" name="file_number" value="{{ $privateWing->file_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="statistical_number">الرقم الإحصائي</label>
                                <input type="text" class="form-control" id="statistical_number" name="statistical_number" value="{{ $privateWing->statistical_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="days_count">عدد الأيام</label>
                                <input type="number" class="form-control" id="days_count" name="days_count" value="{{ $privateWing->days_count }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="entry_date">تاريخ الدخول</label>
                                <input type="date" class="form-control" id="entry_date" name="entry_date" value="{{ $privateWing->entry_date }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exit_date">تاريخ الخروج</label>
                                <input type="date" class="form-control" id="exit_date" name="exit_date" value="{{ $privateWing->exit_date }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="patient_bed_fee">أجور رقود المريض</label>
                                <input type="number" step="0.01" class="form-control" id="patient_bed_fee" name="patient_bed_fee" value="{{ $privateWing->patient_bed_fee }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="companion_bed_fee">أجور رقود المرافق</label>
                                <input type="number" step="0.01" class="form-control" id="companion_bed_fee" name="companion_bed_fee" value="{{ $privateWing->companion_bed_fee }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nutrition_fee">أجور التغذية</label>
                                <input type="number" step="0.01" class="form-control" id="nutrition_fee" name="nutrition_fee" value="{{ $privateWing->nutrition_fee }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="medicine_supplies_fee">أجور الأدوية والمستلزمات</label>
                                <input type="number" step="0.01" class="form-control" id="medicine_supplies_fee" name="medicine_supplies_fee" value="{{ $privateWing->medicine_supplies_fee }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="laboratory_tests_fee">أجور الفحوص المختبرية</label>
                                <input type="number" step="0.01" class="form-control" id="laboratory_tests_fee" name="laboratory_tests_fee" value="{{ $privateWing->laboratory_tests_fee }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="xray_fees">أجور الفحوصات الشعاعية</label>
                                <input type="number" step="0.01" class="form-control" id="xray_fees" name="xray_fees" value="{{ $privateWing->xray_fees }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="sonar_fees">أجور فحوصات السونار</label>
                                <input type="number" step="0.01" class="form-control" id="sonar_fees" name="sonar_fees" value="{{ $privateWing->sonar_fees }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="deposit_amount">مبلغ التأمينات</label>
                                <input type="number" step="0.01" class="form-control" id="deposit_amount" name="deposit_amount" value="{{ $privateWing->deposit_amount }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="receipt_number">رقم الوصل</label>
                                <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="{{ $privateWing->receipt_number }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="receipt_date">تاريخ الوصل</label>
                                <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="{{ $privateWing->receipt_date }}" required>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">تحديث</button>
                            <a href="{{ route('private-wings.index') }}" class="btn btn-secondary">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
