@extends('layouts.master')
@section('title')
الاستشارية@stop
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">الاستشارية</span>
        </div>
    </div>

</div>
<!-- breadcrumb -->
@endsection
@section('content')

@if (session()->has('delete_invoice'))
<script>
    window.onload = function() {
        notif({
            msg: "تم حذف الفاتورة بنجاح",
            type: "success"
        })
    }
</script>
@endif


@if (session()->has('Status_Update'))
<script>
    window.onload = function() {
        notif({
            msg: "تم تحديث حالة الدفع بنجاح",
            type: "success"
        })
    }
</script>
@endif

@if (session()->has('restore_invoice'))
<script>
    window.onload = function() {
        notif({
            msg: "تم استعادة الفاتورة بنجاح",
            type: "success"
        })
    }
</script>
@endif


<!-- row -->
<div class="row">
    <!--div-->
    <div class="col-xl-12">
        <div class="card mg-b-20">
            <div class="card-header pb-0">

                <a href="cases/create" class="modal-effect btn btn-sm btn-primary" style="color:white"><i
                        class="fas fa-plus"></i>&nbsp; اضافة </a>


                @can('تصدير')
                <a class="modal-effect btn btn-sm btn-primary" href="{{ url('export_invoices') }}"
                    style="color:white"><i class="fas fa-file-download"></i>&nbsp;تصدير </a>

                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50' style="text-align: center">
                        <thead>
                            <tr>
                                <th class="border-bottom-0">دائرة الصحة </th>
                                <th class="border-bottom-0">اسم المستشفى</th>
                                <th class="border-bottom-0">شهر</th>
                                <th class="border-bottom-0">سنة</th>
                                <th class="border-bottom-0">الحالات المرضية في العيادات الاستشارية</th>
                                <th class="border-bottom-0">الحالات المرضية في العيادات الخافة او العامة</th>
                                <th class="border-bottom-0">الحالات المرضية في عيادات الطوارئ</th>
                                <th class="border-bottom-0">الفحوصات الساندة للمراجعين</th>
                                <th class="border-bottom-0">الفحوصات المختبرية للمراجعين</th>
                                <th class="border-bottom-0">الفحوصات الساندة للراقدين</th>
                                <th class="border-bottom-0">الفحوصات المختبرية للراقدين</th>
                                <th class="border-bottom-0">الاسرة في المخزن</th>
                                <th class="border-bottom-0">الاسرة في الأخرى</th>
                                <th class="border-bottom-0">الاسرة في الاستشاريات</th>
                                <th class="border-bottom-0">الاسرة في الردهات المغلقة</th>
                                <th class="border-bottom-0">عدد الاسرة الكلية المهيئة</th>
                                <th class="border-bottom-0"> عدد الاسرة الكلي</th>
                                <th class="border-bottom-0">الاسرة الادمالي </th>

                                <th class="border-bottom-0">ألعمليات</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach ($cases as $cases)
                            @php
                            $i++
                            @endphp
                            @php
                            $months = [
                            1 => 'كانون الثاني',
                            2 => 'شباط',
                            3 => 'اذار',
                            4 => 'نيسان',
                            5 => 'ايار',
                            6 => 'حزيران',
                            7 => 'تموز',
                            8 => 'أب',
                            9 => 'ايلول',
                            10 => 'تشرين الاول',
                            11 => 'تشرين الثاني',
                            12 => 'كانون الاول',
                            ];
                            @endphp
                            <tr>
                                <td>{{ $cases->moh ? $cases->moh->mohname : 'No Section' }}</td>
                                <td>{{ $cases->fck ? $cases->fck->Fckname  : 'No Section' }}</td>
                                <td>{{ $months[$cases->month] }}</td>
                                <td>{{ $cases->year }}</td>
                                <td>{{ $cases->caseest}}</td>
                                <td>{{ $cases->casekhaf}}</td>
                                <td>{{ $cases->caseesemer}}</td>
                                <td>{{ $cases->cheoutpationsan}}</td>
                                <td>{{ $cases->cheoutpationlab}}</td>
                                <td>{{ $cases->cheinpationsan}}</td>
                                <td>{{ $cases->cheinpationlab}}</td>
                                <td>{{ $cases->bedssav}}</td>
                                <td>{{ $cases->otherbed}}</td>
                                <td>{{ $cases->bedest}}</td>
                                <td>{{ $cases->bedrdhclose}}</td>
                                <td>{{ $cases->tbedsmoh}}</td>
                                <td>{{ $cases->totalbed}}</td>
                                <td>{{ $cases->tbedsf}}</td>




                                <td>
                                    <div class="dropdown">
                                        <button aria-expanded="false" aria-haspopup="true"
                                            class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                            type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                        <div class="dropdown-menu tx-13">

                                            <!-- رابط تعديل -->
                                            <a href="{{ route('cases.edit', $cases->id) }}" class="dropdown-item">تعديل العملية</a>

                                            <!-- نموذج حذف العملية بدون وضعه داخل <a> -->
                                            <div class="dropdown-item">
                                                <form action="{{ route('cases.destroy', $cases->id) }}" method="POST" style="display:inline;">
                                                    {{ csrf_field() }}
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>

                                </td>


                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>




<!-- ارشيف الفاتورة -->


</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
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
<script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>



<script>
    $('#Transfer_invoice').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var invoice_id = button.data('invoice_id')
        var modal = $(this)
        modal.find('.modal-body #invoice_id').val(invoice_id);
    })
</script>







@endsection