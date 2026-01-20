@extends('layouts.master')
@section('title')
ألاسرة المهيئة الغير موثقة
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
            <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> قائمة
 الاسرة الغير موثقة</span>
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
                    <table id="example1_nonapprove" class="table key-buttons text-md-nowrap" data-page-length='50' style="text-align: center">
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
                            @foreach($pations as $pation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pation->moh ? $pation->moh->mohname : 'No Section' }}</td>
                                <td>{{ $pation->fck ? $pation->fck->Fckname : 'No Section' }}</td>
                                <td>
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
                                    {{ $months[$pation->month] ?? '' }}
                                </td>
                                <td>{{ $pation->year }}</td>
                                <td>{{ $pation->rdhs ? $pation->rdhs->Spcuname : 'No Section' }}</td>
                                <td>{{ $pation->unitnum }}</td>
                                <td>{{ $pation->bedm }}</td>
                                <td>{{ $pation->outpationmon }}</td>
                                <td>{{ $pation->stayoutpation }}</td>
                                <td>{{ $pation->mkoth }}</td>
                                <td>{{ $pation->death }}</td>
                                <td>
                                    @if($pation->status_value == 1)
                                        <span class="badge badge-pill badge-success">{{ $pation->status }}</span>
                                    @elseif($pation->status_value == 2)
                                        <span class="badge badge-pill badge-danger">{{ $pation->status }}</span>
                                    @else
                                        <span class="badge badge-pill badge-warning">{{ $pation->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button">توثيق<i class="fas fa-caret-down ml-1"></i></button>
                                        <div class="dropdown-menu tx-13">
                                            @can('توثيق')
                                                <a class="dropdown-item" href="{{ URL::route('status_show', [$pation->id]) }}"><i class="text-success fas fa-money-bill"></i>&nbsp;&nbsp;تغيير حالة الاستمارة</a>
                                            @endcan
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
    $(document).ready(function() {
        $('#example1_nonapprove').DataTable({
            pageLength: 50,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json'
            }
        });
    });
</script>





@endsection