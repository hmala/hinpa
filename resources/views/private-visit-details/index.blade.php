@extends('layouts.master')
@section('title')
تقرير حسابات ووصولات الزيارات الخارجية المعتمد
@stop
@section('css')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-header { display: block !important; }
        .print-footer { display: block !important; margin-top: 30px; page-break-inside: avoid; }
        body { font-size: 11px !important; background: #fff !important; color: #000 !important; }
        .table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
        .table th, .table td { padding: 4px 6px !important; border: 1px solid #333 !important; }
        .table thead th { background-color: #f1f5f9 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        @page { size: landscape; margin: 10mm; }
    }
    .print-header, .print-footer { display: none; }

    select option {
        color: #000 !important;
        background-color: #fff !important;
    }
    .stat-card {
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between no-print">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الزيارات الخارجية</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تقرير الزيارات المعتمد</span>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
    $visits = $visits ?? $receipts ?? collect();
    $totalVisitsCount = $totalVisitsCount ?? (method_exists($visits, 'total') ? $visits->total() : $visits->count());
    $totalCash = $totalCash ?? 0;
    $totalElectronic = $totalElectronic ?? 0;
    $grandTotalAmount = $grandTotalAmount ?? 0;
    $totalServiceFees = $totalServiceFees ?? 0;
    $availableMohs = $availableMohs ?? collect();
    $availableFcks = $availableFcks ?? collect();
    $doctors = $doctors ?? collect();
    $services = $services ?? collect();
    $dateFrom = $dateFrom ?? request('date_from', request('receipt_date_from', ''));
    $dateTo = $dateTo ?? request('date_to', request('receipt_date_to', ''));
@endphp
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- بطاقات الإحصائيات والمجاميع المالية للفترة المحددة -->
            <div class="row mb-3 no-print">
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-2">
                    <div class="card stat-card bg-primary text-white shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-white-50 tx-12 mb-1">إجمالي المرضى / الزيارات</h6>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalVisitsCount) }}</h3>
                            </div>
                            <div class="tx-30 text-white-50"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-2">
                    <div class="card stat-card bg-success text-white shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-white-50 tx-12 mb-1">المقبوضات النقدية (كاش)</h6>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalCash) }} <small class="tx-12">د.ع</small></h3>
                            </div>
                            <div class="tx-30 text-white-50"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-2">
                    <div class="card stat-card bg-info text-white shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-white-50 tx-12 mb-1">المقبوضات الإلكترونية (POS)</h6>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($totalElectronic) }} <small class="tx-12">د.ع</small></h3>
                            </div>
                            <div class="tx-30 text-white-50"><i class="fas fa-credit-card"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-2">
                    <div class="card stat-card bg-dark text-white shadow-sm h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-white-50 tx-12 mb-1">الإيراد الكلي الإجمالي</h6>
                                <h3 class="mb-0 font-weight-bold">{{ number_format($grandTotalAmount) }} <small class="tx-12">د.ع</small></h3>
                            </div>
                            <div class="tx-30 text-white-50"><i class="fas fa-chart-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 no-print border-bottom">
                    <h5 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-file-invoice-dollar me-2"></i> تقرير حسابات الزيارات والخدمات الخارجية
                    </h5>
                    <div>
                        <a href="{{ route('private-visit-details.export', request()->query()) }}" class="btn btn-success mr-2">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل معتمد
                        </a>
                        <a href="{{ route('private-visit-details.print', request()->query()) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i> طباعة كشف رسمي معتمد
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- الترويسة الرسمية الخاصة بالطباعة الرسمية -->
                    <div class="print-header mb-4 text-center">
                        <div class="row align-items-center pb-2 border-bottom">
                            <div class="col-4 text-right">
                                <h6 class="mb-1 font-weight-bold">جمهورية العراق</h6>
                                <h6 class="mb-1 font-weight-bold">وزارة الصحة</h6>
                                <p class="mb-0 text-muted tx-11">
                                    {{ request('moh_id') && isset($availableMohs) ? $availableMohs->where('id', request('moh_id'))->first()->mohname ?? 'دائرة الصحة' : (auth()->user()->moh->mohname ?? 'دائرة الصحة') }}
                                    -
                                    {{ request('fck_id') && isset($availableFcks) ? $availableFcks->where('id', request('fck_id'))->first()->Fckname ?? 'المستشفى' : (auth()->user()->fck->Fckname ?? 'المستشفى') }}
                                </p>
                            </div>
                            <div class="col-4 text-center">
                                <h4 class="font-weight-bold mb-1">كشف حسابات ووصولات الزيارات الخارجية</h4>
                                <h6 class="text-primary mb-0 font-weight-bold">
                                    @if($dateFrom || $dateTo)
                                        للفترة من: {{ $dateFrom ?: 'بداية التسجيل' }} إلى: {{ $dateTo ?: 'تاريخ اليوم' }}
                                    @else
                                        تقرير شامل لكافة الزيارات
                                    @endif
                                </h6>
                            </div>
                            <div class="col-4 text-left">
                                <p class="mb-1 tx-11"><strong>تاريخ الطباعة:</strong> {{ date('Y-m-d') }}</p>
                                <p class="mb-1 tx-11"><strong>وقت الطباعة:</strong> {{ date('H:i') }}</p>
                                <p class="mb-0 tx-11"><strong>المستخدم:</strong> {{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- نموذج فلترة التقرير حسب الفترة والمعايير -->
                    <form method="GET" action="{{ route('private-visit-details.index') }}" class="mb-4 no-print bg-light p-3 rounded border">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label font-weight-bold tx-12">من تاريخ</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                                       value="{{ request('date_from', request('receipt_date_from')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label font-weight-bold tx-12">إلى تاريخ</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                                       value="{{ request('date_to', request('receipt_date_to')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="patient_name" class="form-label font-weight-bold tx-12">اسم المريض / الزائر</label>
                                <input type="text" name="patient_name" id="patient_name" class="form-control form-control-sm"
                                       value="{{ request('patient_name') }}" placeholder="ابحث بالاسم">
                            </div>
                            <div class="col-md-3">
                                <label for="receipt_number" class="form-label font-weight-bold tx-12">رقم الوصل</label>
                                <input type="text" name="receipt_number" id="receipt_number" class="form-control form-control-sm"
                                       value="{{ request('receipt_number') }}" placeholder="رقم الوصل">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="payment_type" class="form-label font-weight-bold tx-12">طريقة الدفع</label>
                                <select name="payment_type" id="payment_type" class="form-control form-control-sm">
                                    <option value="">الكل (كاش وإلكتروني)</option>
                                    <option value="كاش" {{ request('payment_type') == 'كاش' ? 'selected' : '' }}>كاش</option>
                                    <option value="إلكتروني" {{ request('payment_type') == 'إلكتروني' ? 'selected' : '' }}>إلكتروني</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="doctor_id" class="form-label font-weight-bold tx-12">الطبيب</label>
                                <select name="doctor_id" id="doctor_id" class="form-control form-control-sm">
                                    <option value="">جميع الأطباء</option>
                                    @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="service_id" class="form-label font-weight-bold tx-12">الخدمة / الفحص</label>
                                <select name="service_id" id="service_id" class="form-control form-control-sm">
                                    <option value="">جميع الخدمات</option>
                                    @foreach($services as $srv)
                                    <option value="{{ $srv->id }}" {{ request('service_id') == $srv->id ? 'selected' : '' }}>{{ $srv->namesv }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php
                            $userRoles = is_array(auth()->user()->roles_name) ? auth()->user()->roles_name : (is_string(auth()->user()->roles_name) ? json_decode(auth()->user()->roles_name, true) : []);
                            $isAdmin = (is_array($userRoles) && in_array('Admin', $userRoles)) || auth()->user()->hasRole('Admin') || (is_array($userRoles) && in_array('mohst', $userRoles));
                        @endphp

                        <div class="row align-items-end">
                            @if($isAdmin)
                            <div class="col-md-3">
                                <label for="moh_id" class="form-label font-weight-bold tx-12">الدائرة</label>
                                <select name="moh_id" id="moh_id" class="form-control form-control-sm">
                                    <option value="">جميع الدوائر</option>
                                    @foreach($availableMohs as $moh)
                                    <option value="{{ $moh->id }}" {{ request('moh_id') == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if($isAdmin || (is_array($userRoles) && in_array('stat-doh', $userRoles)))
                            <div class="col-md-3">
                                <label for="fck_id" class="form-label font-weight-bold tx-12">المستشفى</label>
                                <select name="fck_id" id="fck_id" class="form-control form-control-sm">
                                    <option value="">جميع المستشفيات</option>
                                    @foreach($availableFcks as $fck)
                                    <option value="{{ $fck->id }}" data-moh-id="{{ $fck->moh_id }}" {{ request('fck_id') == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-md-2">
                                <label for="status" class="form-label font-weight-bold tx-12">حالة القيد</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">الكل</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال فقط</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي فقط</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex">
                                <button type="submit" class="btn btn-primary btn-sm mr-2 px-3">
                                    <i class="fas fa-filter me-1"></i> تصفية التقرير
                                </button>
                                @if(request()->hasAny(['patient_name', 'receipt_number', 'date_from', 'date_to', 'receipt_date_from', 'receipt_date_to', 'payment_type', 'moh_id', 'fck_id', 'doctor_id', 'service_id', 'status']))
                                <a href="{{ route('private-visit-details.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i> إعادة ضبط
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- جدول التقرير: صف معتمد كامل لكل مريض -->
                    <div class="table-responsive">
                       <table class="table table-bordered table-hover text-center align-middle" dir="rtl">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 3%;">ت</th>
                                    <th style="width: 6%;">الترقيم السنوي</th>
                                    <th style="width: 15%;">اسم المريض / الزائر</th>
                                    <th style="width: 8%;">الجنسية / الهاتف</th>
                                    <th style="width: 13%;">الخدمة المقدمة</th>
                                    <th style="width: 11%;">الطبيب المعالج</th>
                                    <th style="width: 9%;">تاريخ الزيارة</th>
                                    <th style="width: 8%;">رقم الوصل</th>
                                    <th style="width: 8%;">تاريخ الوصل</th>
                                    <th style="width: 6%;">طريقة الدفع</th>
                                    <th style="width: 6%;">الرسوم</th>
                                    <th style="width: 7%;">الإجمالي (د.ع)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pageServiceFeeTotal = 0;
                                    $pageGrandTotal = 0;
                                @endphp
                                @forelse($visits as $visit)
                                @php
                                    $serviceFee = (float)($visit->service_fee ?? 0);
                                    $totalAmt = (float)($visit->total_amount ?? $serviceFee);
                                    $pageServiceFeeTotal += $serviceFee;
                                    $pageGrandTotal += $totalAmt;
                                @endphp
                                <tr class="{{ $visit->cancelled_at ? 'table-danger' : '' }}">
                                    <td>{{ $loop->iteration + ($visits->currentPage() - 1) * $visits->perPage() }}</td>
                                    <td>
                                        <span class="badge badge-light border font-weight-bold">
                                            {{ $visit->annual_number ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold text-right">
                                        {{ $visit->visitor_name ?? $visit->patient_name ?? '-' }}
                                        @if($visit->cancelled_at)
                                            <span class="badge badge-danger mr-1">ملغي</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="tx-11">{{ $visit->visitor_nationality ?? $visit->patient_nationality ?? 'عراقي' }}</div>
                                        @if($visit->visitor_phone ?? $visit->patient_phone)
                                        <div class="tx-10 text-muted" dir="ltr">{{ $visit->visitor_phone ?? $visit->patient_phone }}</div>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-primary text-right">
                                        {{ $visit->serviceSpecialization ? $visit->serviceSpecialization->namesv : '-' }}
                                    </td>
                                    <td>{{ $visit->doctor ? $visit->doctor->name : '-' }}</td>
                                    <td>
                                        <div>{{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '-' }}</div>
                                        @if($visit->visit_time)
                                        <small class="text-muted">{{ is_string($visit->visit_time) ? substr($visit->visit_time, 0, 5) : $visit->visit_time->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $visit->receipt_number ?: '-' }}</span>
                                    </td>
                                    <td>
                                        {{ $visit->receipt_date ? $visit->receipt_date->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $visit->payment_type == 'كاش' ? 'success' : 'info' }}">
                                            {{ $visit->payment_type }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($serviceFee, 0) }}</td>
                                    <td class="font-weight-bold text-dark">
                                        {{ number_format($totalAmt, 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-1"></i> لا توجد بيانات مطابقة للفترة والمعايير المحددة
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($visits->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="10" class="text-left font-weight-bold">مجموع الصفحة الحالية:</td>
                                    <td class="font-weight-bold text-primary">{{ number_format($pageServiceFeeTotal, 0) }}</td>
                                    <td class="font-weight-bold text-success">{{ number_format($pageGrandTotal, 0) }} د.ع</td>
                                </tr>
                                <tr class="bg-dark text-white">
                                    <td colspan="10" class="text-left font-weight-bold text-white">المجموع الكلي للفترة المحددة ({{ number_format($totalVisitsCount) }} مريض):</td>
                                    <td class="font-weight-bold text-white">{{ number_format($totalServiceFees, 0) }}</td>
                                    <td class="font-weight-bold text-warning">{{ number_format($grandTotalAmount, 0) }} د.ع</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- الترقيم الصفحي -->
                    <div class="d-flex justify-content-between align-items-center mt-3 no-print">
                        <div class="text-muted tx-12">
                            عرض {{ $visits->firstItem() ?? 0 }} إلى {{ $visits->lastItem() ?? 0 }} من أصل {{ number_format($visits->total()) }} سجل
                        </div>
                        <div>
                            {{ $visits->links() }}
                        </div>
                    </div>

                    <!-- تذييل الاعتماد الرسمي للطباعة: منظم الكشف فقط -->
                    <div class="print-footer pt-4">
                        <div class="row">
                            <div class="col-4 mr-auto text-center">
                                <p class="mb-1 font-weight-bold tx-14">منظم الكشف</p>
                                <p class="mb-1 text-dark tx-12"><strong>الاسم:</strong> {{ auth()->user()->name }}</p>
                                <p class="mb-0 text-muted tx-12">التوقيع: .....................</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        var $mohSelect = $('#moh_id');
        var $fckSelect = $('#fck_id');
        
        if ($mohSelect.length && $fckSelect.length) {
            var allOptions = $fckSelect.find('option').clone();
            
            function filterHospitals() {
                var selectedMoh = $mohSelect.val();
                var currentSelectedFck = $fckSelect.val();
                
                $fckSelect.empty();
                
                allOptions.each(function() {
                    var optMohId = $(this).attr('data-moh-id');
                    if (!$(this).val() || !selectedMoh || optMohId == selectedMoh) {
                        $fckSelect.append($(this).clone());
                    }
                });
                
                if (currentSelectedFck && $fckSelect.find('option[value="' + currentSelectedFck + '"]').length) {
                    $fckSelect.val(currentSelectedFck);
                } else if (!selectedMoh) {
                    $fckSelect.val(currentSelectedFck || '');
                } else {
                    $fckSelect.val('');
                }
            }
            
            $mohSelect.on('change', filterHospitals);
            filterHospitals();
        }
    });
</script>
@endsection
