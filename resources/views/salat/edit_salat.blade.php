@extends('layouts.master')
@section('css')
<!--- Internal Select2 css-->
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!---Internal Fileupload css-->
<link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
<!---Internal Fancy uploader css-->
<link href="{{ URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
<!--Internal Sumoselect css-->
<link rel="stylesheet" href="{{ URL::asset('assets/plugins/sumoselect/sumoselect-rtl.css') }}">
<!--Internal  TelephoneInput css-->
<link rel="stylesheet" href="{{ URL::asset('assets/plugins/telephoneinput/telephoneinput-rtl.css') }}">
@endsection
@section('title')
اضافة فاتورة
@stop

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                اضافة فاتورة</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session()->has('Add'))
<div class="alert alert-success">
    {{ session()->get('Add') }}
</div>
@endif
@if (session()->has('edit'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('edit') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- row -->

<div class="row row-sm">

    <div class="col-xl-12">
        <!-- div -->
        <div class="card mg-b-20" id="tabs-style2">
            <div class="card-body">
                <div class="text-wrap">
                    <div class="example">
                        <div class="panel panel-primary tabs-style-2">
                            <div class=" tab-menu-heading">
                                <div class="tabs-menu1">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs main-nav-line">
                                        <li><a href="#tab4" class="nav-link active" data-toggle="tab">ألمعلومات ألاساسية</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body tabs-menu-body main-content-body-right border">
                                <div class="tab-content">

                                    <div class="tab-pane active" id="tab4">
                                        <div class="table-responsive mt-15">
                                            <div class="row">

                                                <div class="col-lg-12 col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div id="formRows">
                                                                <form action="{{ route('salat.update') }}" method="post" autocomplete="off">
                                                                    {{ csrf_field() }}
                                                                    @method('PUT')
                                                                    {{-- 1 --}}
                                                                    <div class="col">
                                                                    <input type="hidden" class="form-control" id="id" name="id" value="{{ $salat->id }}">
                                                                    </div>
                                                                    <div class="row">
                                                                    <div class="col">
                                                                        <label style="font-weight: bold;">الشهر</label>
                                                                        <select class="form-control" id="month" name="month" required>
                                                                                <option value="" disabled selected>اختر الشهر</option>
                                                                                <option value="1" {{ $salat->month == 1 ? 'selected' : '' }}>كانون الثاني</option>
                                                                                <option value="2" {{ $salat->month == 2 ? 'selected' : '' }}>شباط</option>
                                                                                <option value="3" {{ $salat->month == 3 ? 'selected' : '' }}>اذار</option>
                                                                                <option value="4" {{ $salat->month == 4 ? 'selected' : '' }}>نيسان</option>
                                                                                <option value="5" {{ $salat->month == 5 ? 'selected' : '' }}>ايار</option>
                                                                                <option value="6" {{ $salat->month == 6 ? 'selected' : '' }}>حزيران</option>
                                                                                <option value="7" {{ $salat->month == 7 ? 'selected' : '' }}>تموز</option>
                                                                                <option value="8" {{ $salat->month == 8 ? 'selected' : '' }}>اب</option>
                                                                                <option value="9" {{ $salat->month == 9 ? 'selected' : '' }}>ايلول</option>
                                                                                <option value="10" {{ $salat->month == 10 ? 'selected' : '' }}>تشرين الاول</option>
                                                                                <option value="11" {{ $salat->month == 11 ? 'selected' : '' }}>تشرين الثاني</option>
                                                                                <option value="12" {{ $salat->month == 12 ? 'selected' : '' }}>كانون الاول</option>
                                                                            </select>

                                                                    </div>

                                                                    <div class="col">
                                                                        <label style="font-weight: bold;">السنة</label>
                                                                        <select class="form-control" name="year" required>
                                                                                <option value="" disabled selected>اختر السنة</option>
                                                                                @for ($year = date('Y'); $year >= 2024; $year--)
                                                                                <option value="{{ $year }}" {{ isset($salat) && $salat->year == $year ? 'selected' : '' }}>
                                                                                    {{ $year }}
                                                                                </option>
                                                                                @endfor
                                                                            </select>
                                                                    </div>

                                                                   
                                                                </div>
                                                              

                                                               
                                                                <div class="row">

                                                                <div class="col">
                                                                        <label class="my-1 mr-2" for="operationType" style="font-weight: bold;">الصالات </label> 
                                                                        <select name="salid" id="salid" class="form-control select2" required>
                                                                            <option value="" selected disabled>--حدد الاختصاص--</option> <!-- قائمة البيانات المكررة هنا -->
                                                                            @foreach ($salsur as $salsur)
                                                                            <option value="{{ $salsur->id}}" {{ isset($salat) && $salat->salid  == $salsur->id ? 'selected' : '' }}>
                                                                                {{ $salsur->salsur }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col">
                                                                        <label style="font-weight: bold;" for="inputName" class="control-label"> عدد  الصالات </label>
                                                                        <input type="text" class="form-control" id="salnum" name="salnum"  value="{{ $salat->salnum }}"
                                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                    </div>
                                                                    <div class="col">
                                                                        <label style="font-weight: bold;" for="inputName" class="control-label"> عدد أسرة الصالات  </label>
                                                                        <input type="text" class="form-control" id="bsalnum" name="bsalnum"  value="{{ $salat->bsalnum }}"
                                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                    </div>
                                                                   

                                                                   

                                                                </div>
                                                                <br>
                                                                <div class="d-flex justify-content-center">
                                                                        <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                                                                    </div>

                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /div -->
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

<!-- Internal Select2 js-->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!--Internal Fileuploads js-->
<script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
<!--Internal Fancy uploader js-->
<script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
<!--Internal  Form-elements js-->
<script src="{{ URL::asset('assets/js/advanced-form-elements.js') }}"></script>
<script src="{{ URL::asset('assets/js/select2.js') }}"></script>
<!--Internal Sumoselect js-->
<script src="{{ URL::asset('assets/plugins/sumoselect/jquery.sumoselect.js') }}"></script>
<!--Internal  Datepicker js -->
<script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<!--Internal  jquery.maskedinput js -->
<script src="{{ URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
<!--Internal  spectrum-colorpicker js -->
<script src="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
<!--Internal  Datepicker js -->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!-- Internal Jquery.mCustomScrollbar js-->
<script src="{{ URL::asset('assets/plugins/custom-scroll/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<!-- Internal Input tags js-->
<script src="{{ URL::asset('assets/plugins/inputtags/inputtags.js') }}"></script>
<!--- Tabs JS-->
<script src="{{ URL::asset('assets/plugins/tabs/jquery.multipurpose_tabcontent.js') }}"></script>
<script src="{{ URL::asset('assets/js/tabs.js') }}"></script>
<!--Internal  Clipboard js-->
<script src="{{ URL::asset('assets/plugins/clipboard/clipboard.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/clipboard/clipboard.js') }}"></script>
<!-- Internal Prism js-->
<script src="{{ URL::asset('assets/plugins/prism/prism.js') }}"></script>

<script>
    var date = $('.fc-datepicker').datepicker({
        dateFormat: 'yy'
    }).val();
</script>

<script>
    $(document).ready(function() {
        $('select[name="Section"]').on('change', function() {
            var SectionId = $(this).val();
            if (SectionId) {
                $.ajax({
                    url: "{{ URL::to('section') }}/" + SectionId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="product"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="product"]').append('<option value="' +
                                value + '">' + value + '</option>');
                        });
                    },
                });

            } else {
                console.log('AJAX load did not work');
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('#rdhs').select2();
    }); <
    script >
        function myFunction() {

            var Amount_Commission = parseFloat(document.getElementById("Amount_Commission").value);
            var Discount = parseFloat(document.getElementById("Discount").value);
            var Rate_VAT = parseFloat(document.getElementById("Rate_VAT").value);
            var Value_VAT = parseFloat(document.getElementById("Value_VAT").value);

            var Amount_Commission2 = Amount_Commission - Discount;

            if (typeof Amount_Commission === 'undefined' || !Amount_Commission) {

                alert('يرجي ادخال مبلغ العمولة ');

            } else {
                var intResults = Amount_Commission2 * Rate_VAT / 100;

                var intResults2 = parseFloat(intResults + Amount_Commission2);

                sumq = parseFloat(intResults).toFixed(2);

                sumt = parseFloat(intResults2).toFixed(2);

                document.getElementById("Value_VAT").value = sumq;

                document.getElementById("Total").value = sumt;

            }

        }
</script>

<script>
    $('#delete_file').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_file = button.data('id_file')
        var file_name = button.data('file_name')
        var invoice_number = button.data('invoice_number')
        var modal = $(this)

        modal.find('.modal-body #id_file').val(id_file);
        modal.find('.modal-body #file_name').val(file_name);
        modal.find('.modal-body #invoice_number').val(invoice_number);
    })
</script>

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>


@endsection