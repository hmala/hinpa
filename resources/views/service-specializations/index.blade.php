@extends('layouts.master')
@section('title')
ربط الخدمات والتخصصات
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
            <h4 class="content-title mb-0 my-auto">إدارة الخدمات والتخصصات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة الربط</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mg-b-0">
                        <i class="fas fa-link text-primary mr-2"></i>
                        قائمة ربط الخدمات بالتخصصات
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('service-specializations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle ml-2"></i>
                            ربط خدمة جديدة
                        </a>
                        <a href="{{ url('service-specializations/import') }}" class="btn btn-success mr-2">
                            <i class="fas fa-file-excel ml-2"></i>
                            استيراد من Excel
                        </a>
                    </div>
                </div>
                <p class="tx-12 tx-gray-500 mb-2">قائمة بجميع ربط الخدمات مع التخصصات في النظام</p>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-check-circle ml-2"></i>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="example" class="table text-md-nowrap key-buttons">
                        <thead>
                            <tr>
                                <th class="border-bottom-0">الخدمة</th>
                                <th class="border-bottom-0">التخصص</th>
                                <th class="border-bottom-0">رمز الخدمة في التخصص</th>
                                <th class="border-bottom-0">اسم الخدمة في التخصص</th>
                                <th class="border-bottom-0">السعر</th>
                                <th class="border-bottom-0">ملاحظات</th>
                                <th class="border-bottom-0">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                @foreach ($service->typeSpecializations as $specialization)
                                    <tr>
                                        <td>
                                            <span class="text-dark">{{ $service->sername }}</span>
                                           
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $specialization->tsname }}</span>
                                           
                                        </td>
                                        <td><span class="badge badge-primary">{{ $specialization->pivot->codesv }}</span></td>
                                        <td>{{ $specialization->pivot->namesv }}</td>
                                        <td>{{ number_format($specialization->pivot->price, 2) }} د.ع</td>
                                        <td>{{ $specialization->pivot->notes ?: '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('service-specializations.edit', [$service->id, $specialization->id]) }}" 
                                                   class="btn btn-sm btn-primary" title="تعديل">
                                                   <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('service-specializations.destroy', [$service->id, $specialization->id]) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الربط؟');"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>

<!--Internal  Datatable js -->
<script>
    $(function() {
        $('#example').DataTable({            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json',
            },
            "ordering": true,
            "order": [[0, 'asc']], // ترتيب حسب رمز الخدمة
            "columnDefs": [{
                "targets": 0, // عمود الخدمة
                "orderData": [0], // ترتيب حسب الخدمة
                "type": "string"
            }],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "الكل"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: 'نسخ',
                    className: 'btn btn-sm btn-primary'
                },
                {
                    extend: 'excel',
                    text: 'تصدير Excel',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'print',
                    text: 'طباعة',
                    className: 'btn btn-sm btn-info'
                }
            ]
        });
    });
</script>
@endsection
