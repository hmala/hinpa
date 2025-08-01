@extends('layouts.master')
@section('title')
 ألاسرة المهيئةالنهائية
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
            <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ سجل الاسر المهيئة
 </span>
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
            msg: "تم حذف  بنجاح",
            type: "success"
        })
    }
</script>
@endif


@if (session()->has('Status_Update'))
<script>
    window.onload = function() {
        notif({
            msg: "تم تحديث   ",
            type: "success"
        })
    }
</script>
@endif

@if (session()->has('restore_invoice'))
<script>
    window.onload = function() {
        notif({
            msg: "تم استعادة  بنجاح",
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

            





            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50' style="text-align: center">
                        <thead>
                            <tr>
                                <th class="border-bottom-0">تسلسل</th>
                                <th class="border-bottom-0">دائرة الصحة </th>
                                <th class="border-bottom-0">اسم المستشفى</th>
                                <th class="border-bottom-0">شهر</th>
                                <th class="border-bottom-0">سنة</th>
                                <th class="border-bottom-0">اختصاص السرير </th>
                                <th class="border-bottom-0">عدد الوحدات</th>
                                <th class="border-bottom-0">عدد الاسرة المهيئة</th>
                                <th class="border-bottom-0">الخارجين خلال الشهر</th>
                                <th class="border-bottom-0">الباقين آخر الشهر</th>
                                <th class="border-bottom-0">أيام المكوث</th>
                                <th class="border-bottom-0">عدد الوفيات </th>
                                <th class="border-bottom-0">حالة الاستمارة</th>
                                <th class="border-bottom-0">ألعمليات</th>


                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach ($pations as $pations)
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
                                <td>{{ $i }}</td>
                                <td>{{ $pations->moh ? $pations->moh->mohname : 'No Section' }}</td>
                                <td>{{ $pations->fck ? $pations->fck->Fckname  : 'No Section' }}</td>
                                <td>{{ $months[$pations->month] }}</td>                                <td>{{ $pations->year }}</td>
                                <td>{{ $pations->rdhs ? $pations->rdhs->Spcuname  : 'No Section'}}</td>
                                <td>{{ $pations->unitnum}}</td>
                                <td>{{ $pations->bedm}}</td>
                                <td>{{ $pations->outpationmon}}</td>
                                <td>{{ $pations->stayoutpation}}</td>
                                <td>{{ $pations->mkoth}}</td>
                                <td>{{ $pations->death}}</td>
                                <td>
                                @if ($pations->status_value == 1)
                                                <span class="badge badge-pill badge-success">{{ $pations->status }}</span>
                                            @elseif($pations->status_value == 2)
                                                <span class="badge badge-pill badge-danger">{{ $pations->status }}</span>
                                            @else
                                                <span class="badge badge-pill badge-warning">{{ $pations->status }}</span>
                                            @endif 
                                </td>
                                <td>

                               
                                                
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

<!-- حذف الفاتورة -->
<div class="modal fade" id="delete_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">حذف </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <form action="{{ route('pations.destroy', 'test') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
            </div>
            <div class="modal-body">
                هل انت متاكد من عملية الحذف ؟
                <input type="hidden" name="invoice_id" id="invoice_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                <button type="submit" class="btn btn-danger">تاكيد</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- ارشيف الفاتورة -->
<div class="modal fade" id="Transfer_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ارشفة </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <form action="{{ route('pations.destroy', 'test') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
            </div>
            <div class="modal-body">
                هل انت متاكد من عملية الارشفة ؟
                <input type="hidden" name="invoice_id" id="invoice_id" value="">
                <input type="hidden" name="id_page" id="id_page" value="2">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                <button type="submit" class="btn btn-success">تاكيد</button>
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
    $('#delete_invoice').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var invoice_id = button.data('invoice_id')
        var modal = $(this)
        modal.find('.modal-body #invoice_id').val(invoice_id);
    })
</script>

<script>
    $('#Transfer_invoice').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var invoice_id = button.data('invoice_id')
        var modal = $(this)
        modal.find('.modal-body #invoice_id').val(invoice_id);
    })
</script>







@endsection