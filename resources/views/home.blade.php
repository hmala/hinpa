@extends('layouts.master')
@section('title')
الواجهة الرئيسية
@stop

@section('css')
<!-- إضافة خط Cairo -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
    .chart-container {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        width: 100% !important;
        height: 500px !important;
        position: relative;
    }
	#calendar-container {
		max-height: 600px;
		overflow-y: auto;
	}
</style>

<style>
	@media print {
		#print_Button {
			display: none;
		}
	}

    .chart-content {
        margin: 20px;
        padding: 15px;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .chart-container {
        min-height: 400px;
        margin-bottom: 20px;
    }
</style>
<!--  Owl-carousel css-->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .table-stats {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        direction: rtl;
        font-family: 'Cairo', sans-serif;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        background: white;
    }
    
    .table-stats thead {
        background: #f8f9fa;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table-stats th,
    .table-stats td {
        padding: 15px 20px;
        text-align: right;
        border: 1px solid #dee2e6;
        background: white;
    }
    
    .table-stats th {
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }
    
    .table-stats tbody tr {
        background: white;
        transition: all 0.3s ease;
    }
    
    .table-stats tbody tr:hover {
        background: #f8f9fa;
    }
    
    @media screen and (max-width: 600px) {
        .table-stats {
            font-size: 14px;
        }
        
        .table-stats th,
        .table-stats td {
            padding: 8px 10px;
        }
    }
    
    .card-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
        color: #333;
    }

    @media print {
        /* تنسيق عام للطباعة */
        @page {
            size: landscape A4;
            margin: 0.5cm;
        }

        /* تنسيق المؤشرات */
        .row-sm {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            page-break-inside: avoid !important;
        }

        .col-xl-2 {
            width: 16% !important;
            flex: 0 0 16% !important;
            max-width: 16% !important;
        }

        /* تنسيق البطاقات */
        .card {
            border: 1px solid #ddd !important;
            margin: 5px !important;
            page-break-inside: avoid !important;
        }

        .sales-card {
            background: #f8f9fa !important;
            color: #000 !important;
        }

        .text-white {
            color: #000 !important;
        }

        /* تنسيق المخطط */
        .chart-container {
            width: 100% !important;
            height: 500px !important;
            margin: 0 !important;
            page-break-before: always !important;
        }

        .chart-content {
            page-break-inside: avoid !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        canvas {
            max-width: 100% !important;
            height: 100% !important;
        }

        /* تنسيق النص في المخطط */
        .chart-title {
            font-size: 16px !important;
            margin-bottom: 10px !important;
        }

        /* إخفاء العناصر غير المطلوبة */
        .pagination,
        .btn,
        #calendar-container,
        .no-print {
            display: none !important;
        }

        /* تنسيق الجدول */
        .table-stats {
            page-break-inside: avoid !important;
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        
        .table-stats th {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        .table-stats td {
            border: 1px solid #ddd;
            font-size: 12px;
        }

        /* تنسيق العنوان */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
        }

        /* إخفاء عناصر غير ضرورية */
        form,
        select,
        .form-group {
            display: none !important;
        }
    }

    @media screen and (max-width: 768px) {
        .table-stats {
            font-size: 14px;
        }
        
        .table-stats th, .table-stats td {
            padding: 8px 10px;
        }
        
        .card-title {
            font-size: 18px;
        }
    }

    /* تنسيق البطاقات الإحصائية */
    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        border-radius: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    
    .opacity-75 {
        opacity: 0.8;
    }
    
    /* تنسيق أشرطة التقدم */
    .progress {
        border-radius: 25px;
        overflow: hidden;
        background-color: rgba(0,0,0,0.1);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .progress-bar {
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 11px;
        line-height: 25px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    
    /* تلوين الصفوف */
    .table-success {
        background: #d4edda !important;
        border-left: 4px solid #28a745;
    }
    
    .table-warning {
        background: #fff3cd !important;
        border-left: 4px solid #ffc107;
    }
    
    .table-info {
        background: #d1ecf1 !important;
        border-left: 4px solid #17a2b8;
    }
    
    .table-danger {
        background: #f8d7da !important;
        border-left: 4px solid #dc3545;
    }
    
    /* تأثير hover على الصفوف */
    .table-stats tbody tr {
        transition: all 0.3s ease;
        border-radius: 0;
        margin-bottom: 0;
    }
    
    .table-stats tbody tr:hover {
        background: #e9ecef !important;
    }

    /* تحسين البطاقات الإحصائية السريعة */
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 25px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        border-radius: 15px;
    }
    
    .stats-card .card-body {
        position: relative;
        z-index: 1;
    }
    
    .stats-card i {
        opacity: 0.9;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }
    
    .stats-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .stats-card h6 {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 8px;
        font-weight: 500;
    }

    /* تنسيق أزرار التنقل */
    .pagination {
        direction: rtl;
        padding: 0;
        margin: 20px 0;
        font-family: 'Cairo', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }
    
    .pagination li {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination li a,
    .pagination li span {
        min-width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        color: #333;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .pagination li.active span {
        background-color: #0162e8;
        color: #fff;
        border-color: #0162e8;
    }
    
    .pagination li a:hover {
        background-color: #f8f9fa;
        border-color: #0162e8;
        color: #0162e8;
    }
    
    .pagination .disabled span {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    /* تخصيص أزرار التالي والسابق */
    .pagination li:first-child a,
    .pagination li:last-child a {
        min-width: 80px;
    }
    
    .pagination li:first-child a::before {
        content: "السابق";
    }
    
    .pagination li:last-child a::before {
        content: "التالي";
    }
    
    .pagination li:first-child a span,
    .pagination li:last-child a span {
        display: none;
    }
    
    /* إخفاء الأسهم غير المرغوب فيها */
    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        border-radius: 4px !important;
    }
    
    /* تأثيرات النماذج */
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-2px);
    }
    
    .form-control {
        transition: all 0.3s ease;
    }
    
    .form-control:hover {
        border-color: #667eea;
    }
    
    .btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    /* تأثير الخلفية المتحركة */
    body {
        background: white;
    }
    
    /* تحسين الخلفية للصفحة */
    .container-fluid {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 25px;
        margin: 20px;
        padding: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    }
    
    /* تأثيرات التحميل والتفاعل */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* تأثيرات الدخول */
    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    body {
        background: white;
        min-height: 100vh;
    }
    
    /* تحسين الخلفية للصفحة */
    .container-fluid {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 25px;
        margin: 20px;
        padding: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    }
</style>
@endsection
@section('page-header')
<!-- breadcrumb -->

<!-- /breadcrumb -->
@endsection
@section('content')
<!-- row -->


<!-- row closed -->

<!-- row opened -->
<div class="row">
	<div class="col-xl-12">
		<div class="card" style="border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
			<div class="card-header pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 25px 25px 0 0; border: none;">
				<div class="d-flex justify-content-between align-items-center">
					<h4 class="card-title mg-b-0 text-white" style="font-weight: 600; font-size: 1.4rem;">
						<i class="fas fa-filter mr-2"></i>خيارات البحث والفلترة
					</h4>
					<i class="fas fa-sliders-h text-white opacity-75"></i>
				</div>
			</div>
			<div class="card-body" style="background: rgba(255,255,255,0.9); border-radius: 0 0 25px 25px;">
				<form method="POST" action="{{ route('index') }}">
					@csrf
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label for="month" class="font-weight-bold text-primary" style="font-size: 0.9rem;">
									<i class="fas fa-calendar-alt mr-1"></i>الشهر:
								</label>
								<select class="form-control" id="month" name="month" style="border-radius: 10px; border: 2px solid #e1e5e9; transition: all 0.3s ease;">
									<option value="1" {{ $month == 1 ? 'selected' : '' }}>كانون الثاني</option>
									<option value="2" {{ $month == 2 ? 'selected' : '' }}>شباط</option>
									<option value="3" {{ $month == 3 ? 'selected' : '' }}>آذار</option>
									<option value="4" {{ $month == 4 ? 'selected' : '' }}>نيسان</option>
									<option value="5" {{ $month == 5 ? 'selected' : '' }}>أيار</option>
									<option value="6" {{ $month == 6 ? 'selected' : '' }}>حزيران</option>
									<option value="7" {{ $month == 7 ? 'selected' : '' }}>تموز</option>
									<option value="8" {{ $month == 8 ? 'selected' : '' }}>آب</option>
									<option value="9" {{ $month == 9 ? 'selected' : '' }}>أيلول</option>
									<option value="10" {{ $month == 10 ? 'selected' : '' }}>تشرين الأول</option>
									<option value="11" {{ $month == 11 ? 'selected' : '' }}>تشرين الثاني</option>
									<option value="12" {{ $month == 12 ? 'selected' : '' }}>كانون الأول</option>
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label for="year" class="font-weight-bold text-primary" style="font-size: 0.9rem;">
									<i class="fas fa-calendar mr-1"></i>السنة:
								</label>
								<select class="form-control" id="year" name="year" style="border-radius: 10px; border: 2px solid #e1e5e9; transition: all 0.3s ease;">
									@for ($i = 2024; $i <= 2025; $i++)
										<option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
									@endfor
								</select>
							</div>
						</div>

						@if(in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name))
						<div class="col-md-2">
							<div class="form-group">
								<label for="moh_id" class="font-weight-bold text-primary" style="font-size: 0.9rem;">
									<i class="fas fa-map-marker-alt mr-1"></i>دائرة الصحة:
								</label>
								<select class="form-control" id="moh_id" name="moh_id" style="border-radius: 10px; border: 2px solid #e1e5e9; transition: all 0.3s ease;">
									<option value="">الكل</option>
									@foreach($mohs as $moh)
										<option value="{{ $moh->id }}" {{ $moh_id == $moh->id ? 'selected' : '' }}>{{ $moh->mohname }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label for="fctype_id" class="font-weight-bold text-primary" style="font-size: 0.9rem;">
									<i class="fas fa-building mr-1"></i>نوع المؤسسة:
								</label>
								<select class="form-control" id="fctype_id" name="fctype_id" style="border-radius: 10px; border: 2px solid #e1e5e9; transition: all 0.3s ease;">
									<option value="">الكل</option>
									@foreach($fctypes as $fctype)
										<option value="{{ $fctype->id }}" {{ $fctype_id == $fctype->id ? 'selected' : '' }}>{{ $fctype->Fname }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="fck_id" class="font-weight-bold text-primary" style="font-size: 0.9rem;">
									<i class="fas fa-hospital mr-1"></i>اسم المؤسسة:
								</label>
								<select class="form-control" id="fck_id" name="fck_id" style="border-radius: 10px; border: 2px solid #e1e5e9; transition: all 0.3s ease;">
									<option value="">الكل</option>
									@foreach($fcks as $fck)
										<option value="{{ $fck->id }}" data-moh="{{ $fck->moh_id }}" data-fctype="{{ $fck->fctypesid }}" {{ $fck_id == $fck->id ? 'selected' : '' }}>{{ $fck->Fckname }}</option>
									@endforeach
								</select>
							</div>
						</div>
						@endif

						<div class="col-md-1 d-flex align-items-end">
							<div class="form-group mb-0 d-flex" style="gap: 10px; width: 100%;">
								<button class="btn btn-primary" type="submit" style="flex: 1; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
									<i class="fas fa-search"></i> بحث
								</button>
								<button class="btn btn-secondary" type="button" onclick="resetFilters()" style="flex: 1; border-radius: 10px; font-weight: 600; transition: all 0.3s ease;">
									<i class="fas fa-redo"></i> إعادة
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<h2 class="text-center mt-4 mb-5" style="font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 4px 8px rgba(0,0,0,0.1);">
			<i class="fas fa-chart-bar mr-3"></i>مؤشرات شهر {{ $monthName }} سنة {{ $year }}
		</h2>
	</div>
</div>
<BR>
<div>
	@if(isset($labels1) && isset($chart_data1))
	<div class="row row-sm">
		<div class="col-xl-6">
			<div class="card mg-b-20">
				<div class="card-body">
					<h6 class="card-title mb-3">إحصائيات الأسرة حسب الاختصاص</h6>
                    <div class="d-flex justify-content-end mb-3 no-print" style="gap: 10px;">
                        <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </button>
                        <button onclick="exportToPDF()" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> تصدير PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                    </div>
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <table class="table table-stats table-striped table-hover">
                                <thead class="thead-primary">
                                    <tr>
                                        <th class="th-lg">الاختصاص</th>
                                        <th class="text-center th-md">عدد الأسرة</th>
                                        <th class="text-center th-sm">النسبة المئوية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paginatedData as $item)
                                    @php
                                        $percentage = (strpos($item['name'], 'كلية') !== false || strpos($item['name'], 'ثلاسيميا') !== false || strpos($item['name'], 'أورام') !== false) 
                                            ? (($item['count'] / (5 * $daysInMonth * ($item['bed_count'] ?: 1))) * 100) 
                                            : (($item['count'] / ($total ?: 1)) * 100);
                                        
                                        $rowClass = '';
                                        $barColor = '';
                                        $barBg = '';
                                        if ($percentage >= 75) {
                                            $rowClass = '';
                                            $barColor = 'bg-success';
                                            $barBg = 'linear-gradient(90deg, #22c55e, #16a34a)';
                                        } elseif ($percentage >= 50) {
                                            $rowClass = '';
                                            $barColor = 'bg-warning';
                                            $barBg = 'linear-gradient(90deg, #fbbf24, #f59e0b)';
                                        } elseif ($percentage >= 25) {
                                            $rowClass = '';
                                            $barColor = 'bg-info';
                                            $barBg = 'linear-gradient(90deg, #3b82f6, #2563eb)';
                                        } else {
                                            $rowClass = '';
                                            $barColor = 'bg-danger';
                                            $barBg = 'linear-gradient(90deg, #ef4444, #dc2626)';
                                        }
                                    @endphp
                                    <tr class="{{ $rowClass }}" data-toggle="tooltip" title="الاختصاص: {{ $item['name'] }} - النسبة: {{ number_format($percentage, 1) }}%">
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="stats-value text-center">{{ number_format($item['count']) }}</td>
                                        <td class="text-center percentage-cell">
                                            <strong>{{ number_format($percentage, 1) }}%</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td><strong>المجموع الكلي</strong></td>
                                        <td class="stats-value text-center"><strong>{{ number_format($total) }}</strong></td>
                                        <td class="text-center"><strong>100%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $paginatedData->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<div class="col-xl-6">
			<div class="card mg-b-20">
				<div class="card-body">
					<h6 class="card-title mb-3">إحصائيات العمليات الجراحية</h6>
                    <div class="d-flex justify-content-end mb-3 no-print" style="gap: 10px;">
                        <button onclick="exportSurgeryToExcel()" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </button>
                        <button onclick="exportSurgeryToPDF()" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> تصدير PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                    </div>
                    <div class="table-responsive">
                        <div class="table-wrapper">
                            <table class="table table-stats table-striped table-hover">
                                <thead class="thead-primary">
                                    <tr>
                                        <th class="th-lg">نوع العملية الجراحية</th>
                                        <th class="text-center th-md">عدد العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $surgeryTotal = isset($surgeryData) && is_array($surgeryData) ? array_sum(array_column($surgeryData, 'count')) : 0;
                                    @endphp
                                    @if(isset($surgeryData) && is_array($surgeryData))
                                    @foreach($surgeryData as $item)
                                    <tr data-toggle="tooltip" title="العملية: {{ $item['name'] }}">
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="stats-value text-center">{{ number_format($item['count']) }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="2" class="text-center">لا توجد بيانات متاحة للعمليات الجراحية</td>
                                    </tr>
                                    @endif
                                    <tr class="total-row">
                                        <td><strong>المجموع الكلي</strong></td>
                                        <td class="stats-value text-center"><strong>{{ number_format($surgeryTotal) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	@endif
</div>

</div>
</div>
</div>



<style>
	#calendar-container {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
		/* يضمن أن يكون التقويم في منتصف الشاشة */
	}

	#calendar {
		max-width: 800px;
		/* تحديد عرض التقويم */
		width: 100%;
	}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'listYear', // عرض كل الأشهر دفعة واحدة
        height: 'auto', // ضبط ارتفاع التقويم
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'listYear'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/getCalendarData?year=' + fetchInfo.start.getFullYear())
                .then(response => response.json())
                .then(data => {
                    console.log("📌 البيانات المسترجعة:", data);
                    let events = data.map(inst => ({
                        title: inst.entry_exists ? inst.Fckname : inst.Fckname + " (غير مدخل)",
                        color: inst.entry_exists ? '#28a745' : '#dc3545',
                        start: `${fetchInfo.start.getFullYear()}-${inst.month}-01` // بداية كل شهر فقط
                    }));
                    successCallback(events);
                }).catch(error => {
                    console.error("⚠️ فشل تحميل البيانات!", error);
                });
        }
    });
    calendar.render();
});
س
</script>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</div>
<!-- Container closed -->
@endsection
@section('js')
<script type="text/javascript">
	function printDiv() {
		var printContents = document.getElementById('print').innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
		location.reload();
	}
