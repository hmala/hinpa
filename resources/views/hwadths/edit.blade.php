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
    <script>
        // دالة عامة لحساب الولادات الطبيعية
        function calculateNaturalTotal() {
            const naturalMale = document.getElementById('livebnm').value || 0;
            const naturalFemale = document.getElementById('livebnf').value || 0;
            const naturalIntersex = document.getElementById('livebnkh').value || 0;

            const naturalTotal = parseInt(naturalMale) + parseInt(naturalFemale) + parseInt(naturalIntersex);
            document.getElementById('livebnt').value = naturalTotal;
            calculateFinalTotal(); // استدعاء لتحديث الإجمالي النهائي
        }

        // دالة عامة لحساب الولادات القيصرية
        function calculateCSectionTotal() {
            const cSectionMale = document.getElementById('livebsm').value || 0;
            const cSectionFemale = document.getElementById('livebsf').value || 0;
            const cSectionIntersex = document.getElementById('livebskh').value || 0;

            const cSectionTotal = parseInt(cSectionMale) + parseInt(cSectionFemale) + parseInt(cSectionIntersex);
            document.getElementById('livebst').value = cSectionTotal;
            calculateFinalTotal(); // استدعاء لتحديث الإجمالي النهائي
        }

        // دوال حساب إجماليات الذكور والإناث والخنثى
        function updateMaleTotal() {
            const naturalMale = document.getElementById('livebnm').value || 0;
            const cSectionMale = document.getElementById('livebsm').value || 0;

            const totalMale = parseInt(naturalMale) + parseInt(cSectionMale);
            document.getElementById('totalbm').value = totalMale;
        }

        function updateFemaleTotal() {
            const naturalFemale = document.getElementById('livebnf').value || 0;
            const cSectionFemale = document.getElementById('livebsf').value || 0;

            const totalFemale = parseInt(naturalFemale) + parseInt(cSectionFemale);
            document.getElementById('totalbf').value = totalFemale;
        }

        function updateKhonthaTotal() {
            const naturalIntersex = document.getElementById('livebnkh').value || 0;
            const cSectionIntersex = document.getElementById('livebskh').value || 0;

            const totalIntersex = parseInt(naturalIntersex) + parseInt(cSectionIntersex);
            document.getElementById('totalbkh').value = totalIntersex;
        }

        // دالة لتحديث الإجمالي النهائي
        function calculateFinalTotal() {
            const totalNatural = document.getElementById('livebnt').value || 0;
            const totalCSection = document.getElementById('livebst').value || 0;

            const finalTotal = parseInt(totalNatural) + parseInt(totalCSection);
            document.getElementById('totalbt').value = finalTotal;
        }
        // دالة عامة لحساب المجموع لأي مجموعة من الحقول
        // دالة عامة لحساب المجموع لأي مجموعة من الحقول
        // دالة عامة لحساب المجموع لأي مجموعة من الحقول
        function calculateTotal(...inputIds) {
            const outputId = inputIds.pop(); // آخر ID هو الحقل الإجمالي
            const total = inputIds.reduce((sum, id) => sum + (parseInt(document.getElementById(id).value) || 0), 0);
            document.getElementById(outputId).value = total;
            updateGenderTotals(); // تحديث إجمالي الذكور والإناث والخنثى
        }

        // دالة لحساب الإجماليات حسب النوع (ذكور، إناث، خنثى)
        function updateGenderTotals() {
            const totalMale =
               
                (parseInt(document.getElementById('deadlm').value) || 0) +
                (parseInt(document.getElementById('deadmm').value) || 0);

            const totalFemale =
               
                (parseInt(document.getElementById('deadlf').value) || 0) +
                (parseInt(document.getElementById('deadmf').value) || 0);

            const totalIntersex =
              
                (parseInt(document.getElementById('deadlkh').value) || 0) +
                (parseInt(document.getElementById('deadmkh').value) || 0);

            // تحديث الحقول الخاصة بالإجمالي لكل فئة
            document.getElementById('totaldm').value = totalMale;
            document.getElementById('totaldf').value = totalFemale;
            document.getElementById('totaldkh').value = totalIntersex;

            // تحديث الإجمالي الكلي
            const totalOverall = totalMale + totalFemale + totalIntersex;
            document.getElementById('totaldt').value = totalOverall;
        }
    </script>






    <!-- breadcrumb -->
