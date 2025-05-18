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
                                                                <form action="{{ route('cases.store') }}" method="POST" autocomplete="off">
                                                                    {{ csrf_field() }}

                                                                    {{-- 1 --}}
                                                                    <div class="col">
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <label style="font-weight: bold;">الشهر</label>
                                                                            <select class="form-control" id="month" name="month" required onchange="updateBeds()">
                                                                                <option value="" disabled selected>اختر الشهر</option>
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
                                                                            <select class="form-control" id="year" name="year" required onchange="updateBeds()">
                                                                            <option value="" disabled selected>اختر السنة</option>
                                                                                @for ($year = date('Y'); $year >= 2024; $year--)
                                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                                                @endfor
                                                                            </select>
                                                                        </div>
                                                                        <div class="col">
                                                                            <label for="tbedsmoh" class="form-label fw-bold">عدد الاسرة الكلية المهيئة</label>
                                                                            <input type="text" class="form-control" id="tbedsmoh" name="tbedsmoh"
                                                                                value="{{ $calculatedTotal ?? 'لا توجد بيانات' }}" readonly>

                                                                        </div>


                                                                    </div>


                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="caseest" class="form-label fw-bold">عدد الحالات المرضية في العيادات الاستشارية</label>
                                                                                <input type="text" class="form-control" id="caseest" name="caseest" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="casekhaf" class="form-label fw-bold">عدد الحالات المرضية في العيادات الخافة او العامة</label>
                                                                                <input type="text" class="form-control" id="casekhaf" name="casekhaf" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="caseesemer" class="form-label fw-bold">عدد الحالات المرضية في عيادات الطوارئ</label>
                                                                                <input type="text" class="form-control" id="caseesemer" name="caseesemer" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="cheoutpationsan" class="form-label fw-bold">عدد الفحوصات الساندة للمراجعين</label>
                                                                                <input type="text" class="form-control" id="cheoutpationsan" name="cheoutpationsan" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-3">
                                                                            <div class="col">
                                                                                <label for="cheoutpationlab" class="form-label fw-bold">عدد الفحوصات المختبرية للمراجعين</label>
                                                                                <input type="text" class="form-control" id="cheoutpationlab" name="cheoutpationlab" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="cheinpationsan" class="form-label fw-bold">عدد الفحوصات الساندة للراقدين</label>
                                                                                <input type="text" class="form-control" id="cheinpationsan" name="cheinpationsan" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="cheinpationlab" class="form-label fw-bold">عدد الفحوصات المختبرية للراقدين</label>
                                                                                <input type="text" class="form-control" id="cheinpationlab" name="cheinpationlab" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="bedssav" class="form-label fw-bold">عدد الاسرة في المخزن</label>
                                                                                <input type="text" class="form-control" id="bedssav" name="bedssav" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-3">
                                                                            <div class="col">
                                                                                <label for="otherbed" class="form-label fw-bold">عدد الاسرة في الأخرى</label>
                                                                                <input type="text" class="form-control" id="otherbed" name="otherbed" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="bedest" class="form-label fw-bold">عدد الاسرة في الاستشاريات</label>
                                                                                <input type="text" class="form-control" id="bedest" name="bedest" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="bedrdhclose" class="form-label fw-bold">عدد الاسرة في الردهات المغلقة</label>
                                                                                <input type="text" class="form-control" id="bedrdhclose" name="bedrdhclose" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mt-3">
                                                                            <div class="col">
                                                                                <label for="totalbed" class="form-label fw-bold">عدد الاسرة الكلي</label>
                                                                                <input type="text" class="form-control" id="totalbed" name="totalbed" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="tbedsf" class="form-label fw-bold">إجمالي الاسرة</label>
                                                                                <input type="text" class="form-control" id="tbedsf" name="tbedsf" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex justify-content-center mt-4">
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
<script>
    function updateBeds() {
        let month = document.getElementById("month").value;
        let year = document.getElementById("year").value;

        if (month && year) {
            fetch(`/cases/getBedsTotal/${month}/${year}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("tbedsmoh").value = data.totalBeds;
                })
                .catch(error => console.error("خطأ في جلب البيانات:", error));
        }
    }
</script>

<script>
    function calculateTotalBeds() {
        let tbedsmoh = parseInt(document.getElementById('tbedsmoh').value) || 0;
        let bedssav = parseInt(document.getElementById('bedssav').value) || 0;
        let otherbed = parseInt(document.getElementById('otherbed').value) || 0;
        let bedest = parseInt(document.getElementById('bedest').value) || 0;
        let bedrdhclose = parseInt(document.getElementById('bedrdhclose').value) || 0;

        let total = tbedsmoh + bedssav + otherbed + bedest + bedrdhclose;
        document.getElementById('tbedsf').value = total;
    }

    document.querySelectorAll("input[type='text']").forEach(input => {
        input.addEventListener("input", calculateTotalBeds);
    });
</script>

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



@endsection