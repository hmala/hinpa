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
                                                                <form action="{{ route('status_update', ['id' => $pations->id]) }}" method="POST" autocomplete="off"> {{ csrf_field() }}
                                                                    @method('PUT')
                                                                    {{-- 1 --}}
                                                                    <div class="col">
                                                                        <input type="hidden" class="form-control" id="id" name="id" value="{{ $pations->id }}">
                                                                    </div>
                                                                    <div class="row">

                                                                        <div class=s"col">
                                                                            <label style="font-weight: bold;">الشهر</label>
                                                                            <select class="form-control" id="month" name="month" required readonly>
                                                                                <option value="" disabled selected>اختر الشهر</option>
                                                                                <option value="1" {{ $pations->month == 1 ? 'selected' : '' }}>يناير</option>
                                                                                <option value="2" {{ $pations->month == 2 ? 'selected' : '' }}>فبراير</option>
                                                                                <option value="3" {{ $pations->month == 3 ? 'selected' : '' }}>مارس</option>
                                                                                <option value="4" {{ $pations->month == 4 ? 'selected' : '' }}>أبريل</option>
                                                                                <option value="5" {{ $pations->month == 5 ? 'selected' : '' }}>مايو</option>
                                                                                <option value="6" {{ $pations->month == 6 ? 'selected' : '' }}>يونيو</option>
                                                                                <option value="7" {{ $pations->month == 7 ? 'selected' : '' }}>يوليو</option>
                                                                                <option value="8" {{ $pations->month == 8 ? 'selected' : '' }}>أغسطس</option>
                                                                                <option value="9" {{ $pations->month == 9 ? 'selected' : '' }}>سبتمبر</option>
                                                                                <option value="10" {{ $pations->month == 10 ? 'selected' : '' }}>أكتوبر</option>
                                                                                <option value="11" {{ $pations->month == 11 ? 'selected' : '' }}>نوفمبر</option>
                                                                                <option value="12" {{ $pations->month == 12 ? 'selected' : '' }}>ديسمبر</option>
                                                                            </select>

                                                                        </div>

                                                                        <div class="col">
                                                                            <label style="font-weight: bold;">السنة</label>
                                                                            <select class="form-control" name="year" required readonly>
                                                                                <option value="" disabled selected>اختر السنة</option>
                                                                                @for ($year = date('Y'); $year >= 2024; $year--)
                                                                                <option value="{{ $year }}" {{ isset($pations) && $pations->year == $year ? 'selected' : '' }}>
                                                                                    {{ $year }}
                                                                                </option>
                                                                                @endfor
                                                                            </select>

                                                                        </div>

                                                                      
                                                                    </div>
                                                                    <div class="row">

                                                                        <div class="col">
                                                                            <label class="my-1 mr-2" for="inlineFormCustomSelectPref" style="font-weight: bold;">اختصاص السرير</label>
                                                                            <select name="rdhs" id="rdhs" class="form-control select2" required readonly>
                                                                                <option value="" selected disabled>--حدد الاختصاص--</option> <!-- قائمة البيانات المكررة هنا -->
                                                                                @foreach ($rdhs as $rdh)
                                                                                <option value="{{ $rdh->id }}" {{ isset($pations) && $pations->spebed  == $rdh->id ? 'selected' : '' }}>
                                                                                    {{ $rdh->Spcuname }}
                                                                                </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                    </div>

                                                                    {{-- 2 --}}
                                                                    <div class="row">

                                                                        <div class="col">
                                                                            <label for="inputName" class="control-label" style="font-weight: bold;"> عدد الوحدات</label>
                                                                            <input type="text" class="form-control" id="unit" name="unit" value="{{ $pations->unitnum }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>

                                                                        <div class="col">
                                                                            <label style="font-weight: bold;" for="inputName" class="control-label"> عدد الاسرة المهيئة </label>
                                                                            <input type="text" class="form-control" id="beds" name="beds" value="{{ $pations->bedm }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label style="font-weight: bold;" for="ouyputName" class="control-label"> الخارجين خلال الشهر</label>
                                                                            <input type="text" class="form-control" id="paout" name="paout" value="{{ $pations->outpationmon }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label style="font-weight: bold;" for="inputName" class="control-label"> الباقين آخر الشهر</label>
                                                                            <input type="text" class="form-control" id="past" name="past" value="{{ $pations->stayoutpation }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label style="font-weight: bold;" for="inputName" class="control-label"> أيام المكوث</label>
                                                                            <input type="text" class="form-control" id="mkoth" name="mkoth" value="{{ $pations->mkoth }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label style="font-weight: bold;" for="inputName" class="control-label"> عدد الوفيات</label>
                                                                            <input type="text" class="form-control" id="bde" name="bde" value="{{ $pations->death }}" readonly
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                                        </div>

                                                                    </div>



                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <label for="Status">حالة التوثيق</label>
                                                                            <select class="form-control" id="Status" name="Status" required>
                                                                                <option value="" disabled selected>-- حدد حالة التوثيق --</option>
                                                                                <option value="موثق">موثق</option>
                                                                                <option value="غير موثق">غير موثق</option>
                                                                            </select>
                                                                            <small id="errorMsg" style="color: red; display: none;">⚠️ الرجاء اختيار حالة التوثيق!</small>
                                                                        </div>





                                                                    </div><br>
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <label for="exampleTextarea">ملاحظات</label>
                                                                            <textarea class="form-control" id="exampleTextarea" name="note" rows="3">
                                                                            {{ $pations->note }}</textarea>
                                                                        </div>
                                                                    </div><br>
                                                                    <div class="d-flex justify-content-center">
                                                                        <button type="submit" class="btn btn-primary">تحديث حالة التوثيق</button>
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