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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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
                                                        <form action="{{ route('pations.store') }}" method="post" enctype="multipart/form-data" autocomplete="off">
    {{ csrf_field() }}
    <div class="row">
        <div class="col">
            <label style="font-weight: bold;">الشهر</label>
            <select class="form-control" id="month" name="month" required>
                <option value="">اختر الشهر</option>
                <option value="1">كانون الثاني</option>
                <option value="2">شباط</option>
                <option value="3">آذار</option>
                <option value="4">نيسان</option>
                <option value="5">أيار</option>
                <option value="6">حزيران</option>
                <option value="7">تموز</option>
                <option value="8">آب</option>
                <option value="9">أيلول</option>
                <option value="10">تشرين الأول</option>
                <option value="11">تشرين الثاني</option>
                <option value="12">كانون الأول</option>
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
    <br>
    <table id="dataTable" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>اختصاص السرير</th>
            <th>عدد الوحدات</th>
            <th>عدد الأسرّة المهيئة</th>
            <th>الخارجين خلال الشهر</th>
            <th>الباقين آخر الشهر</th>
            <th>أيام المكوث</th>
            <th>عدد الوفيات</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        <!-- يتم إدخال الصفوف ديناميكيًا -->
    </tbody>
</table>
<button type="button" onclick="addRow()">إضافة صف جديد</button>
<div class="d-flex justify-content-center mt-3">
    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
</div>

<script>
    // جدول قواعد الحساب لكل اختصاص
    const unitRules = {
        "طب الامراض الباطنية": 12,
        "طب الامراض القلبية": 8,
        "طب الاْمراض الصدرية": 8,
        "امراض الجهاز الهضمي والكبد": 8,
        "امراض الكلى": 8,
        "الكلية الاصطناعية": 8,
        "أنعاش القلب": 8,
        "طب الامراض النفسية": 8,
        "طب الامراض العقلية": 8,
        "الادمان": 8,
        "طب الامراض العصبية": 8,
        "الامراض الانتقالية (سارية وحميات)": 8,
        "فايروس كورونا (حالة مؤكدة)": 8,
        "فايروس كورونا (حالة مشتبهة او محتملة)": 8,
        "المفاصل والتأهيل الطبي": 8,
        "جلدية": 8,
        "امراض الدم النزفية": 8,
        "امراض الدم": 8,
        "معالجة الاورام": 8,
        "الطب النووي": 8,
        "اشعة تداخلية": 8,
        "الجراحة العامة": 12,
        "جراحة القلب": 8,
        "جراحة الصدر": 8,
        "الجراحة التجميلية والتقويمية": 8,
        "زرع الكلى": 8,
        "جراحة الجهاز الهضمي والكبد": 8,
        "الجراحة البولية": 8,
        "جراحة الكسور": 8,
        "الجراحة الاشعاعية": 8,
        "جراحة الاعصاب": 8,
        "جراحة العمود الفقري": 8,
        "جراحة الوجه والفكين": 8,
        "جراحة العيون": 8,
        "جراحة الحروق": 8,
        "الانف والاذن والحنجرة": 8,
        "العناية المركزة": 8,
        "التداخل القسطاري": 8,
        "جراحة الجملة العصبية": 8,
        "جراحة الاطفال": 8,
        "زراعة نخاع العظم": 8,
        "نسائية وتوليد": 12,
        "العقم او (الخصوبة)": 8,
        "الاطفال": 12,
        "الخدج وحديثي الولادة": 8,
        "الثلاسيميا": 8,
        "عناية مركز لحديثي الولادة": 5,
        "تأهيل تغذوي": 8,
        "عامة": 8,
        "الطوارئ": 10,
        "الجناح الخاص": 8
    };

    function addRow() {
        const table = document.querySelector("#dataTable tbody");
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${table.rows.length + 1}</td>
            <td>
                <select name="rdhs[]" class="choices form-control" required onchange="updateUnitRule(this)">
                    <option value="" selected disabled>-- اختر الاختصاص --</option>
                    ${Object.keys(unitRules).map(
                        key => `<option value="${key}">${key}</option>`
                    ).join('')}
                </select>
                <small style="color: red;" class="text-muted">يرجى اختيار اختصاص الردهة.</small>
            </td>
            <td>
                <input type="number" name="units[]" class="form-control" placeholder="عدد الوحدات" readonly>
                <small style="color: red;" class="text-muted">الحقل يتم ملؤه تلقائيًا.</small>
            </td>
            <td>
                <input type="number" name="beds[]" class="form-control" placeholder="عدد الأسرّة" required onchange="calculateUnits(this)">
                <small style="color: red;" class="text-muted">يرجى إدخال عدد الأسرة.</small>
            </td>
            <td><input type="number" name="paout[]" class="form-control" placeholder="الخارجين"></td>
            <td><input type="number" name="past[]" class="form-control" placeholder="الباقين"></td>
            <td><input type="number" name="mkoth[]" class="form-control" placeholder="أيام المكوث"></td>
            <td><input type="number" name="bde[]" class="form-control" placeholder="عدد الوفيات"></td>
            <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">حذف</button></td>
        `;
        table.appendChild(row);
    }

    function updateUnitRule(select) {
        const row = select.closest("tr");
        const selectedRdh = select.value; // الحصول على الاختصاص المختار
        const unitRule = unitRules[selectedRdh];
        const bedInput = row.querySelector("input[name='beds[]']");

        if (bedInput.value) {
            calculateUnits(bedInput, unitRule);
        }
    }

    function calculateUnits(input, unitRule = null) {
    const row = input.closest("tr"); // البحث عن الصف الأقرب
    const bedCount = parseInt(input.value); // عدد الأسرة
    const unitInput = row.querySelector("input[name='units[]']"); // حقل الوحدات
    const selectedRdh = row.querySelector("select[name='rdhs[]']").value; // اختيار الاختصاص

    // إذا لم يتم تمرير قاعدة الوحدة، اجلبها بناءً على الاختصاص
    if (!unitRule) {
        unitRule = unitRules[selectedRdh];
    }

    if (!isNaN(bedCount) && unitRule) {
        let units = Math.floor(bedCount / unitRule); // حساب الوحدات الأساسية
        const remainder = bedCount % unitRule;

        // إذا كان الباقي أكبر من نصف قاعدة الوحدة، إضافة وحدة إضافية
        if (remainder > unitRule / 2) {
            units += 1;
        }

        // تخصيص وحدة واحدة إذا كان عدد الأسرة أقل من قاعدة الوحدة ولكن أكبر من 0
        if (bedCount > 0 && bedCount < unitRule) {
            units = 1;
        }

        unitInput.value = units; // تحديث حقل الوحدات
    } else {
        unitInput.value = ""; // إعادة تعيين الحقل إذا كانت القيم غير صحيحة
    }
}

    function deleteRow(button) {
        const row = button.closest("tr");
        row.remove();
        updateRowNumbers();
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll("#dataTable tbody tr");
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
<script src="assets/vendors/choices.js/choices.min.js"></script>

<script>
    var date = $('.fc-datepicker').datepicker({
        dateFormat: 'yy'
    }).val();

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

    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>


@endsection