@endsection
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('Add'))
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
                                            <li><a href="#tab4" class="nav-link active" data-toggle="tab">ألمعلومات
                                                    ألاساسية</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab4">
                                            <div class="table-responsive mt-15">

                                                <form method="POST" action="{{ route('hwadths.update', $hwadths->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" class="form-control" id="id" name="id" value="{{ $hwadths->id }}">
                                                    <div class="row">
        <div class="col">
            <label style="font-weight: bold;">الشهر</label>
            <select class="form-control" id="month" name="month" required>
            <option value="" disabled selected>اختر الشهر</option>
                                                                                <option value="1" {{ $hwadths->month == 1 ? 'selected' : '' }}>يناير</option>
                                                                                <option value="2" {{ $hwadths->month == 2 ? 'selected' : '' }}>فبراير</option>
                                                                                <option value="3" {{ $hwadths->month == 3 ? 'selected' : '' }}>مارس</option>
                                                                                <option value="4" {{ $hwadths->month == 4 ? 'selected' : '' }}>أبريل</option>
                                                                                <option value="5" {{ $hwadths->month == 5 ? 'selected' : '' }}>مايو</option>
                                                                                <option value="6" {{ $hwadths->month == 6 ? 'selected' : '' }}>يونيو</option>
                                                                                <option value="7" {{ $hwadths->month == 7 ? 'selected' : '' }}>يوليو</option>
                                                                                <option value="8" {{ $hwadths->month == 8 ? 'selected' : '' }}>أغسطس</option>
                                                                                <option value="9" {{ $hwadths->month == 9 ? 'selected' : '' }}>سبتمبر</option>
                                                                                <option value="10" {{ $hwadths->month == 10 ? 'selected' : '' }}>أكتوبر</option>
                                                                                <option value="11" {{ $hwadths->month == 11 ? 'selected' : '' }}>نوفمبر</option>
                                                                                <option value="12" {{ $hwadths->month == 12 ? 'selected' : '' }}>ديسمبر</option>
            </select>
        </div>
        <div class="col">
            <label style="font-weight: bold;">السنة</label>
            <select class="form-control" name="year" required>
                <option value="">اختر السنة</option>
                @for ($year = date('Y'); $year >= 2024; $year--)
                <option value="{{ $year }}" {{ isset($hwadths) && $hwadths->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>
                                                    <!-- الحقول داخل النموذج -->
                                                    <h7>الولادات الحية طبيعية</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <label for="livebnm">ذكور</label>
                                                            <input type="number" id="livebnm" name="livebnm"
                                                                class="form-control"
                                                                value="{{ $hwadths->livebnm }}"
                                                                oninput="updateMaleTotal(); calculateNaturalTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="livebnf">إناث</label>
                                                            <input type="number" id="livebnf" name="livebnf"
                                                                class="form-control"
                                                                  value="{{ $hwadths->livebnf }}"
                                                                oninput="updateFemaleTotal(); calculateNaturalTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="livebnkh">خنثى</label>
                                                            <input type="number" id="livebnkh" name="livebnkh"
                                                                class="form-control"
                                                                 value="{{ $hwadths->livebnf }}"
                                                                oninput="updateKhonthaTotal(); calculateNaturalTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="livebnt">إجمالي الطبيعية</label>
                                                            <input type="number" name="livebnt" id="livebnt" 
                                                            class="form-control"
                                                             value="{{ $hwadths->livebnf }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <h7>الولادات الحية قيصرية</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" id="livebsm" name="livebsm"
                                                                class="form-control"
                                                                 value="{{ $hwadths->livebnf }}"
                                                                oninput="updateMaleTotal(); calculateCSectionTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="livebsf" name="livebsf"
                                                                class="form-control"
                                                                 value="{{ $hwadths->livebnf }}"
                                                                oninput="updateFemaleTotal(); calculateCSectionTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="livebskh" name="livebskh"
                                                                class="form-control"
                                                                 value="{{ $hwadths->livebskh }}"
                                                                oninput="updateKhonthaTotal(); calculateCSectionTotal()"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="livebst" name="livebst"
                                                             class="form-control"
                                                              value="{{ $hwadths->livebst }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <h7>إجمالي الولادات</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" name="totalbm" id="totalbm" 
                                                            class="form-control"
                                                             value="{{ $hwadths->totalbm }}"
                                                                readonly>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="totalbf" id="totalbf"
                                                             class="form-control"
                                                              value="{{ $hwadths->totalbf }}"
                                                                readonly>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="totalbkh" id="totalbkh"
                                                             class="form-control"
                                                              value="{{ $hwadths->totalbkh }}"
                                                                readonly>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="totalbt" id="totalbt"
                                                             class="form-control"
                                                              value="{{ $hwadths->totalbt }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- الولادات الميتة طبيعية -->
                                                    <h7>الولادات الميتة طبيعية</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadnm" id="bdeadnm"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadnm }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadnm', 'bdeadnf', 'bdeadnkh', 'bdeadnt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadnf" id="bdeadnf" 
                                                            class="form-control"
                                                             value="{{ $hwadths->bdeadnf }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadnm', 'bdeadnf', 'bdeadnkh', 'bdeadnt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadnkh" id="bdeadnkh"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadnkh }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadnm', 'bdeadnf', 'bdeadnkh', 'bdeadnt')" required >
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadnt" id="bdeadnt"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadnt }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- الولادات الميتة قيصرية -->
                                                    <h7>الولادات الميتة قيصرية</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadsm" id="bdeadsm"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadsm }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadsm', 'bdeadsf', 'bdeadskh', 'bdeadst')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadsf" id="bdeadsf"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadsf }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadsm', 'bdeadsf', 'bdeadskh', 'bdeadst')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadskh" id="bdeadskh"
                                                             class="form-control"
                                                              value="{{ $hwadths->bdeadskh }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('bdeadsm', 'bdeadsf', 'bdeadskh', 'bdeadst')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="bdeadst" id="bdeadst" 
                                                            class="form-control"
                                                             value="{{ $hwadths->bdeadst }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- الوفيات أقل من سنة -->
                                                    <h7>الوفيات أقل من سنة</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadlm" id="deadlm"
                                                             class="form-control"
                                                              value="{{ $hwadths->deadlm }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadlm', 'deadlf', 'deadlkh', 'deadlt')"required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadlf" id="deadlf" 
                                                            class="form-control"
                                                             value="{{ $hwadths->deadlf }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadlm', 'deadlf', 'deadlkh', 'deadlt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadlkh" id="deadlkh" 
                                                            class="form-control"
                                                             value="{{ $hwadths->deadlkh }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadlm', 'deadlf', 'deadlkh', 'deadlt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadlt" id="deadlt" 
                                                            class="form-control"
                                                             value="{{ $hwadths->deadlt }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- الوفيات أكثر من سنة -->
                                                    <h7>الوفيات أكثر من سنة</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadmm" id="deadmm" 
                                                            class="form-control"
                                                             value="{{ $hwadths->deadmm }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadmm', 'deadmf', 'deadmkh', 'deadmt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadmf" id="deadmf" 
                                                            class="form-control"
                                                              value="{{ $hwadths->deadmf }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadmm', 'deadmf', 'deadmkh', 'deadmt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" name="deadmkh" id="deadmkh"
                                                             class="form-control"
                                                               value="{{ $hwadths->deadmkh }}"
                                                                placeholder="أدخل العدد"
                                                                oninput="calculateTotal('deadmm', 'deadmf', 'deadmkh', 'deadmt')" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number"  name="deadmt" id="deadmt" 
                                                            class="form-control"
                                                             value="{{ $hwadths->deadmt }}"
                                                                readonly>
                                                        </div>
                                                    </div>


                                                    <!--     المجموع الكلي للوفيات -->
                                                    <h7>المجموع الكلي للوفيات</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" id="totaldm" name="totaldm"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                  value="{{ $hwadths->totaldm }}"
                                                                oninput="calculateTotal('totaldm', 'totaldf', 'totaldkh', 'totaldt')"
                                                                readonly required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="totaldf" name="totaldf"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                  value="{{ $hwadths->totaldf }}"
                                                                oninput="calculateTotal('totalbm', 'totalbf', 'totalbkh', 'totalbt')"
                                                                readonly required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="totaldkh" name="totaldkh"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                value="{{ $hwadths->totaldkh }}"
                                                                oninput="calculateTotal('totaldm', 'totaldf', 'totaldkh', 'totaldt')"
                                                                readonly required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="totaldt" name="totaldt"
                                                                class="form-control"
                                                                value="{{ $hwadths->totaldt }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <!-- باقي الحقول -->
                                                    <h7>وفيات الأمهات</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" id="mdeadm" name="mdeadm"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                 value="{{ $hwadths->mdeadm }}"
                                                                oninput="calculateTotal('mdeadm', 'mdeadf', 'mdeadkh', 'mdeadt')"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="mdeadf" name="mdeadf"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                 value="{{ $hwadths->mdeadf }}"
                                                                oninput="calculateTotal('mdeadm', 'mdeadf', 'mdeadkh', 'mdeadt')"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="mdeadkh" name="mdeadkh"
                                                                class="form-control" placeholder="أدخل العدد"
                                                                 value="{{ $hwadths->mdeadkh }}"
                                                                oninput="calculateTotal('mdeadm', 'mdeadf', 'mdeadkh', 'mdeadt')"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number" id="mdeadt" name="mdeadt"
                                                                class="form-control" 
                                                                 value="{{ $hwadths->mdeadt }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <h7>وفيات الراقيدن التي احيلت الى الطب العدلي</h7>
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <input type="number" id="deadtb" name="deadtb"
                                                                class="form-control"
                                                                 value="{{ $hwadths->deadtb }}"
                                                                placeholder="أدخل العدد"
                                                               
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-primary btn-lg">حفظ
                                                                البيانات</button>
                                                        </div>
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


@endsection
