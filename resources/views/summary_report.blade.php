@extends('layouts.master')
@section('title')
التقرير التجميعي
@stop
@section('css')
<style>
    @page {
        size: A4 landscape;
        margin: 5mm;
    }

    .table {
        width: 100%;
        table-layout: fixed;
    }

    th, td {
        height: auto;
        max-width: 100px;
        font-size: 12px !important;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        .no-print {
            display: none;
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            padding: 5mm;
        }

        .table-container {
            margin-top: 30mm;
        }
    }
</style>
@endsection

@section('content')
<div class="row" id="print">
    <div class="col-xl-12">
        <div class="card">            <div class="card-header">
                <h3>التقرير التجميعي للمؤسسات الصحية</h3>
                @if(request('month') && request('year'))
                    <h4 class="text-primary mb-2">تقرير شهر {{ request('month') }} - {{ request('year') }}</h4>
                @endif
                @if(request('moh_id'))
                    <h5 class="text-success mb-3">{{ $mohs->find(request('moh_id'))->mohname ?? '' }}</h5>
                @else
                    <h5 class="text-success mb-3">تقرير جميع دوائر الصحة</h5>
                @endif
                
                <!-- نموذج الفلترة -->
                <form method="GET" action="{{ route('summary_report') }}" class="mb-4 no-print">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>دائرة الصحة:</label>
                                <select name="moh_id" class="form-control">
                                    <option value="">الكل</option>
                                    @foreach($mohs as $moh)
                                        <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>
                                            {{ $moh->mohname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>المؤسسة:</label>
                                <select name="fck_id" class="form-control">
                                    <option value="">الكل</option>
                                    @foreach($fck as $f)
                                        <option value="{{ $f->id }}" {{ request('fck_id') == $f->id ? 'selected' : '' }}>
                                            {{ $f->Fckname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        <div class="col-md-3">
                            <div class="form-group">
                                <label>الشهر:</label>
                                <select name="month" class="form-control">
                                    @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>السنة:</label>
                                <select name="year" class="form-control">
                                    @foreach(range(2024, 2025) as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">بحث</button>
                            </div>
                        </div>
                    </div>
                </form>

                <button class="btn btn-danger mt-3 mr-2 no-print" onclick="window.print()">
                    <i class="mdi mdi-printer ml-1"></i>طباعة
                </button>
            </div>

            <div class="card-body">
                <!-- جدول الأسرة -->
                <h4>ملخص الأسرة والوحدات</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المؤسسة</th>
                                <th>الاختصاص</th>
                                <th>مجموع الوحدات</th>
                                <th>مجموع الأسرة</th>
                                <th>مجموع الخارجين</th>
                                <th>مجموع الباقين</th>
                                <th>مجموع أيام المكوث</th>
                                <th>مجموع الوفيات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pations->groupBy('fck_id') as $fckId => $fckPations)
                                @foreach($fckPations as $pation)
                                <tr>
                                    <td>{{ $fck->find($fckId)->Fckname ?? 'غير محدد' }}</td>
                                    <td>{{ $rdhs->find($pation->spebed)->Spcuname ?? 'غير محدد' }}</td>
                                    <td>{{ $pation->total_unitnum }}</td>
                                    <td>{{ $pation->total_bedm }}</td>
                                    <td>{{ $pation->total_outpationmon }}</td>
                                    <td>{{ $pation->total_stayoutpation }}</td>
                                    <td>{{ $pation->total_mkoth }}</td>
                                    <td>{{ $pation->total_death }}</td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- جدول العمليات -->
                <h4>ملخص العمليات</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المؤسسة</th>
                                <th>مجموع العمليات الخاصة</th>
                                <th>مجموع العمليات فوق الكبرى</th>
                                <th>مجموع العمليات الكبرى</th>
                                <th>مجموع العمليات المتوسطة</th>
                                <th>مجموع العمليات الصغرى</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($surgery as $surg)
                            <tr>
                                <td>{{ $fck->find($surg->fck_id)->Fckname ?? 'غير محدد' }}</td>
                                <td>{{ $surg->total_khasa }}</td>
                                <td>{{ $surg->total_fkubra }}</td>
                                <td>{{ $surg->total_kubra }}</td>
                                <td>{{ $surg->total_mtws }}</td>
                                <td>{{ $surg->total_sugra }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- جدول الصالات -->
                <h4>ملخص الصالات</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المؤسسة</th>
                                <th>مجموع وحدات الصالات</th>
                                <th>مجموع أسرة الصالات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salat as $sal)
                            <tr>
                                <td>{{ $fck->find($sal->fck_id)->Fckname ?? 'غير محدد' }}</td>
                                <td>{{ $sal->total_salnum }}</td>
                                <td>{{ $sal->total_bsalnum }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- جدول الحوادث -->
                <h4>ملخص الحوادث والولادات</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المؤسسة</th>
                                <th>مجموع الولادات الطبيعية</th>
                                <th>مجموع الولادات القيصرية</th>
                                <th>المجموع الكلي للولادات</th>
                                <th>مجموع الولادات الميتة الطبيعية</th>
                                <th>مجموع الولادات الميتة القيصرية</th>
                                <th>مجموع الوفيات أقل من سنة</th>
                                <th>مجموع وفيات سنة فأكثر</th>
                                <th>المجموع الكلي للوفيات</th>
                                <th>مجموع وفيات الأمهات</th>
                                <th>مجموع الوفيات المحالة للطب العدلي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hwadth as $hw)
                            <tr>
                                <td>{{ $fck->find($hw->fck_id)->Fckname ?? 'غير محدد' }}</td>
                                <td>{{ $hw->total_livebnt }}</td>
                                <td>{{ $hw->total_livebst }}</td>
                                <td>{{ $hw->total_totalbt }}</td>
                                <td>{{ $hw->total_bdeadnt }}</td>
                                <td>{{ $hw->total_bdeadst }}</td>
                                <td>{{ $hw->total_deadlt }}</td>
                                <td>{{ $hw->total_deadmt }}</td>
                                <td>{{ $hw->total_totaldt }}</td>
                                <td>{{ $hw->total_mdeadt }}</td>
                                <td>{{ $hw->total_deadtb }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