</script>

<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!--Internal  Flot js-->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
<!--Internal Apexchart js-->
<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
<!-- Internal Map -->
<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
<script>
// Store all institutions data for filtering
var allInstitutions = @json($fcks ?? []);

// Filter institutions based on selected department and type
function filterInstitutions() {
    var mohId = $('#moh_id').val();
    var fctypeId = $('#fctype_id').val();
    var fckSelect = $('#fck_id');
    
    // Clear and rebuild options
    fckSelect.empty();
    fckSelect.append('<option value="">الكل</option>');
    
    // Filter institutions based on both department and type
    var filteredInstitutions = allInstitutions.filter(function(fck) {
        var mohMatch = (!mohId || mohId === '' || fck.moh_id == mohId);
        var typeMatch = (!fctypeId || fctypeId === '' || fck.fctypesid == fctypeId);
        return mohMatch && typeMatch;
    });
    
    filteredInstitutions.forEach(function(fck) {
        fckSelect.append('<option value="' + fck.id + '" data-moh="' + fck.moh_id + '" data-fctype="' + fck.fctypesid + '">' + fck.Fckname + '</option>');
    });
}

// Reset filters function
function resetFilters() {
    $('#month').val({{ date('n') }});
    $('#year').val({{ date('Y') }});
    @if(in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name))
    $('#moh_id').val('');
    $('#fctype_id').val('');
    $('#fck_id').val('');
    filterInstitutions();
    @endif
}

