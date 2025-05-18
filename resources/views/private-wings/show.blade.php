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
                    <h2>تفاصيل حساب الجناح الخاص</h2>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>المستشفى:</strong>
                            <p>{{ $privateWing->hospital }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>دائرة صحة:</strong>
                            <p>{{ $privateWing->health_department }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>اسم المريض:</strong>
                            <p>{{ $privateWing->patient_name }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>رقم الملف:</strong>
                            <p>{{ $privateWing->file_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>الرقم الإحصائي:</strong>
                            <p>{{ $privateWing->statistical_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>عدد الأيام:</strong>
                            <p>{{ $privateWing->days_count }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>تاريخ الدخول:</strong>
                            <p>{{ $privateWing->entry_date }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الخروج:</strong>
                            <p>{{ $privateWing->exit_date }}</p>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">الأجور والرسوم</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>أجور رقود المريض</th>
                                    <td>{{ $privateWing->patient_bed_fee }}</td>
                                    <th>أجور رقود المرافق</th>
                                    <td>{{ $privateWing->companion_bed_fee }}</td>
                                </tr>
                                <tr>
                                    <th>أجور التغذية</th>
                                    <td>{{ $privateWing->nutrition_fee }}</td>
                                    <th>أجور الأدوية والمستلزمات</th>
                                    <td>{{ $privateWing->medicine_supplies_fee }}</td>
                                </tr>
                                <tr>
                                    <th>أجور الفحوص المختبرية</th>
                                    <td>{{ $privateWing->laboratory_tests_fee }}</td>
                                    <th>أجور الفحوصات الشعاعية</th>
                                    <td>{{ $privateWing->xray_fees }}</td>
                                </tr>
                                <tr>
                                    <th>أجور فحوصات السونار</th>
                                    <td>{{ $privateWing->sonar_fees }}</td>
                                    <th>مبلغ التأمينات</th>
                                    <td>{{ $privateWing->deposit_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <strong>رقم الوصل:</strong>
                            <p>{{ $privateWing->receipt_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الوصل:</strong>
                            <p>{{ $privateWing->receipt_date }}</p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('private-wings.edit', $privateWing) }}" class="btn btn-primary">تعديل</a>
                        <a href="{{ route('private-wings.index') }}" class="btn btn-secondary">رجوع</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
