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
اضافة 
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
                <form action="{{ route('surgery.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col">
                            <label style="font-weight: bold;">الشهر</label>
                            <select class="form-control" name="month" required>
                                <option value="">اختر الشهر</option>
                                <option value="1">كانون الثاني</option>
                                <option value="2">شباط</option>
                                <option value="3">اذار</option>
                                <option value="4">نيسان</option>
                                <option value="5">ايار</option>
                                <option value="6">حزيران</option>
                                <option value="7">تموز</option>
                                <option value="8">اب</option>
                                <option value="9">ايلول</option>
                                <option value="10">تشرين الاول</option>
                                <option value="11">تشرين الثاني</option>
                                <option value="12">كانون الاول</option>
                            </select>
                        </div>
                        <div class="col">
                            <label style="font-weight: bold;">السنة</label>
                            <select class="form-control" name="year" required>
                                <option value="">اختر السنة</option>
                                @for ($year = date('Y'); $year >= 2024; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <table id="dataTableSurgery" class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاختصاص</th>
                                <th>الخاصة</th>
                                <th>فوق الكبرى</th>
                                <th>الكبرى</th>
                                <th>المتوسطة</th>
                                <th>الصغرى</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- يتم إدخال الصفوف ديناميكيًا -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" onclick="addRowSurgery()">إضافة صف جديد</button>

                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function addRowSurgery() {
        const table = document.querySelector("#dataTableSurgery tbody");
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${table.rows.length + 1}</td>
            <td>
                <select name="surgtyp[]" class="form-control" required>
                    <option value="" selected disabled>-- اختر الاختصاص --</option>
                    @foreach ($surg as $x)
                        <option value="{{ $x->id }}">{{ $x->surgname }}</option>
                    @endforeach
                </select><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small>
            </td>
            <td><input type="number" name="khasa[]" class="form-control" placeholder="عدد العمليات الخاصة" required><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small></td>
            <td><input type="number" name="fkubra[]" class="form-control" placeholder="عدد العمليات فوق الكبرى" required><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small></td>
            <td><input type="number" name="kubra[]" class="form-control" placeholder="عدد العمليات الكبرى" required><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small></td>
            <td><input type="number" name="mtws[]" class="form-control" placeholder="عدد العمليات المتوسطة" required><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small></td>
            <td><input type="number" name="sugra[]" class="form-control" placeholder="عدد العمليات الصغرى" required><small style="color: red; class="text-muted">يرجى عدم ترك الحقل فارغ  .</small></td>
            <td><button type="button" class="btn btn-danger" onclick="deleteRowSurgery(this)">حذف</button></td>
        `;
        table.appendChild(row);
    }

    function deleteRowSurgery(button) {
        const row = button.parentElement.parentElement;
        row.remove();
        updateRowNumbersSurgery();
    }

    function updateRowNumbersSurgery() {
        const rows = document.querySelectorAll("#dataTableSurgery tbody tr");
        rows.forEach((row, index) => {
            row.firstElementChild.textContent = index + 1;
        });
    }
</script>
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
    }); 
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

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>


@endsection