// Export to Excel function
function exportToExcel() {
    alert('جاري تصدير البيانات إلى Excel...');
    // يمكن إضافة كود التصدير الفعلي هنا
}

// Export to PDF function
function exportToPDF() {
    alert('جاري تصدير البيانات إلى PDF...');
    // يمكن إضافة كود التصدير الفعلي هنا
}

// Export Surgery to Excel function
function exportSurgeryToExcel() {
    alert('جاري تصدير بيانات العمليات الجراحية إلى Excel...');
    // يمكن إضافة كود التصدير الفعلي هنا
}

// Export Surgery to PDF function
function exportSurgeryToPDF() {
    alert('جاري تصدير بيانات العمليات الجراحية إلى PDF...');
    // يمكن إضافة كود التصدير الفعلي هنا
}

// Show loading spinner on form submit
function showLoading() {
    var loadingHtml = '<div id="loadingSpinner" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center;">' +
        '<div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">' +
        '<span class="sr-only">جاري التحميل...</span>' +
        '</div>' +
        '</div>';
    $('body').append(loadingHtml);
}

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize filtering on page load
    @if(in_array("Admin", Auth::user()->roles_name) || in_array("mohst", Auth::user()->roles_name))
    $('#moh_id, #fctype_id').on('change', function() {
        filterInstitutions();
    });
    
    // Filter institutions if values are pre-selected
    if ($('#moh_id').val() || $('#fctype_id').val()) {
        filterInstitutions();
    }
    @endif
    
    // Show loading on form submit
    $('form').on('submit', function() {
        showLoading();
    });
    
    // Animate progress bars on page load
    $('.progress-bar').each(function() {
        var width = $(this).css('width');
        $(this).css('width', '0');
        $(this).animate({width: width}, 1000);
    });
    
    // Add loading animation to buttons
    $('.btn').on('click', function() {
        var $btn = $(this);
        $btn.addClass('btn-loading');
        setTimeout(function() {
            $btn.removeClass('btn-loading');
        }, 1000);
    });
    
    // Smooth scroll animations
    $(window).on('scroll', function() {
        $('.card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-in');
            }
        });
    });
    
    // Add entrance animations
    $('.card').addClass('animate-in');
    
    // Add loading animation to buttons
    $('.btn').on('click', function() {
        var $btn = $(this);
        $btn.addClass('btn-loading');
        setTimeout(function() {
            $btn.removeClass('btn-loading');
        }, 1000);
    });
    
    // Smooth scroll animations
    $(window).on('scroll', function() {
        $('.card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-in');
            }
        });
    });
    
    // Add entrance animations
    $('.card').addClass('animate-in');
});

