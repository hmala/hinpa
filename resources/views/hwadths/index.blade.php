@extends('layouts.master')
@section('title')
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
                <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    </span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

@if (session()->has('Add'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('Add') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
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
                    msg: "تم تحديث   بنجاح",
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
                   
                        <a href="hwadths/create" class="modal-effect btn btn-sm btn-primary" style="color:white"><i
                                class="fas fa-plus"></i>&nbsp; اضافة </a>
                  

                  
                        <a class="modal-effect btn btn-sm btn-primary" href="{{ url('export_invoices') }}"
                            style="color:white"><i class="fas fa-file-download"></i>&nbsp;تصدير اكسيل</a>
             

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered text-center" data-page-length='50'style="text-align: center">
                        <thead >
                        <tr>
            <th rowspan="2" class="border-bottom-0">دائرة الصحة</th>
            <th rowspan="2" class="border-bottom-0">اسم المستشفى</th>
            <th rowspan="2" class="border-bottom-0">شهر</th>
            <th rowspan="2" class="border-bottom-0">سنة</th>
            <th colspan="4" class="border-bottom-0">الولادات الحية (طبيعية)</th>
            <th colspan="4" class="border-bottom-0">الولادات الحية (قيصرية)</th>
            <th colspan="4" class="border-bottom-0">المجموع الكلي للولادات</th>
            <th colspan="4" class="border-bottom-0">الولادات الميتة (طبيعية)</th>
            <th colspan="4" class="border-bottom-0">الولادات الميتة (قيصرية)</th>
            <th colspan="4" class="border-bottom-0">الوفيات أقل من سنة (المرضى الراقدين)</th>
            <th colspan="4" class="border-bottom-0">الوفيات سنة فأكثر (المرضى الراقدين)</th>
            <th colspan="4" class="border-bottom-0">المجموع الكلي للوفيات</th>
            <th colspan="4" class="border-bottom-0">وفيات الأمهات</th>
            <th rowspan="2" class="border-bottom-0">وفيات  احيلت للطب العدلي</th>
            <th rowspan="2" class="border-bottom-0">العمليات</th>
        </tr>
        <tr>
            <!-- الصف الخاص بالعناوين الفرعية -->
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
            <th>ذكور</th>
            <th>إناث</th>
            <th>خنثى</th>
            <th>مجموع</th>
        </tr>


    </thead>

                            <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach ($hwadths as $hwadths)
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
                               
                                <td>{{ $hwadths->moh ? $hwadths->moh->mohname : 'No Section' }}</td>
                                <td>{{ $hwadths->fck ? $hwadths->fck->Fckname  : 'No Section' }}</td>
                                <td>{{ $months[$hwadths->month] }}</td>            
                                <td>{{ $hwadths->year }}</td>
                                <td>{{ $hwadths->livebnm}}</td>
                                <td>{{ $hwadths->livebnf}}</td>
                                <td>{{ $hwadths->livebnkh}}</td>
                                <td>{{ $hwadths->livebnt}}</td>

                                <td>{{ $hwadths->livebsm}}</td>
                                <td>{{ $hwadths->livebsf}}</td>
                                <td>{{ $hwadths->livebskh}}</td>
                                <td>{{ $hwadths->livebst}}</td>

                                <td>{{ $hwadths->totalbm}}</td>
                                <td>{{ $hwadths->totalbf}}</td>
                                <td>{{ $hwadths->totalbkh}}</td>
                                <td>{{ $hwadths->totalbt}}</td>

                                <td>{{ $hwadths->bdeadnm}}</td>
                                <td>{{ $hwadths->bdeadnf}}</td>
                                <td>{{ $hwadths->bdeadnkh}}</td>
                                <td>{{ $hwadths->bdeadnt}}</td>

                                <td>{{ $hwadths->bdeadsm}}</td>
                                <td>{{ $hwadths->bdeadsf}}</td>
                                <td>{{ $hwadths->bdeadskh}}</td>
                                <td>{{ $hwadths->bdeadst}}</td>

                                <td>{{ $hwadths->deadlm}}</td>
                                <td>{{ $hwadths->deadlf}}</td>
                                <td>{{ $hwadths->deadlkh}}</td>
                                <td>{{ $hwadths->deadlt}}</td>

                                <td>{{ $hwadths->deadmm}}</td>
                                <td>{{ $hwadths->deadmf}}</td>
                                <td>{{ $hwadths->deadmkh}}</td>
                                <td>{{ $hwadths->deadmt}}</td>

                                <td>{{ $hwadths->totaldm}}</td>
                                <td>{{ $hwadths->totaldf}}</td>
                                <td>{{ $hwadths->totaldkh}}</td>
                                <td>{{ $hwadths->totaldt}}</td>


                                <td>{{ $hwadths->mdeadm}}</td>
                                <td>{{ $hwadths->mdeadf}}</td>
                                <td>{{ $hwadths->mdeadkh}}</td>
                                <td>{{ $hwadths->mdeadt}}</td>
                                
                                <td>{{ $hwadths->deadtb}}</td>




                                
                                
                               
                              
                                <td>
                                <div class="dropdown">
                                                <button aria-expanded="false" aria-haspopup="true"
                                                    class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                                    type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                                <div class="dropdown-menu tx-13">
                                                   
                                                <a href="{{ route('hwadths.edit', $hwadths->id) }}" class="dropdown-item">تعديل العملية</a>
                                                            <a class="dropdown-item" href="#" data-invoice_id="{{ $hwadths->id }}"
                                                            data-toggle="modal" data-target="#delete_invoice"><i
                                                                class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;حذف
                                                            </a>

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
                <form action="{{ route('hwadths.destroy', 'test') }}" method="post">
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