document.addEventListener('DOMContentLoaded', function() {
    // تكوين الخيارات العامة للرسوم البيانية
    Chart.defaults.font.family = 'Cairo';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#333';
    
    let charts = document.querySelectorAll('canvas');
    charts.forEach(canvas => {
        let chartInstance = Chart.getChart(canvas);
        if (chartInstance && chartInstance.data && chartInstance.data.datasets && chartInstance.data.datasets.length > 0) {
            // تحديث خيارات المخطط
            chartInstance.options = {
                ...chartInstance.options,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 10, // تصغير حجم الخط في وضع الطباعة
                                family: 'Cairo'
                            },
                            boxWidth: 10, // تقليل حجم مربع الألوان
                            padding: 10
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            // إظهار القيم فقط إذا كانت أكبر من الصفر
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 10 // تصغير حجم القيم
                        },
                        formatter: (value) => {
                            if (value === 0) return '';
                            return value.toLocaleString('ar-SA');
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        ticks: {
                            font: {
                                size: 10, // تصغير حجم النص على المحور السيني
                                family: 'Cairo'
                            },
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: false // عدم تخطي أي تسمية
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10, // تصغير حجم النص على المحور الصادي
                                family: 'Cairo'
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 20,
                        bottom: 10
                    }
                }
            };
            
            // إضافة مستمع لحدث الطباعة
            window.addEventListener('beforeprint', function() {
                chartInstance.resize();
            });
            
            // تحديث المخطط
            chartInstance.update();
        }
    });
});
</script>
@endsection